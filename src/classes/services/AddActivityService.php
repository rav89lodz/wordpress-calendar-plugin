<?php

namespace CalendarPlugin\src\classes\services;

use CalendarPlugin\src\classes\models\AddActivityModel;
use CalendarPlugin\src\classes\Utils;

class AddActivityService
{
    private $utils;
    private $model;

    public function __construct($data = null) {
        $this->utils = new Utils;
        $this->model = new AddActivityModel($data);
    }

    public function get_response_after_add_activity() {
        $message = $this->store_activity_data($this->model);
        $message = $this->utils->set_up_polish_characters($message);
        $code = 200;
        $subject = "Prośba o dopisanie zajęć do kalendarza";
        $replayTo = ["name" => $this->model->get_activity_user_name(), "email" => $this->model->get_activity_user_email()];
        $this->utils->send_email_with_store_data($message, $subject, $replayTo);

        return $this->utils->set_success_error_message_with_code($this->model->get_activity_user_name(), $code, $message);
    }
    
    public function get_user_friendly_names($name) {
        switch($name) {
            case 'add_activity_user_name':
                return 'Imię i nazwisko';
            case 'add_activity_user_email':
                return 'Adres email';
            case 'add_activity_date':
                return 'Proponowana data zajęć';
            case 'add_activity_time':
                return 'Godzina rozpoczęcia';
            case 'add_activity_duration':
                return 'Czas trwania w minutach';
            case 'add_activity_name':
                return 'Nazwa zajęć';
            default:
                return "";
        }
    }

    private function store_activity_data($model) {
        $message = "<h2>Wiadomość od {$model->get_activity_user_name()}</h2>";

        $postId = wp_insert_post([
            'post_title' => "Dopisanie zajęć do kalendarza: " . $model->get_activity_user_name(),
            'post_type' => 'add_activity',
            'post_status' => 'publish',
        ]);

        add_post_meta($postId, 'add_activity_user_name', $model->get_activity_user_name());
        add_post_meta($postId, 'add_activity_user_email', $model->get_activity_user_email());
        add_post_meta($postId, 'add_activity_date', $model->get_activity_date());
        add_post_meta($postId, 'add_activity_time', $model->get_activity_time());
        add_post_meta($postId, 'add_activity_duration', $model->get_activity_duration());
        add_post_meta($postId, 'add_activity_name', $model->get_activity_name());

        $message .= "<div><strong>Prośba o dopisanie do kalendarza zajęć: " . $model->get_activity_name() . "</strong></div><div><strong>Adres email</strong>: " . $model->get_activity_user_email() . "</div><div><strong>Data zajęć</strong>: "
                    . $model->get_activity_date() . "</div><div><strong>Godzina zajęć</strong>: " . $model->get_activity_time() . "</div><div><strong>Czas trwania zajęć: </strong>: " . $model->get_activity_duration();

        return $message;
    }
}