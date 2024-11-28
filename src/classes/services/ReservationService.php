<?php

namespace CalendarPlugin\src\classes\services;

use CalendarPlugin\src\classes\models\ReservationModel;
use CalendarPlugin\src\classes\Utils;

class ReservationService
{
    private $utils;
    private $model;
    private $service;

    public function __construct($data = null) {
        $this->utils = new Utils;
        $this->model = new ReservationModel($data);
        $this->service = new LanguageService;
    }

    public function get_response_after_reservation() {
        $message = $this->store_reservation_data($this->model);
        $message[0] = $this->utils->set_up_polish_characters($message[0]);
        $code = 200;
        $subject = $this->service->reservationMessage['subject'];
        $replayTo = ["name" => $this->model->get_user_name(), "email" => $this->model->get_user_email()];
        $this->utils->send_email_with_store_data($message[0], $subject, $replayTo);
        if($message[1] === false) {
            $code = 422;
        }
        return $this->utils->set_success_error_message_with_code($this->model->get_user_name(), $code, $message[1]);
    }

    public function check_reservation_limit($activityId, $date) {
        global $wpdb;
                                                    // SELECT COUNT(*) AS 'limit' FROM `wp_postmeta` WHERE `post_id` IN
                                                    // (SELECT `post_id` FROM `wp_postmeta` WHERE `meta_key` = 'reservation_id' AND `meta_value` = '')
                                                    // AND (`meta_key` = 'reservation_date' AND `meta_value` = '');
        $currentlimit = $wpdb->get_row($wpdb->prepare("SELECT count(*) AS 'limit' FROM $wpdb->postmeta WHERE post_id IN
                                                    (SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'reservation_id' AND meta_value = %s)
                                                    AND (meta_key = 'reservation_date' AND meta_value = %s)", [$activityId, $date]));
        return $currentlimit->limit;
    }

    private function store_reservation_data($model) {
        $message = "<h2>" . $this->service->reservationMessage['message_from'] . " {$model->get_user_name()}</h2>";

        $activity = $model->get_activity();

        $limit = $this->check_reservation_limit($activity->get_hidden_id(), $model->get_reservation_date());
        if($limit >= $activity->get_slot()) {
            $message .= "<div><strong style='color:red'>" . $this->service->reservationMessage['message_beginning_failure'] .
                        "</strong></div><div><strong>" . $this->service->reservationFriendlyNames['user_email'] . "</strong>: " . $model->get_user_email() .
                        "</div><div><strong>" . $this->service->reservationFriendlyNames['reservation_date'] . "</strong>: " . $model->get_reservation_date() .
                        "</div><div><strong>" . $this->service->reservationFriendlyNames['reservation_time'] . "</strong>: " . $model->get_reservation_time() .
                        "</div><div><strong>" . $this->service->reservationFriendlyNames['activity_name'] . "</strong>: " . $activity->get_name();
            return [$message, false];
        }
        $postId = wp_insert_post([
            'post_title' => $this->service->reservationMessage['post_title'] . $model->get_user_name(),
            'post_type' => 'reservation',
            'post_status' => 'publish',
        ]);

        add_post_meta($postId, 'user_name', $model->get_user_name());
        add_post_meta($postId, 'user_email', $model->get_user_email());
        add_post_meta($postId, 'reservation_date', $model->get_reservation_date());
        add_post_meta($postId, 'reservation_time', $model->get_reservation_time());
        add_post_meta($postId, 'reservation_id', $activity->get_hidden_id());
        add_post_meta($postId, 'activity_name', $activity->get_name());

        $message .= "<div><strong style='color:green'>" . $this->service->reservationMessage['message_beginning_success'] .
                    "</strong></div><div><strong>" . $this->service->reservationFriendlyNames['user_email'] . "</strong>: " . $model->get_user_email() .
                    "</div><div><strong>" . $this->service->reservationFriendlyNames['reservation_date'] . "</strong>: " . $model->get_reservation_date() .
                    "</div><div><strong>" . $this->service->reservationFriendlyNames['reservation_time'] . "</strong>: " . $model->get_reservation_time() .
                    "</div><div><strong>" . $this->service->reservationFriendlyNames['activity_name'] . "</strong>: " . $activity->get_name();

        return [$message, true];
    }
}