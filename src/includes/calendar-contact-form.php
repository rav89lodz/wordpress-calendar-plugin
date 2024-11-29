<?php

use CalendarPlugin\src\classes\services\AddActivityService;
use CalendarPlugin\src\classes\services\LanguageService;

if (! defined('ABSPATH')) {
    exit;
}

add_shortcode('contact-form-calendar1', 'show_calendar_contact_form');

/**
 * Show calendar contact form
 * 
 * @return string|false
 */
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

/**
 * Handle rest API endpoint
 * 
 * @param mixed data
 * @return object|null
 */
function handle_calendar_form_add_activity($data) {
    $service = new AddActivityService(json_decode($data->get_body()));
    $response = $service->get_response_after_add_activity();

    return new WP_Rest_Response($response['message'], $response['code']);
}

add_action('init', 'create_add_activity_page');

/**
 * Create option on WP menu
 * 
 * @return void
 */
function create_add_activity_page() {
    $service = new LanguageService;
    $args = [
        'public' => true,
        'has_archive' => true,
        'menu_position' => 80,
        'publicly_queryable' => false,
        'description' => $service->addActivityMenu['description'],
        'labels' => [
            'name' => $service->addActivityMenu['name'],
            'singular_name' => $service->addActivityMenu['singular_name'],
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

/**
 * Crete meta box
 * 
 * @return void
 */
function create_meta_box_for_add_activity() {
    $service = new LanguageService;
    add_meta_box('custom_calendar_form', $service->addActivityMenu['meta_box_title'], 'display_add_activity', 'add_activity');
}

/**
 * Display data in meta box
 * 
 * @return void
 */
function display_add_activity() {
    $data = get_post_meta(get_the_ID());
    $service = new LanguageService;

    unset($data['_edit_lock']);
    unset($data['_edit_last']);

    echo "<ul>";

    foreach ($data as $key => $value) {
        echo "<li><strong>" . $service->addActivityFriendlyNames[$key] . "</strong>:<br>" . $value[0] . "</li><br>";
    }

    echo "</ul>";
}

add_filter('manage_add_activity_posts_columns', 'custom_add_activity_columns');

/**
 * Create activity columns view
 * 
 * @param array columns
 * @return array
 */
function custom_add_activity_columns($columns) {
    $service = new LanguageService;
    return [
        'cb' => $columns['cb'],
        'add_activity_user_name' => $service->addActivityFriendlyNames['add_activity_user_name'],
        'add_activity_user_email' => $service->addActivityFriendlyNames['add_activity_user_email'],
        'add_activity_user_phone' => $service->addActivityFriendlyNames['add_activity_user_phone'],
        'add_activity_name' => $service->addActivityFriendlyNames['add_activity_name'],
        'add_activity_date' => $service->addActivityFriendlyNames['add_activity_date'],
        'add_activity_time_start' => $service->addActivityFriendlyNames['add_activity_time_start'],
        'add_activity_time_end' => $service->addActivityFriendlyNames['add_activity_time_end'],
    ];
}

add_action('manage_add_activity_posts_custom_column', 'fill_add_activity_columns', 10, 2);

/**
 * Fill activity columns view
 * 
 * @param string column
 * @param string|int postId
 * @return void
 */
function fill_add_activity_columns($column, $postId) {
    echo get_post_meta($postId, $column, true);
}

add_action('admin_init', 'setup_search_for_calendar_form_add_activity');

/**
 * Setup search for add_activity post type
 * 
 * @return void
 */
function setup_search_for_calendar_form_add_activity() {
    global $typenow;

    if ($typenow == 'add_activity') {
        add_filter('posts_search', 'add_activity_search_override', 10, 2);
    }
}

/**
 * Search data
 * 
 * @param mixed search
 * @param mixed query
 * @return mixed
 */
function add_activity_search_override($search, $query) {
    global $wpdb;

    if ($query->is_main_query() && ! empty($query->query['s'])) {
        $sql = "or exists (
            select * from {$wpdb->postmeta} where post_id={$wpdb->posts}.ID
            and meta_key in ('add_activity_user_name', 'add_activity_user_email', 'add_activity_user_phone', 'add_activity_name')
            and meta_value like %s
        )";
        $like = '%' . $wpdb->esc_like($query->query['s']) . '%';
        $search = preg_replace("#\({$wpdb->posts}.post_title LIKE [^)]+\)\K#", $wpdb->prepare($sql, $like), $search);
    }

    return $search;
}
