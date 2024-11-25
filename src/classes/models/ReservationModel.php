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

    public function get_user_name() {
        return $this->userName;
    }

    public function get_user_email() {
        return $this->userEmail;
    }

    public function get_reservation_date() {
        return $this->reservationDate;
    }

    public function get_reservation_time() {
        return $this->reservationTime;
    }

    public function get_activity() {
        return $this->activity;
    }

    private function set_data($data) {
        $data = $this->validate_model_data($data, ['user_name_calendar_modal', 'user_email_calendar_modal', 'calendar_modal_day_name', 'calendar_modal_hour', 'calendar_modal_hidden_id']);
        
        $this->userName = $data->user_name_calendar_modal;
        $this->userEmail = $data->user_email_calendar_modal;
        $this->reservationDate = $data->calendar_modal_day_name;
        $this->reservationTime = $data->calendar_modal_hour;
        $this->activity = new ActivityModel($data->calendar_modal_hidden_id);
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