<?php

namespace CalendarPlugin\src\classes\services;

use CalendarPlugin\src\classes\models\ActivityModel;
use CalendarPlugin\src\classes\models\CalendarModel;

class CalendarService
{
    public $calendar;
    public $showLeftArrows;
    private $datesOnThisWeek;
    private $service;
    private $shortCode;
    private $langService;

    /**
     * Constructor
     *
     * @param string|int monthNumber
     * @param string startDate
     * @param mixed shortCode
     * @return void
     */
    public function __construct($monthNumber = null, $startDate = null, $shortCode = null)
    {
        $this->calendar = new CalendarModel($monthNumber, $startDate);
        $this->datesOnThisWeek = [];
        $this->showLeftArrows = $this->show_left_arrows($monthNumber, $startDate);
        $this->service = new ReservationService;
        $this->langService = new LanguageService;
        $this->shortCode = $shortCode;
    }

    /**
     * Create table header
     * 
     * @return void
     */
    public function create_table_header() {
        $days = $this->langService->days;
        $currentDay = $this->calendar->get_cuttent_monday_date();

        if($this->calendar->get_horizontal_calendar_grid() === true) {

        }
        else {
            echo '<th scope="col">';
            for($i = 0; $i < 7; $i ++) {
                if($i > 0) {
                    $currentDay = $this->calendar->get_monday_plus_days($i);
                }
                $this->print_header_row($days[$i], $currentDay, $i + 1);
                $this->datesOnThisWeek[] = $currentDay;
            }
            echo "</th>";
        }
    }

    /**
     * Create table content
     * 
     * @return void
     */
    public function create_table_content() {
        $activities = $this->get_all_activities_models();

        $current = strtotime($this->calendar->get_first_hour_on_calendar());
        $end_time = strtotime($this->calendar->get_last_hour_on_calendar());
        $interval = "+" . $this->calendar->get_calendar_interval() . " minutes";

        while ($current <= $end_time) {
            $activitiesGrouped = $this->group_activities_by_day_and_hour($activities, date('H:i', $current));

            $this->print_row_with_data($activitiesGrouped, date('H:i', $current));

            for($min = 5; $min < $this->calendar->get_calendar_interval() - 4; $min += 5) {
                $minute = date('H:i', strtotime("+$min minutes", $current));
                if (isset($activitiesGrouped[$minute])) {
                    $this->print_row_with_data($activitiesGrouped, $minute);
                }
            }

            $current = strtotime($interval, $current);
        }
    }

    /**
     * Group activities by day and hour
     * 
     * @param array activities
     * @param string currentTime
     * @return array
     */
    private function group_activities_by_day_and_hour($activities, $currentTime) {
        $groupedActivities = [];
    
        foreach ($activities as $activity) {
            if ($activity->get_is_cyclic() == '1') {
                foreach ($activity->get_day() as $dayOfWeek) {
                    $groupedActivities = $this->attach_activity_to_array($groupedActivities, $activity, $currentTime, $dayOfWeek);
                }
            }
            else {
                if (in_array($activity->get_date(), $this->datesOnThisWeek)) {
                    $dayOfWeek = date('N', strtotime($activity->get_date()));
                    $groupedActivities = $this->attach_activity_to_array($groupedActivities, $activity, $currentTime, $dayOfWeek);
                }
            }
        }
    
        return $groupedActivities;
    }

    /**
     * Attach single activity to passed array on specific position
     * 
     * @param array groupedActivities
     * @param object|null activity
     * @param string currentTime
     * @param string|int dayOfWeek
     * @return array
     */
    private function attach_activity_to_array($groupedActivities, $activity, $currentTime, $dayOfWeek) {
        if ($activity->get_start_at() == $currentTime) {
            $groupedActivities[$currentTime][$dayOfWeek][] = $activity;
        }
        else {
            $minuteHour = date('H:i', strtotime($activity->get_start_at()));
            if (!isset($groupedActivities[$minuteHour])) {
                $groupedActivities[$minuteHour] = [];
            }
            $groupedActivities[$minuteHour][$dayOfWeek][] = $activity;
        }
        return $groupedActivities;
    }

    /**
     * Print table row with data
     * 
     * @param array groupedActivities
     * @param string currentTime
     * @return void
     */
    private function print_row_with_data($groupedActivities, $currentTime) {
        echo "<tr>";
        echo "<td>" . $currentTime . "</td>";
    
        for ($day = 1; $day <= 7; $day++) {
            if (!empty($groupedActivities[$currentTime][$day])) {

                if($this->calendar->get_fluent_calendar_grid() === false) {
                    echo "<td>";
                }
                else {
                    echo "<td><div class='flex-cell'>";
                }
                
                foreach ($groupedActivities[$currentTime][$day] as $activity) {
                    $this->get_cell_with_activity($activity, $currentTime, $day);
                }
                if($this->calendar->get_fluent_calendar_grid() === false) {
                    echo "</td>";
                }
                else {
                    echo "</div></td>";
                }
            }
            else {
                echo "<td></td>";
            }
        }
        echo "</tr>";
    }

