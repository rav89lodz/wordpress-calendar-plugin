<?php

namespace CalendarPlugin\src\classes\services;

use CalendarPlugin\src\classes\models\AddActivityModel;
use CalendarPlugin\src\classes\Utils;

class AddActivityService
{
    private $utils;
    private $model;
    private $service;

    /**
     * Constructor
     * 
     * @param array|object|null data
     * @return void
     */
    public function __construct($data = null) {
        $this->utils = new Utils;
        $this->model = new AddActivityModel($data);
        $this->service = new LanguageService;
    }

    /**
     * Store activity in DB, send email and get response message
     * 
     * @return array
     */
    public function get_response_after_add_activity() {
        $message = $this->store_activity_data($this->model);
        $message = $this->utils->set_up_polish_characters($message);
        $subject = $this->service->addActivityMessage['subject'];
        $replayTo = ["name" => $this->model->get_activity_user_name(), "email" => $this->model->get_activity_user_email()];

        $isSended = $this->utils->send_email_with_data($message, $subject, $replayTo);

        if($isSended === false) {
            return $this->utils->set_success_error_message_with_code($this->model->get_activity_user_name(), 422);
        }
        return $this->utils->set_success_error_message_with_code($this->model->get_activity_user_name(), 200, 2);
    }

    /**
     * Add activity to DB by model data and get response message
     * 
     * @param object model
     * @return string
     */
    private function store_activity_data($model) {
        $message = "<h2>" . $this->service->addActivityMessage['message_from'] . " {$model->get_activity_user_name()}</h2>";

        $this->insert_data($model);

        $message .= "<div><strong>" . $this->service->addActivityMessage['message_beginning'] . $model->get_activity_name() .
                    "</strong></div><div><strong>" . $this->service->addActivityFriendlyNames['add_activity_user_email'] . "</strong>: " . $model->get_activity_user_email() .
                    "</div><div><strong>" . $this->service->addActivityFriendlyNames['add_activity_user_phone'] . "</strong>: " . $model->get_activity_user_phone() .
                    "</div><div><strong>" . $this->service->addActivityFriendlyNames['add_activity_date'] . "</strong>: " . $model->get_activity_date() .
                    "</div><div><strong>" . $this->service->addActivityFriendlyNames['add_activity_time_start'] . "</strong>: " . $model->get_activity_time_start() .
                    "</div><div><strong>" . $this->service->addActivityFriendlyNames['add_activity_time_end'] . "</strong>: " . $model->get_activity_time_end();

        return $message;
    }

    /**
     * Insert data to DB by model data
     * 
     * @param object model
     * @return void
     */
    private function insert_data($model) {
        $postId = wp_insert_post([
            'post_title' => $this->service->addActivityMessage['post_title'] . $model->get_activity_user_name(),
            'post_type' => 'add_activity',
            'post_status' => 'publish',
        ]);

        add_post_meta($postId, 'add_activity_user_name', $model->get_activity_user_name());
        add_post_meta($postId, 'add_activity_user_email', $model->get_activity_user_email());
        add_post_meta($postId, 'add_activity_user_phone', $model->get_activity_user_phone());
        add_post_meta($postId, 'add_activity_date', $model->get_activity_date());
        add_post_meta($postId, 'add_activity_time_start', $model->get_activity_time_start());
        add_post_meta($postId, 'add_activity_time_end', $model->get_activity_time_end());
        add_post_meta($postId, 'add_activity_name', $model->get_activity_name());
    }
}