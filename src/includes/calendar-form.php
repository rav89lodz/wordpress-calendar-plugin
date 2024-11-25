<?php

use CalendarPlugin\src\classes\services\ReservationService;
use CalendarPlugin\src\classes\Utils;

if (! defined('ABSPATH')) {
    exit;
}

add_shortcode('calendar-grid1', 'show_calendar_grid');

function show_calendar_grid($post_id = null) {
    $post_content = get_post_field('post_content', $post_id);

    $utils = new Utils;
    $_POST['calendar_grid_short_code'] = $utils->prepare_current_short_codes($post_content);

    ob_start();
        include( CALENDAR_PLUGIN_PATH . '/src/templates/calendar-form.php' );
    return ob_get_clean();
}

add_action('rest_api_init', function() {
    register_rest_route('v1/calendar-grid-change', 'week', [
        'methods' => 'POST',
        'callback' => 'handle_calendar_grid_change_week',
        'permission_callback' => [],
    ]);
    register_rest_route('v1/calendar-grid-change', 'month', [
        'methods' => 'POST',
        'callback' => 'handle_calendar_grid_change_month',
        'permission_callback' => [],
    ]);
    register_rest_route('v1/calendar-grid-form', 'registration-for-activity', [
        'methods' => 'POST',
        'callback' => 'handle_calendar_grid_form_registration_for_activity',
        'permission_callback' => [],
    ]);
});

function handle_calendar_grid_change_month($data) {
    $data = json_decode($data->get_body());
    $_POST['calendar_grid_change_month'] = $data->data;
    $utils = new Utils;
    $_POST['calendar_grid_short_code'] = $utils->prepare_current_short_codes("[" . $data->short_code . "]");
    
    ob_start();
        include( CALENDAR_PLUGIN_PATH . '/src/templates/calendar-form.php' );
    return ob_get_clean();
}

function handle_calendar_grid_change_week($data) {
    $data = json_decode($data->get_body());
    $_POST['calendar_grid_change_week'] = $data->data;
    $utils = new Utils;
    $_POST['calendar_grid_short_code'] = $utils->prepare_current_short_codes("[" . $data->short_code . "]");
   
    ob_start();
        include( CALENDAR_PLUGIN_PATH . '/src/templates/calendar-form.php' );
    return ob_get_clean();
}

function handle_calendar_grid_form_registration_for_activity($data) {
    $service = new ReservationService(json_decode($data->get_body()));
    $response = $service->get_response_after_reservation();

    return new WP_Rest_Response($response['message'], $response['code']);
}


add_action('init', 'create_reservation_page');

function create_reservation_page() {
    $args = [
        'public' => true,
        'has_archive' => true,
        'menu_position' => 80,
        'publicly_queryable' => false,
        'description' => 'Zapisy na zajęcia',
        'labels' => [
            'name' => 'Zapisy na zajęcia',
            'singular_name' => 'Zapis na zajęcia',
        ],
        'capability_type' => 'post',
        'capabilities' => [
            'create_posts' => false,
        ],
        'supports' => false,
        'map_meta_cap' => true,
    ];

    register_post_type('reservation', $args);
}

add_action('add_meta_boxes', 'create_meta_box_for_calendar_plugin');

function create_meta_box_for_calendar_plugin() {
    add_meta_box('custom_calendar_form', 'Zapis na zajęcia', 'display_reservation', 'reservation');
}

function display_reservation() {
    $data = get_post_meta(get_the_ID());
    unset($data['_edit_lock']);
    unset($data['_edit_last']);

    echo "<ul>";

    foreach ($data as $key => $value) {
        echo "<li><strong>" . ucfirst($key) . "</strong>:<br>" . $value[0] . "</li>";
    }

    echo "</ul>";
}

add_filter('manage_reservation_posts_columns', 'custom_reservation_columns'); // manage_{post-type}_posts_columns

function custom_reservation_columns($columns) {
    return [
        'cb' => $columns['cb'],
        'user_name' => 'Imię i nazwisko', // calendar-plugin = Text Domain from calendar-plugin.php
        'user_email' => 'Adres email',
        'activity_name' => 'Nazwa zajęć',
        'reservation_date' => 'Data rezerwacji',
        'reservation_time' => 'Godzina rezerwacji',
    ];
}

add_action('manage_reservation_posts_custom_column', 'fill_reservation_columns', 10, 2); // manage_{post-type}_posts_custom_column

function fill_reservation_columns($column, $postId) {
    switch ($column) {
        case 'user_name':
            echo get_post_meta($postId, 'user_name', true);
            break;
        case 'user_email':
            echo get_post_meta($postId, 'user_email', true);
            break;
        case 'activity_name':
            echo get_post_meta($postId, 'activity_name', true);
            break;
        case 'reservation_date':
            echo get_post_meta($postId, 'reservation_date', true);
            break;
        case 'reservation_time':
            echo get_post_meta($postId, 'reservation_time', true);
            break;
    }
}

add_action('admin_init', 'setup_search_for_calendar_grid_plugin');

function setup_search_for_calendar_grid_plugin() {
    global $typenow;

    if ($typenow == 'reservation') {
        add_filter('posts_search', 'reservation_search_override', 10, 2);
    }
}

function reservation_search_override($search, $query) {
    global $wpdb;

    if ($query->is_main_query() && ! empty($query->query['s'])) {
        $sql = "or exists (
            select * from {$wpdb->postmeta} where post_id={$wpdb->posts}.ID
            and meta_key in ('user_name', 'user_email', 'activity_name')
            and meta_value like %s
        )";
        $like = '%' . $wpdb->esc_like($query->query['s']) . '%';
        $search = preg_replace("#\({$wpdb->posts}.post_title LIKE [^)]+\)\K#", $wpdb->prepare($sql, $like), $search);
    }

    return $search;
}
