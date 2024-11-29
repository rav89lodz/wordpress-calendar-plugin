<?php

namespace CalendarPlugin\src\classes\models;

use CalendarPlugin\src\classes\FormValidator;

class ReservationModel
{
    private $userName;
    private $userEmail;
    private $reservationDate;
    private $reservationTime;
    private $activity;

    private $validator;

    /**
     * Constructor
     * 
     * @param array|object data
     * @return void
     */
    public function __construct($data = null) {
        $this->userName = null;
        $this->userEmail = null;
        $this->reservationDate = null;
        $this->reservationTime = null;
        $this->activity = null;

        if($data !== null && is_object($data)) {
            $this->set_data($data);
        }
    }

    /**
     * Get userName
     * 
     * @return string|null
     */
    public function get_user_name() {
        return $this->userName;
    }

    /**
     * Get userEmail
     * 
     * @return string|null
     */
    public function get_user_email() {
        return $this->userEmail;
    }

    /**
     * Get reservationDate
     * 
     * @return string|null
     */
    public function get_reservation_date() {
        return $this->reservationDate;
    }

    /**
     * Get reservationTime
     * 
     * @return string|null
     */
    public function get_reservation_time() {
        return $this->reservationTime;
    }

    /**
     * Get activity
     * 
     * @return object|null
     */
    public function get_activity() {
        return $this->activity;
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
                case 'user_name_calendar_modal':
                    $this->userName = $this->validation_sequence_for_name($value);
                    break;
                case 'calendar_modal_day_name':
                    $this->reservationDate = $this->validation_sequence_for_date($value);
                    break;
                case 'calendar_modal_hour':
                    $this->reservationTime = $this->validation_sequence_for_time($value);
                    break;
                case 'user_email_calendar_modal':
                    $this->userEmail = $this->validation_sequence_for_email($value);
                    break;
                case 'calendar_modal_hidden_id':
                    $this->activity = new ActivityModel($this->validation_sequence_for_id($value));
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
     * Validation sequence for date
     * 
     * @param string date
     * @return string|null
     */
    private function validation_sequence_for_date($date) {
        if($this->validator->is_valid_date($date)) {
            return $this->validator->sanitize_string($date);
        }
        return null;
    }

    /**
     * Validation sequence for time
     * 
     * @param string time
     * @return string|null
     */
    private function validation_sequence_for_time($time) {
        if($this->validator->is_valid_time($time)) {
            return $this->validator->sanitize_string($time);
        }
        return null;
    }

    /**
     * Validation sequence for id
     * 
     * @param string id
     * @return string|null
     */
    private function validation_sequence_for_id($id) {
        if($this->validator->is_valid_string($id)) {
            return $this->validator->sanitize_string($id);
        }
        return null;
    }
}