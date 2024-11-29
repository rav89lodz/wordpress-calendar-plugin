<?php

if (! defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', 'enqueue_custom_scripts_for_calendar_plugin');

/**
 * Get calendar plugin options
 * 
 * @param string param
 * @return mixed
 */
function get_calendar_plugin_options($name) {
    return carbon_get_theme_option($name);
}

/**
 * Enqueue custom scripts
 * 
 * @return void
 */
function enqueue_custom_scripts_for_calendar_plugin() {
    wp_enqueue_style('calendar-form-style', CALENDAR_PLUGIN_URL . 'src/assets/css/bootstrap.min.css');
    wp_enqueue_style('calendar-form-style-map', CALENDAR_PLUGIN_URL . 'src/assets/css/bootstrap.min.css.map');
    wp_enqueue_style('calendar-form-plugin', CALENDAR_PLUGIN_URL . 'src/assets/css/calendar-plugin.css');
    wp_enqueue_script('calendar-form-style', CALENDAR_PLUGIN_URL . 'src/assets/js/bootstrap.min.js');
    // wp_enqueue_script('calendar-form-style-map', CALENDAR_PLUGIN_URL . 'src/assets/js/bootstrap.min.js.map');
    wp_enqueue_script('calendar-form-functions', CALENDAR_PLUGIN_URL . 'src/assets/js/calendar-functions.js');
    wp_enqueue_script('calendar-form-plugin', CALENDAR_PLUGIN_URL . 'src/assets/js/calendar-plugin.js');
    wp_enqueue_script('calendar-form-plugin-form', CALENDAR_PLUGIN_URL . 'src/assets/js/calendar-plugin-form.js');
}