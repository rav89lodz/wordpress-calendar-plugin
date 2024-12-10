<?php

namespace CalendarPlugin\src\classes\services;

use CalendarPlugin\src\classes\Utils;

class OptionsPageService
{
    /**
     * Constructor
     */
    public function __construct() {
    }

    /**
     * Update data after settings page is saved
     * 
     * @return void
     */
    public function update_data_after_save() {
        $this->set_hidden_ids();
        $this->remove_incorrect_data();
        $this->set_type_keys();
    }

    /**
     * Get activity types from DB
     * 
     * @return array
     */
    public function get_activity_types() {
        global $wpdb;
    
        $rows = $wpdb->get_results( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name like %s", '_calendar_plugin_types|type_%' ), ARRAY_A );
    
        if($rows !== null && is_array($rows)) {
            $utils = new Utils;
            return $utils->array_of_object_to_flat_array($rows);
        }
        
        return [];
    }

    /**
     * Create short code
     * 
     * @param string code
     * @return string
     */
    public function create_short_code($code) {
        $utils = new Utils;
        $short = $utils->remove_polish_letters($code);
        $short = strtolower(str_replace(" ", "_", $short));
        return $short;
    }

    /**
     * Set type keys if incorrect value after settings page is saved
     * 
     * @return void
     */
    private function set_type_keys() {
        global $wpdb;
        $rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->options WHERE option_name LIKE %s", '_calendar_plugin_types|type_name%' ), ARRAY_A );
        foreach($rows as $row) {
            $oprionName = str_replace("type_name", "type_key", $row["option_name"]);
            $typeKey = $this->create_short_code($row["option_value"]);
            update_option( $oprionName, $typeKey );
        }
    }

    /**
     * Remove incorrect data after settings page is saved
     * 
     * @return void
     */
    private function remove_incorrect_data() {
        global $wpdb;
                                // SELECT * FROM `wp_options` WHERE `option_name` IN (SELECT CONCAT("_calendar_plugin_data|activity_date|", SUBSTRING_INDEX(`option_name`, '|', -3)) as "id" FROM `wp_options` WHERE `option_name` like '_calendar_plugin_data|activity_cyclic%' AND `option_value` = 1) AND `option_value` LIKE '%-%-%';
        $query = $wpdb->prepare( "SELECT * FROM $wpdb->options WHERE option_name IN (SELECT CONCAT('_calendar_plugin_data|activity_date|', SUBSTRING_INDEX(`option_name`, '|', -3)) as 'id' FROM $wpdb->options WHERE option_name LIKE '_calendar_plugin_data|activity_cyclic%' AND option_value = 1) AND option_value LIKE %s", '%-%-%' );
        $rows = $wpdb->get_results($query, ARRAY_A );

        foreach($rows as $row) {
            update_option( $row['option_name'], null );
        }

        $rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->options WHERE option_name IN (%s, %s, %s)", ['_calendar_plugin_grid_width', '_calendar_plugin_grid_height', '_calendar_plugin_cell_min_height']), ARRAY_A );

        foreach($rows as $row) {
            $number = preg_replace('/[^0-9.-]/', '', $row['option_value']);
            update_option( $row['option_name'], $number );
        }

        $rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->options WHERE option_name IN (%s, %s)", ['_calendar_plugin_one_day_view', '_calendar_plugin_horizontal_calendar_grid']), ARRAY_A );

        if(count($rows) === 2 && $rows[1]['option_value'] !== null && ! empty($rows[1]['option_value'])) {
            update_option( $rows[0]['option_name'], null );
        }
    }

    /**
     * Set Id for calendar object after settings page is saved
     * 
     * @return void
     */
    private function set_hidden_ids() {
        global $wpdb;

        $rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->options WHERE option_name LIKE %s AND (option_value = '' OR option_value IS NULL)", '_calendar_plugin_data|activity_hidden_id%' ), ARRAY_A );

        foreach($rows as $row) {
            update_option( $row['option_name'], $this->get_uuid_for_calendar_object() );
        }
    }

    /**
     * Create UUID
     * 
     * @return string
     */
    private function get_uuid_for_calendar_object() {
        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
            
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}