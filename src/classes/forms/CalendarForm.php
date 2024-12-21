<?php

namespace CalendarPlugin\src\classes\forms;

use CalendarPlugin\src\classes\models\ActivityModel;
use CalendarPlugin\src\classes\services\LanguageService;
use CalendarPlugin\src\classes\services\ReservationService;

abstract class CalendarForm
{
    protected $reservationService;
    protected $langService;
    protected $datesOnThisWeek;

    public function __construct()
    {
        $this->reservationService = new ReservationService();
        $this->langService = new LanguageService;
        $this->datesOnThisWeek = [];
    }

    /**
     * Get all activities models from DB
     * 
     * @return array
     */
    protected function get_all_activities_models($shortCode) {
        global $wpdb;

        $toReturn = [];
        $rows = $wpdb->get_results( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name LIKE %s", '_calendar_plugin_data|activity_hidden_id%' ), ARRAY_A );
        if($shortCode === null || count($shortCode) < 1) {
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
                foreach($shortCode as $code) {
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

    /**
     * Create cell with data
     * 
     * @param object|null calendar
     * @param object|null activity
     * @param string currentTime
     * @param string|int day
     * @param string|null oneDayId
     * @return void
     */
    protected function get_cell_with_activity($calendar, $activity, $currentTime, $day, $oneDayId = null) {
        $id = $activity->get_hidden_id() . "_" . $currentTime . "_" . $day . $oneDayId;
        $limit = $this->reservationService->check_reservation_limit($activity->get_hidden_id(), $this->datesOnThisWeek[$day - 1]);

        $class = $this->set_fulent_background_class($calendar, $activity->get_bg_color(), $activity->get_hidden_id());

        if($limit >= $activity->get_slot() || $calendar->get_calendar_reservation() === false) {
            echo "<div data-info='" . $activity->get_start_at() . "|" . $activity->get_end_at() . "|" . $activity->get_duration() . "' class='calendar-event cursor-default $class' id='$id' style='background-color: " . htmlspecialchars($activity->get_bg_color()) . ";'>";
        }
        else {
            echo "<div data-info='" . $activity->get_start_at() . "|" . $activity->get_end_at() . "|" . $activity->get_duration() . "' class='calendar-event cursor-pointer $class' id='$id' style='background-color: " . htmlspecialchars($activity->get_bg_color()) . ";'>";
        }
        
        if($calendar->get_duration_on_grid() === true) {
            echo "<span>" . htmlspecialchars($activity->get_duration()) . " min</span>";
        }
        echo "<p class='text-wrap' style='font-weight: bold;'>" . htmlspecialchars($activity->get_name()) . "</p>";
        if($calendar->get_place_activity_on_grid() === true) {
            echo "<p class='text-wrap'>" . htmlspecialchars($activity->get_type()) . "</p>";
        }

        if($calendar->get_start_time_on_grid() === true && $calendar->get_end_time_on_grid() === true) {
            echo "<p>" . $this->langService->calendarLabels['label_activity_from'] . " " . htmlspecialchars($activity->get_start_at()) . " "
                . $this->langService->calendarLabels['label_activity_to'] . " " . htmlspecialchars($activity->get_end_at()) . "</p>";
        }

        if($calendar->get_start_time_on_grid() === true && $calendar->get_end_time_on_grid() === false) {
            echo "<p>" . $this->langService->calendarLabels['label_activity_start_at'] . " " . htmlspecialchars($activity->get_start_at()) . "</p>";
        }
        
        if($calendar->get_start_time_on_grid() === false && $calendar->get_end_time_on_grid() === true) {
            echo "<p>" . $this->langService->calendarLabels['label_activity_end_at'] . " " . htmlspecialchars($activity->get_end_at()) . "</p>";
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
    protected function set_fulent_background_class($calendar , $color, $classId) {
        if($calendar->get_fluent_calendar_grid() === false) {
            return null;
        }
        $className = "abc$classId-" . $this->generate_random_string(12);

        if($calendar->get_horizontal_calendar_grid() === true) {
            $whClassParam = "height: 100%; width: var(--after-height); top: 0%;";
        }
        else {
            $whClassParam = "width: 100%; height: var(--after-height); top: 100%;";
        }

        echo "<style>";
        echo '.' . $className .' {
            --after-height: 0px;
        }';
        echo '.' . $className .'::after {
                content: "";
                position: absolute;
                left: 0;'
                . $whClassParam .
                ' background-color:' . $color . ';
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
}