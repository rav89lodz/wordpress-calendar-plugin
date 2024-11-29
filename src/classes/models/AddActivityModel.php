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

    private $validator;

    /**
     * Constructor
     * 
     * @param array|object data
     * @return void
     */
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

    /**
     * Get activityUserName
     * 
     * @return string|null
     */
    public function get_activity_user_name() {
        return $this->activityUserName;
    }

    /**
     * Get activityUserEmail
     * 
     * @return string|null
     */
    public function get_activity_user_email() {
        return $this->activityUserEmail;
    }

    /**
     * Get activityUserPhone
     * 
     * @return string|null
     */
    public function get_activity_user_phone() {
        return $this->activityUserPhone;
    }

    /**
     * Get activityDate
     * 
     * @return string|null
     */
    public function get_activity_date() {
        return $this->activityDate;
    }

    /**
     * Get activityTimeStart
     * 
     * @return string|null
     */
    public function get_activity_time_start() {
        return $this->activityTimeStart;
    }

    /**
     * Get activityTimeEnd
     * 
     * @return string|null
     */
    public function get_activity_time_end() {
        return $this->activityTimeEnd;
    }

    /**
     * Get activityName
     * 
     * @return string|null
     */
    public function get_activity_name() {
        return $this->activityName;
    }

    /**
     * Validation for model data and set this data to properties
     * 
     * @param array|object data
     * @return void
     */
    private function set_data($data) {
        $this->validator = new FormValidator;
        foreach($data as $key => $value) {
            switch($key) {
                case 'user_name_calendar_add_activity':
                    $this->activityUserName = $this->validation_sequence_for_name($value);
                    break;
                case 'name_calendar_add_activity':
                    $this->activityName = $this->validation_sequence_for_name($value);
                    break;
                case 'date_calendar_add_activity':
                    $this->activityDate = $this->validation_sequence_for_name($value);
                    break;
                case 'user_email_calendar_add_activity':
                    $this->activityUserEmail = $this->validation_sequence_for_email($value);
                    break;
                case 'user_phone_calendar_add_activity':
                    $this->activityUserPhone = $this->validation_sequence_for_phone($value);
                    break;
                case 'time_start_calendar_add_activity':
                    $this->activityTimeStart = $this->validation_sequence_for_time($value);
                    break;
                case 'time_end_calendar_add_activity':
                    $this->activityTimeEnd = $this->validation_sequence_for_time($value);
                    break;
            }
        }
    }

    /**
     * Validation sequence for name
     * 
     * @param string name
     * @return string|null
     */
    private function validation_sequence_for_name($name) {
        if($this->validator->is_valid_string($name)) {
            return $this->validator->sanitize_name($name);
        }
        return null;
    }

    /**
     * Validation sequence for email
     * 
     * @param string email
     * @return string|null
     */
    private function validation_sequence_for_email($email) {
        if($this->validator->is_valid_email($email)) {
            return $this->validator->sanitize_string($email);
        }
        return null;
    }

    /**
     * Validation sequence for phone
     * 
     * @param string phone
     * @return string|null
     */
    private function validation_sequence_for_phone($phone) {
        if($this->validator->is_valid_phone_number($phone)) {
            return $this->validator->sanitize_string($phone);
        }
        return null;
    }

    /**
     * Validation sequence for time
     * 
     * @param mixed time
     * @return string|null
     */
    private function validation_sequence_for_time($time) {
        if($this->validator->is_valid_time($time)) {
            return $this->validator->sanitize_string($time);
        }
        return null;
    }
}