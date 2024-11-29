<?php

namespace CalendarPlugin\src\classes\models;

class ActivityModel
{
    private $hiddenID;
    private $name;
    private $startAt;
    private $endAt;
    private $duration;
    private $isCyclic;
    private $date;
    private $day;
    private $bgColor;
    private $type;
    private $rawType;
    private $slot;

    /**
     * Constructor
     * 
     * @param string id
     * @return void
     */
    public function __construct($id) {
        $this->set_activity_model_data_by_id($id);
    }

    /**
     * Get hiddenID
     * 
     * @return string|null
     */
    public function get_hidden_id() {
        return $this->hiddenID;
    }

    /**
     * Get name
     * 
     * @return string|null
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * Get startAt
     * 
     * @return string|null
     */
    public function get_start_at() {
        return $this->startAt;
    }

    /**
     * Get endAt
     * 
     * @return string|null
     */
    public function get_end_at() {
        return $this->endAt;
    }

    /**
     * Get duration
     * 
     * @return string|null
     */
    public function get_duration() {
        return $this->duration;
    }

    /**
     * Get isCyclic
     * 
     * @return string|null
     */
    public function get_is_cyclic() {
        return $this->isCyclic;
    }

    /**
     * Get date
     * 
     * @return string|null
     */
    public function get_date() {
        return $this->date;
    }

    /**
     * Get day
     * 
     * @return string|null
     */
    public function get_day() {
        return $this->day;
    }

    /**
     * Get bgColor
     * 
     * @return string|null
     */
    public function get_bg_color() {
        return $this->bgColor;
    }

    /**
     * Get rawType
     * 
     * @return string|null
     */
    public function get_raw_type() {
        return $this->rawType;
    }

    /**
     * Get type
     * 
     * @return string|null
     */
    public function get_type() {
        return $this->type;
    }

    /**
     * Get slot
     * 
     * @return string|null
     */
    public function get_slot() {
        return $this->slot;
    }

    /**
     * Get array of startAt data
     * 
     * @return array|null
     */
    public function get_hour_and_minutes_form_start_at() {
        return explode(':', $this->startAt);
    }

    /**
     * Set activity model data by id
     * 
     * @param string id
     * @return void
     */
    private function set_activity_model_data_by_id($id) {
        $this->day = [];
        foreach($this->get_activity_model_data_by_id($id) as $row) {
            switch(true) {
                case str_contains($row['option_name'], "hidden_id"):
                    $this->hiddenID = $row['option_value'];
                    break;
                case str_contains($row['option_name'], "activity_name"):
                    $this->name = $row['option_value'];
                    break;
                case str_contains($row['option_name'], "start_at"):
                    $this->startAt = $row['option_value'];
                    break;
                case str_contains($row['option_name'], "end_at"):
                    $this->endAt = $row['option_value'];
                    break;
                case str_contains($row['option_name'], "cyclic"):
                    $this->isCyclic = $row['option_value'];
                    break;
                case str_contains($row['option_name'], "activity_date"):
                    $this->date = $row['option_value'];
                    break;
                case str_contains($row['option_name'], "activity_day"):
                    $this->day[] = $row['option_value'];
                    break;
                case str_contains($row['option_name'], "bg_color"):
                    $this->bgColor = $row['option_value'];
                    break;
                case str_contains($row['option_name'], "activity_type"):
                    $this->rawType = $row['option_value'];
                    $this->type = $this->get_activity_type($row['option_value']);
                    break;
                case str_contains($row['option_name'], "activity_slot"):
                    $this->slot = $row['option_value'];
                    break;
                default:
                    return null;
            }
        }
        $this->duration = $this->set_duration();
    }

    /**
     * Calculate duration time between start and end
     * 
     * @return int
     */
    private function set_duration() {
        $time1Parts = explode(":", $this->endAt);
        $time2Parts = explode(":", $this->startAt);
        
        $time1Minutes = (int)$time1Parts[0] * 60 + (int)$time1Parts[1];
        $time2Minutes = (int)$time2Parts[0] * 60 + (int)$time2Parts[1];

        $difference = $time1Minutes - $time2Minutes;
        
        if ($difference < 0) {
            $difference += 1440;
        }

        return $difference;
    }

    /**
     * Get activity type from DB
     * 
     * @param string type
     * @return string
     */
    private function get_activity_type($type) {
        global $wpdb;

        $obj = $wpdb->get_row( $wpdb->prepare( "SELECT option_name FROM $wpdb->options WHERE option_value = %s AND option_name LIKE %s", [$type, "_calendar_plugin_types|type_key%"]));
        if($obj !== null) {
            $optionName = str_replace('type_key','type_name', $obj->option_name);
            $obj2 = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = %s", $optionName ));
            if($obj2 !== null) {
                return $obj2->option_value;
            }
        }
        return $type;
    }

    /**
     * Get activity model data form DB by passed id
     * 
     * @param string id
     * @return array|null
     */
    private function get_activity_model_data_by_id($id) {
        global $wpdb;

        $obj = $wpdb->get_row( $wpdb->prepare( "SELECT option_name FROM $wpdb->options WHERE option_value = %s", $id ));
        if($obj !== null) {
            $secondParam = str_replace("_calendar_plugin_data|activity_hidden_id", "", $obj->option_name);
            $secondParam = str_replace("|0|value", "|%|value", $secondParam);
            return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->options WHERE option_name LIKE '_calendar_plugin_data%' AND option_name LIKE %s", '%' . $secondParam ), ARRAY_A );
        }
    }
}