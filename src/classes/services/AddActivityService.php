<?php

namespace CalendarPlugin\src\classes\services;

use CalendarPlugin\src\classes\models\AddActivityModel;
use CalendarPlugin\src\classes\Utils;

class AddActivityService
{
    private $utils;
    private $model;
    private $service;

    public function __construct($data = null) {
        $this->utils = new Utils;
        $this->model = new AddActivityModel($data);
        $this->service = new LanguageService;
    }

    public function get_response_after_add_activity() {
        $message = $this->store_activity_data($this->model);
        $message = $this->utils->set_up_polish_characters($message);
        $code = 200;
        $subject = $this->service->addActivityMessage['subject'];
        $replayTo = ["name" => $this->model->get_activity_user_name(), "email" => $this->model->get_activity_user_email()];
        $this->utils->send_email_with_store_data($message, $subject, $replayTo);

        return $this->utils->set_success_error_message_with_code($this->model->get_activity_user_name(), $code, $message);
    }

    private function store_activity_data($model) {
        $message = "<h2>" . $this->service->addActivityMessage['message_from'] . " {$model->get_activity_user_name()}</h2>";

        $postId = wp_insert_post([
            'post_title' => $this->service->addActivityMessage['post_title'] . $model->get_activity_user_name(),
            'post_type' => 'add_activity',
            'post_status' => 'publish',
        ]);

        add_post_meta($postId, 'add_activity_user_name', $model->get_activity_user_name());
        add_post_meta($postId, 'add_activity_user_email', $model->get_activity_user_email());
        add_post_meta($postId, 'add_activity_date', $model->get_activity_date());
        add_post_meta($postId, 'add_activity_time_start', $model->get_activity_time_start());
        add_post_meta($postId, 'add_activity_time_end', $model->get_activity_time_end());
        add_post_meta($postId, 'add_activity_name', $model->get_activity_name());

        $message .= "<div><strong>" . $this->service->addActivityMessage['message_beginning'] . $model->get_activity_name() .
                    "</strong></div><div><strong>" . $this->service->addActivityFriendlyNames['add_activity_user_email'] . "</strong>: " . $model->get_activity_user_email() .
                    "</div><div><strong>" . $this->service->addActivityFriendlyNames['add_activity_date'] . "</strong>: " . $model->get_activity_date() .
                    "</div><div><strong>" . $this->service->addActivityFriendlyNames['add_activity_time_start'] . "</strong>: " . $model->get_activity_time_start() .
                    "</div><div><strong>" . $this->service->addActivityFriendlyNames['add_activity_time_end'] . "</strong>: " . $model->get_activity_time_end();

        return $message;
    }
}