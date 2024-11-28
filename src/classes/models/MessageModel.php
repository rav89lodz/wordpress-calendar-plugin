<?php

namespace CalendarPlugin\src\classes\models;

class MessageModel
{
    private $messageSuccess;
    private $messageError;
    private $messageFormSended;

    public function __construct() {
        $this->messageSuccess = $this->set_massage_option('calendar_plugin_message_success');
        $this->messageError = $this->set_massage_option('calendar_plugin_message_error');
        $this->messageFormSended = $this->set_massage_option('calendar_plugin_reservation_send-message');
    }

    public function get_message_success() {
        return $this->messageSuccess;
    }

    public function get_message_error() {
        return $this->messageError;
    }

    public function get_message_form_sended() {
        return $this->messageFormSended;
    }

    private function set_massage_option($option) {
        if(empty(get_calendar_plugin_options($option))) {
            return null;
        }
        return get_calendar_plugin_options($option);
    }
}