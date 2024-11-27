<?php

use CalendarPlugin\src\classes\services\AddActivityService;

if (! defined('ABSPATH')) {
    exit;
}

add_shortcode('contact-form-calendar1', 'show_calendar_contact_form');

function show_calendar_contact_form() {
    ob_start();
        include( CALENDAR_PLUGIN_PATH . '/src/templates/calendar-contact-form.php' );
    return ob_get_clean();
}

add_action('rest_api_init', function(){
    register_rest_route('v1/calendar-grid-form', 'add-activity', [
        'methods' => 'POST',
        'callback' => 'handle_calendar_form_add_activity',
        'permission_callback' => [],
    ]);
});

function handle_calendar_form_add_activity($data) {
    $service = new AddActivityService(json_decode($data->get_body()));
    $response = $service->get_response_after_add_activity();

    return new WP_Rest_Response($response['message'], $response['code']);
}

add_action('init', 'create_add_activity_page');

function create_add_activity_page() {
    $args = [
        'public' => true,
        'has_archive' => true,
        'menu_position' => 80,
        'publicly_queryable' => false,
        'description' => 'Rezerwacje terminów w kalnedarzu',
        'labels' => [
            'name' => 'Rezerwacje',
            'singular_name' => 'Rezerwacja',
        ],
        'capability_type' => 'post',
        'capabilities' => [
            'create_posts' => false,
        ],
        'supports' => false,
        'map_meta_cap' => true,
    ];

    register_post_type('add_activity', $args);
}

add_action('add_meta_boxes', 'create_meta_box_for_add_activity');

function create_meta_box_for_add_activity() {
    add_meta_box('custom_calendar_form', 'Rezerwacja zajęć', 'display_add_activity', 'add_activity');
}

function display_add_activity() {
    $data = get_post_meta(get_the_ID());
    $service = new AddActivityService();

    unset($data['_edit_lock']);
    unset($data['_edit_last']);

    echo "<ul>";

    foreach ($data as $key => $value) {
        echo "<li><strong>" . $service->get_user_friendly_names($key) . "</strong>:<br>" . $value[0] . "</li>";
    }

    echo "</ul>";
}

add_filter('manage_add_activity_posts_columns', 'custom_add_activity_columns');

function custom_add_activity_columns($columns) {
    return [
        'cb' => $columns['cb'],
        'add_activity_user_name' => 'Imię i nazwisko',
        'add_activity_user_email' => 'Adres email',
        'add_activity_name' => 'Nazwa zajęć',
        'add_activity_date' => 'Data rezerwacji',
        'add_activity_time' => 'Godzina rezerwacji',
        'add_activity_duration' => 'Czas trwania zajęć',
    ];
}

add_action('manage_add_activity_posts_custom_column', 'fill_add_activity_columns', 10, 2);

function fill_add_activity_columns($column, $postId) {
    switch ($column) {
        case 'add_activity_user_name':
            echo get_post_meta($postId, 'add_activity_user_name', true);
            break;
        case 'add_activity_user_email':
            echo get_post_meta($postId, 'add_activity_user_email', true);
            break;
        case 'add_activity_name':
            echo get_post_meta($postId, 'add_activity_name', true);
            break;
        case 'add_activity_date':
            echo get_post_meta($postId, 'add_activity_date', true);
            break;
        case 'add_activity_time':
            echo get_post_meta($postId, 'add_activity_time', true);
            break;
        case 'add_activity_duration':
            echo get_post_meta($postId, 'add_activity_duration', true);
            break;
    }
}

add_action('admin_init', 'setup_search_for_calendar_form_reservation');

function setup_search_for_calendar_form_reservation() {
    global $typenow;

    if ($typenow == 'add_activity') {
        add_filter('posts_search', 'add_activity_search_override', 10, 2);
    }
}

function add_activity_search_override($search, $query) {
    global $wpdb;

    if ($query->is_main_query() && ! empty($query->query['s'])) {
        $sql = "or exists (
            select * from {$wpdb->postmeta} where post_id={$wpdb->posts}.ID
            and meta_key in ('add_activity_user_name', 'add_activity_user_email', 'add_activity_name')
            and meta_value like %s
        )";
        $like = '%' . $wpdb->esc_like($query->query['s']) . '%';
        $search = preg_replace("#\({$wpdb->posts}.post_title LIKE [^)]+\)\K#", $wpdb->prepare($sql, $like), $search);
    }

    return $search;
}
