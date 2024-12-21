<?php

/**
 * Plugin Name:       Calendar Plugin
 * Plugin URI:        https://github.com/rav89lodz/wordpress-calendar-plugin
 * Description:       Calendar Plugin for modern term reservation form
 * Version:           1.0.0
 * Requires at least: 6.6.2
 * Requires PHP:      7.2
 * Author:            Rafał Chęciński
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://github.com/rav89lodz/wordpress-calendar-plugin
 * Text Domain:       calendar-plugin
 * Domain Path:       /languages
 */

use CalendarPlugin\src\classes\Donate;

if (! defined('ABSPATH')) {
    exit;
}

if (! class_exists('CalendarPlugin')) {
    class CalendarPlugin
    {
        public function __construct() {
            define('CALENDAR_PLUGIN_PATH', plugin_dir_path( __FILE__ ));
            define('CALENDAR_PLUGIN_URL', plugin_dir_url( __FILE__ ));

            require_once(CALENDAR_PLUGIN_PATH . '/vendor/autoload.php');
        }

        public function initialize() {
            include_once CALENDAR_PLUGIN_PATH . '/src/includes/calendar-contact-form.php';
            include_once CALENDAR_PLUGIN_PATH . '/src/includes/calendar-form.php';
            include_once CALENDAR_PLUGIN_PATH . '/src/includes/options-page.php';
            include_once CALENDAR_PLUGIN_PATH . '/src/includes/utilities.php';

            require_once(CALENDAR_PLUGIN_PATH . '/src/classes/services/AddActivityService.php');
            require_once(CALENDAR_PLUGIN_PATH . '/src/classes/services/CalendarService.php');
            require_once(CALENDAR_PLUGIN_PATH . '/src/classes/services/LanguageService.php');
            require_once(CALENDAR_PLUGIN_PATH . '/src/classes/services/OptionsPageService.php');
            require_once(CALENDAR_PLUGIN_PATH . '/src/classes/services/ReservationService.php');

            require_once(CALENDAR_PLUGIN_PATH . '/src/classes/forms/CalendarForm.php');
            require_once(CALENDAR_PLUGIN_PATH . '/src/classes/forms/CalendarHorizontalForm.php');
            require_once(CALENDAR_PLUGIN_PATH . '/src/classes/forms/CalendarOneDayForm.php');
            require_once(CALENDAR_PLUGIN_PATH . '/src/classes/forms/CalendarVerticalForm.php');

            require_once(CALENDAR_PLUGIN_PATH . '/src/classes/FormValidator.php');
            require_once(CALENDAR_PLUGIN_PATH . '/src/classes/Utils.php');

            require_once(CALENDAR_PLUGIN_PATH . '/src/classes/models/ActivityModel.php');
            require_once(CALENDAR_PLUGIN_PATH . '/src/classes/models/AddActivityModel.php');
            require_once(CALENDAR_PLUGIN_PATH . '/src/classes/models/CalendarModel.php');
            require_once(CALENDAR_PLUGIN_PATH . '/src/classes/models/MessageModel.php');
            require_once(CALENDAR_PLUGIN_PATH . '/src/classes/models/ReservationModel.php');
        }
    }

    $calendarPlugin = new CalendarPlugin;
    $calendarPlugin->initialize();

    add_filter( 'plugin_action_links', function( $actions, $plugin_file ) {
        static $plugin;

        if (!isset($plugin)) {
            $plugin = plugin_basename(__FILE__);
        }
        
        if ($plugin == $plugin_file) {
            $actions = array_merge(['settings' => '<a href="admin.php?page=calendar-options-global">' . __('Settings', 'General') . '</a>'], $actions);
        }

        return $actions;
    }, 10, 5 );
}

/**
 * Die and dump
 * 
 * @param mixed data
 * @return void
 */
function dd(...$data) {
    foreach($data as $element) {
        // var_export($element, true);
        var_dump($element);
    }
    die();
}
