<?php

namespace CalendarPlugin\src\classes\models;

use CalendarPlugin\src\classes\FormValidator;

class AddActivityModel
{
    private $activityUserName;
    private $activityUserEmail;
    private $activityUserPhone;
    private $activityDate;
    private $activityTimeStart;
    private $activityTimeEnd;
    private $activityName;

    public function __construct($data = null) {
        $this->activityUserName = null;
        $this->activityUserEmail = null;
        $this->activityUserPhone = null;
        $this->activityDate = null;
        $this->activityTimeStart = null;
        $this->activityTimeEnd = null;
        $this->activityName = null;

        if($data !== null && is_object($data)) {
            $this->set_data($data);
        }
    }

    public function get_activity_user_name() {
        return $this->activityUserName;
    }

    public function get_activity_user_email() {
        return $this->activityUserEmail;
    }

    public function get_activity_user_phone() {
        return $this->activityUserPhone;
    }

    public function get_activity_date() {
        return $this->activityDate;
    }

    public function get_activity_time_start() {
        return $this->activityTimeStart;
    }

    public function get_activity_time_end() {
        return $this->activityTimeEnd;
    }

    public function get_activity_name() {
        return $this->activityName;
    }

    private function set_data($data) {
        $data = $this->validate_model_data($data, ['user_name_calendar_add_activity', 'user_email_calendar_add_activity', 'user_phone_calendar_add_activity', 'date_calendar_add_activity',
                                                    'time_start_calendar_add_activity', 'time_end_calendar_add_activity', 'name_calendar_add_activity']);
        
        $this->activityUserName = $data->user_name_calendar_add_activity;
        $this->activityUserEmail = $data->user_email_calendar_add_activity;
        $this->activityUserPhone = $data->user_phone_calendar_add_activity;
        $this->activityDate = $data->date_calendar_add_activity;
        $this->activityTimeStart = $data->time_start_calendar_add_activity;
        $this->activityTimeEnd = $data->time_end_calendar_add_activity;
        $this->activityName = $data->name_calendar_add_activity;
    }

    private function validate_model_data($object, $properties) {
        $validator = new FormValidator;

        foreach($properties as $property) {
            if(property_exists($object, $property)) {
                $type = 'text';
                if(str_contains($property, 'email')) {
                    $type = 'email';
                }
                $object->$property = $validator->validate($object->$property, $type);
            }
        }
        
        return $object;
    }
}