    /**
     * Create cell with data
     * 
     * @param object|null activity
     * @param string currentTime
     * @param string|int day
     * @return void
     */
    private function get_cell_with_activity($activity, $currentTime, $day) {
        $id = $activity->get_hidden_id() . "_" . $currentTime . "_" . $day;
        $limit = $this->service->check_reservation_limit($activity->get_hidden_id(), $this->datesOnThisWeek[$day - 1]);

        $class = $this->set_fulent_background_class($activity->get_bg_color(), $activity->get_hidden_id());

        if($limit >= $activity->get_slot() || $this->calendar->get_calendar_reservation() === false) {
            echo "<div data-info='" . $activity->get_start_at() . "|" . $activity->get_end_at() . "|" . $activity->get_duration() . "' class='calendar-event cursor-default $class' id='$id' style='background-color: " . htmlspecialchars($activity->get_bg_color()) . ";'>";
        }
        else {
            echo "<div data-info='" . $activity->get_start_at() . "|" . $activity->get_end_at() . "|" . $activity->get_duration() . "' class='calendar-event cursor-pointer $class' id='$id' style='background-color: " . htmlspecialchars($activity->get_bg_color()) . ";'>";
        }
        
        if($this->calendar->get_duration_on_grid() === true) {
            echo "<span>" . htmlspecialchars($activity->get_duration()) . " min</span>";
        }
        echo "<p class='text-wrap' style='font-weight: bold;'>" . htmlspecialchars($activity->get_name()) . "</p>";
        if($this->calendar->get_place_activity_on_grid() === true) {
            echo "<p class='text-wrap'>" . htmlspecialchars($activity->get_type()) . "</p>";
        }
        
        if($this->calendar->get_end_time_on_grid() === true) {
            echo "<p>" . $this->langService->calendarLabels['label_activity_end_at'] . htmlspecialchars($activity->get_end_at()) . "</p>";
        }
        echo "</div>";
    }

    /**
     * Create css class for fluent background option
     * 
     * @param string color
     * @param string classId
     * @param string height
     * @return string
     */
    private function set_fulent_background_class($color, $classId) {
        if($this->calendar->get_fluent_calendar_grid() === false) {
            return null;
        }
        $className = "abc$classId-" . $this->generate_random_string(12);
        echo "<style>";
        echo '.' . $className .' {
            --after-height: 0px;
        }';
        echo '.' . $className .'::after {
                content: "";
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                height: var(--after-height);
                background-color:' . $color . ';
                z-index: -1;
            }';
        echo "</style>";
        return $className;
    }

    /**
     * Generate random string
     * 
     * @param int length
     * @return string
     */
    private function generate_random_string($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
    
        return $randomString;
    }

    /**
     * Print header row
     * 
     * @param string dayName
     * @param string date
     * @param string id
     * @return void
     */
    private function print_header_row($dayName, $date, $id) {
        $id = "header_$id";
        echo "<td><p>" . $dayName . "</p><span id='$id'>" . $date . "</span></td>";
    }

    /**
     * Check left arrows can be shown
     * 
     * @param string month
     * @param string startDate
     * @return array
     */
    private function show_left_arrows($month, $startDate) {
        if($month !== null) {
            if(date('Y-m', strtotime($month)) > date('Y-m')) {
                return [true, true];
            }
        }
        if($startDate !== null && $startDate > date('Y-m-d')) {
            if(date('Y-m', strtotime($startDate)) > date('Y-m')) {
                return [true, true];
            }
            return [false, true];
        }
        return [false, false];
    }

    /**
     * Get all activities models from DB
     * 
     * @return array
     */
    private function get_all_activities_models() {
        global $wpdb;

        $toReturn = [];
        $rows = $wpdb->get_results( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name LIKE %s", '_calendar_plugin_data|activity_hidden_id%' ), ARRAY_A );
        if($this->shortCode === null || count($this->shortCode) < 1) {
            foreach($rows as $row) {
                $activity = new ActivityModel($row['option_value']);
                $date = empty($activity->get_date()) ? 'empty' : $activity->get_date();
                $time = str_contains($activity->get_start_at(), ":") ? $activity->get_start_at() : "30:80";
                $key = $date . "#" . $time . "#" . $activity->get_hidden_id();
                $toReturn[$key] = $activity;
            }
        }
        else {
            foreach($rows as $row) {
                $skip = true;
                $activity = new ActivityModel($row['option_value']);
                foreach($this->shortCode as $code) {
                    if($activity->get_raw_type() == $code){
                        $skip = false;
                        break;
                    }
                }
                if($skip == true) {
                    continue;
                }
                
                $date = empty($activity->get_date()) ? 'empty' : $activity->get_date();
                $time = str_contains($activity->get_start_at(), ":") ? $activity->get_start_at() : "30:80";
                $key = $date . "#" . $time . "#" . $activity->get_hidden_id();
                $toReturn[$key] = $activity;
            }
        }

        return $toReturn;
    }
}