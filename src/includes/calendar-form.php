<?php

use CalendarPlugin\src\classes\services\LanguageService;
use CalendarPlugin\src\classes\services\ReservationService;
use CalendarPlugin\src\classes\Utils;

if (! defined('ABSPATH')) {
    exit;
}

add_shortcode('calendar-grid1', 'show_calendar_grid');

/**
 * Show calendar grid
 * 
 * @param mixed post_id
 * @return string|false
 */
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

/**
 * Handle rest API endpoint
 * 
 * @param mixed data
 * @return string|false
 */
function handle_calendar_grid_change_month($data) {
    $data = json_decode($data->get_body());
    $_POST['calendar_grid_change_month'] = $data->data;
    $utils = new Utils;
    $_POST['calendar_grid_short_code'] = $utils->prepare_current_short_codes("[" . $data->short_code . "]");
    
    ob_start();
        include( CALENDAR_PLUGIN_PATH . '/src/templates/calendar-form.php' );
    return ob_get_clean();
}

/**
 * Handle rest API endpoint
 * 
 * @param mixed data
 * @return string|false
 */
function handle_calendar_grid_change_week($data) {
    $data = json_decode($data->get_body());
    $_POST['calendar_grid_change_week'] = $data->data;
    $utils = new Utils;
    $_POST['calendar_grid_short_code'] = $utils->prepare_current_short_codes("[" . $data->short_code . "]");
   
    ob_start();
        include( CALENDAR_PLUGIN_PATH . '/src/templates/calendar-form.php' );
    return ob_get_clean();
}

/**
 * Handle rest API endpoint
 * 
 * @param mixed data
 * @return object|null
 */
function handle_calendar_grid_form_registration_for_activity($data) {
    $service = new ReservationService(json_decode($data->get_body()));
    $response = $service->get_response_after_reservation();

    return new WP_Rest_Response($response['message'], $response['code']);
}


add_action('init', 'create_reservation_page');

/**
 * Create option on WP menu
 * 
 * @return void
 */
function create_reservation_page() {
    $service = new LanguageService;
    $args = [
        'public' => true,
        'has_archive' => true,
        'menu_position' => 80,
        'publicly_queryable' => false,
        'description' => $service->reservationMenu['description'],
        'labels' => [
            'name' => $service->reservationMenu['name'],
            'singular_name' => $service->reservationMenu['singular_name'],
        ],
        'show_in_menu' => true,
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

/**
 * Crete meta box
 * 
 * @return void
 */
function create_meta_box_for_calendar_plugin() {
    $service = new LanguageService;
    add_meta_box('custom_calendar_form', $service->reservationMenu['meta_box_title'], 'display_reservation', 'reservation');
}

/**
 * Display data in meta box
 * 
 * @return void
 */
function display_reservation() {
    $data = get_post_meta(get_the_ID());
    $service = new LanguageService;

    unset($data['_edit_lock']);
    unset($data['_edit_last']);
    unset($data['reservation_id']);

    echo "<ul>";

    foreach ($data as $key => $value) {
        echo "<li><strong>" . $service->reservationFriendlyNames[$key] . "</strong>:<br>" . $value[0] . "</li><br>";
    }

    echo "</ul>";
}

add_filter('manage_reservation_posts_columns', 'custom_reservation_columns');

/**
 * Create reservation columns view
 * 
 * @param array columns
 * @return array
 */
function custom_reservation_columns($columns) {
    $service = new LanguageService;
    return [
        'cb' => $columns['cb'],
        'user_name' => $service->reservationFriendlyNames['user_name'],
        'user_email' => $service->reservationFriendlyNames['user_email'],
        'activity_name' => $service->reservationFriendlyNames['activity_name'],
        'reservation_date' => $service->reservationFriendlyNames['reservation_date'],
        'reservation_time' => $service->reservationFriendlyNames['reservation_time'],
    ];
}

add_action('manage_reservation_posts_custom_column', 'fill_reservation_columns', 10, 2);

/**
 * Fill reservation columns view
 * 
 * @param string column
 * @param string|int postId
 * @return void
 */
function fill_reservation_columns($column, $postId) {
    echo get_post_meta($postId, $column, true);
}

add_action('admin_init', 'setup_search_for_calendar_grid_plugin');

/**
 * Setup search for reservation post type
 * 
 * @return void
 */
function setup_search_for_calendar_grid_plugin() {
    global $typenow;

    if ($typenow == 'reservation') {
        add_filter('posts_search', 'reservation_search_override', 10, 2);
    }
}

/**
 * Search data
 * 
 * @param mixed search
 * @param mixed query
 * @return mixed
 */
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
