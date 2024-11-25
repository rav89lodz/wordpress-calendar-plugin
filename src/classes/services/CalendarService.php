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

    public function __construct($monthNumber = null, $startDate = null, $shortCode = null)
    {
        $this->calendar = new CalendarModel($monthNumber, $startDate);
        $this->datesOnThisWeek = [];
        $this->showLeftArrows = $this->set_show_left_arrows($monthNumber, $startDate);
        $this->service = new ReservationService;
        $this->shortCode = $shortCode;
    }

    public function get_all_activities_models() {
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

    public function create_table_header() {
        $days = ["Poniedziałek", "Wtorek", "Środa", "Czwartek", "Piątek", "Sobota", "Niedziela"];
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

    public function create_table_content() {
        $activities = $this->get_all_activities_models();

        $current = strtotime($this->calendar->get_first_hour_on_calendar());
        $end_time = strtotime($this->calendar->get_last_hour_on_calendar());
        $interval = "+60 minutes";

        while ($current <= $end_time) {
            $activitiesGrouped = $this->group_activities_by_day_and_hour($activities, date('H:i', $current));

            $this->print_row_with_data($activitiesGrouped, date('H:i', $current));

            for($min = 5; $min < 56; $min += 5) {
                $minute = date('H:i', strtotime("+$min minutes", $current));
                if (isset($activitiesGrouped[$minute])) {
                    $this->print_row_with_data($activitiesGrouped, $minute);
                }
            }

            $current = strtotime($interval, $current);
        }
    }

    private function group_activities_by_day_and_hour($activities, $currentTime) {
        $groupedActivities = [];
    
        foreach ($activities as $activity) {
            if ($activity->get_is_cyclic() == '1') {
                foreach ($activity->get_day() as $dayOfWeek) {
                    $groupedActivities = $this->attach_activiti_to_array($groupedActivities, $activity, $currentTime, $dayOfWeek);
                }
            }
            else {
                if (in_array($activity->get_date(), $this->datesOnThisWeek)) {
                    $dayOfWeek = date('N', strtotime($activity->get_date()));
                    $groupedActivities = $this->attach_activiti_to_array($groupedActivities, $activity, $currentTime, $dayOfWeek);
                }
            }
        }
    
        return $groupedActivities;
    }

    private function attach_activiti_to_array($groupedActivities, $activity, $currentTime, $dayOfWeek) {
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

    private function print_row_with_data($groupedActivities, $currentTime) {
        echo "<tr>";
        echo "<td>" . $currentTime . "</td>";
    
        for ($day = 1; $day <= 7; $day++) {
            if (!empty($groupedActivities[$currentTime][$day])) {
                echo "<td>";
                foreach ($groupedActivities[$currentTime][$day] as $activity) {
                    $this->get_row_with_activity($activity, $currentTime, $day);
                }
                echo "</td>";
            }
            else {
                echo "<td></td>";
            }
        }
        echo "</tr>";
    }

    private function get_row_with_activity($activity, $currentTime, $day) {
        $id = $activity->get_hidden_id() . "_" . $currentTime . "_" . $day;
        $limit = $this->service->check_reservation_limit($activity->get_hidden_id(), $this->datesOnThisWeek[$day - 1]);
        if($limit >= $activity->get_slot()) {
            echo "<div class='limit-over' id='$id' style='background-color: " . htmlspecialchars($activity->get_bg_color()) . ";'>";
        }
        else {
            echo "<div class='calendar-event' id='$id' style='background-color: " . htmlspecialchars($activity->get_bg_color()) . ";'>";
        }
        echo "<span>" . htmlspecialchars($activity->get_duration()) . " min</span>";
        echo "<p class='text-wrap' style='font-weight: bold;'>" . htmlspecialchars($activity->get_name()) . "</p>";
        echo "<p class='text-wrap'>" . htmlspecialchars($activity->get_type()) . "</p>";
        echo "</div>";
    }

    private function print_header_row($dayName, $date, $id) {
        $id = "header_$id";
        echo "<td><p>" . $dayName . "</p><span id='$id'>" . $date . "</span></td>";
    }

    private function set_show_left_arrows($month, $startDate) {
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
}