<?php

namespace CalendarPlugin\src\classes\forms;

use CalendarPlugin\src\classes\models\ActivityModel;

abstract class CalendarForm
{
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
}