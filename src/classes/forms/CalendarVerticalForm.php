<?php

namespace CalendarPlugin\src\classes\forms;

use CalendarPlugin\src\classes\models\ActivityModel;
use CalendarPlugin\src\classes\services\LanguageService;
use CalendarPlugin\src\classes\services\ReservationService;

class CalendarVerticalForm extends CalendarForm
{
    private $calendar;
    private $datesOnThisWeek;
    private $shortCode;
    private $service;
    private $langService;

    /**
     * Constructor
     *
     * @param object|null calendar
     * @param mixed shortCode
     * @return void
     */
    public function __construct($calendar = null, $shortCode = null )
    {
        $this->calendar = $calendar;
        $this->datesOnThisWeek = [];
        $this->service = new ReservationService;
        $this->langService = new LanguageService;
        $this->shortCode = $shortCode;
    }

    /**
     * Create table header
     * 
     * @return void
     */
    public function create_vertical_table_header() {
        $days = $this->langService->days;
        $currentDay = $this->calendar->get_cuttent_monday_date();

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

    /**
     * Create table content
     * 
     * @return void
     */
    public function create_vertical_table_content() {
        $activities = $this->get_all_activities_models($this->shortCode);

        $endTime = strtotime($this->calendar->get_last_hour_on_calendar());
        $interval = "+" . $this->calendar->get_calendar_interval() . " minutes";
        
        $currentTime = strtotime($this->calendar->get_first_hour_on_calendar());
        while ($currentTime <= $endTime) {
            $activitiesGrouped = $this->group_activities_by_day_and_hour($activities, date('H:i', $currentTime));

            $this->print_row_with_data($activitiesGrouped, date('H:i', $currentTime));

            for($min = 5; $min < $this->calendar->get_calendar_interval() - 4; $min += 5) {
                $minute = date('H:i', strtotime("+$min minutes", $currentTime));
                if (isset($activitiesGrouped[$minute])) {
                    $this->print_row_with_data($activitiesGrouped, $minute);
                }
            }

            $currentTime = strtotime($interval, $currentTime);
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

        $class = $this->set_fulent_background_class($this->calendar, $activity->get_bg_color(), $activity->get_hidden_id());

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

    
}