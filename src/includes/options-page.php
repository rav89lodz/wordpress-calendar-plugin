<?php

if (! defined('ABSPATH')) {
    exit;
}

use CalendarPlugin\src\classes\services\LanguageService;
use CalendarPlugin\src\classes\services\OptionsPageService;
use Carbon_Fields\Carbon_Fields;
use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action('after_setup_theme', 'load_carbon_fields_for_calendar_plugin');

add_action( 'admin_enqueue_scripts', function() {
	wp_enqueue_script( 'flatpickr-locale-pl', 'https://npmcdn.com/flatpickr/dist/l10n/pl.js', [ 'carbon-fields-core' ] );
});

add_filter( 'carbon_fields_theme_options_container_admin_only_access', '__return_false' );

add_action('carbon_fields_register_fields', 'create_options_page_for_calendar_plugin');

add_action('carbon_fields_theme_options_container_saved', function() {
    $optionsPageService = new OptionsPageService;
    $optionsPageService->update_data_after_save();
}, 10, 2);

function load_carbon_fields_for_calendar_plugin() {
    Carbon_Fields::boot();
}

function create_options_page_for_calendar_plugin() {
    $optionsPageService = new OptionsPageService;
    $langService = new LanguageService;

    $fields = [];
    foreach($optionsPageService->get_activity_types() as $key => $value) {
        add_shortcode($key, 'show_calendar_grid');
        $fields[] = Field::make( 'html', $key )
                    ->set_html( '<h2>[' . $key . ']</h2><p>' . $langService->optionPage['short_code_activity_place'] . $value . '</p>' );

    }

    $fields[] = Field::make( 'complex', 'calendar_plugin_types', $langService->optionPage['activity_place_description'] )->add_fields( array(
                    Field::make( 'text', 'type_name', $langService->optionPage['activity_place_field1_name'] )
                        ->set_required( true )
                        ->set_help_text( $langService->optionPage['activity_place_field1_description'] ),
                    Field::make( 'hidden', 'type_key', "" ),
                ));

    $mainMenu = Container::make('theme_options', $langService->optionPage['main_menu_settings'])
        ->set_page_menu_position(80)
        ->set_page_file( 'calendar-options-global' )
        ->set_icon('dashicons-calendar-alt')
        ->add_fields([
            Field::make( 'html', 'calendar_plugin_short_code' )
                ->set_html( '<h2>[calendar-grid1]</h2><p>' . $langService->optionPage['main_short_code'] . '</p>' ),

            Field::make( 'html', 'calendar_plugin_contact_form_short_code' )
                ->set_html( '<h2>[contact-form-calendar1]</h2><p>' . $langService->optionPage['main_short_code_form'] . '</p>' ),

            Field::make( 'checkbox', 'calendar_plugin_make_rsv_by_calendar', $langService->optionPage['main_menu_field1_name'] )
                ->set_option_value( '1' )
                ->set_help_text( $langService->optionPage['main_menu_field1_description'] ),

            Field::make( 'checkbox', 'calendar_plugin_fluent_calendar_grid', $langService->optionPage['main_menu_field2_name'] )
                ->set_option_value( '1' )
                ->set_help_text( $langService->optionPage['main_menu_field2_description'] ),

            Field::make( 'checkbox', 'calendar_plugin_duration_time_on_grid', $langService->optionPage['main_menu_field3_name'] )
                ->set_option_value( '1' )
                ->set_help_text( $langService->optionPage['main_menu_field3_description'] ),

            Field::make( 'checkbox', 'calendar_plugin_activity_place_on_grid', $langService->optionPage['main_menu_field12_name'] )
                ->set_option_value( '1' )
                ->set_help_text( $langService->optionPage['main_menu_field12_description'] ),

            Field::make( 'checkbox', 'calendar_plugin_end_time_on_grid', $langService->optionPage['main_menu_field4_name'] )
                ->set_option_value( '1' )
                ->set_help_text( $langService->optionPage['main_menu_field4_description'] ),

            Field::make( 'text', 'calendar_plugin_recipients', $langService->optionPage['main_menu_field5_name'] )
                ->set_attribute( 'placeholder', 'email@email.com' )
                ->set_help_text( $langService->optionPage['main_menu_field5_description'] ),

            Field::make( 'textarea', 'calendar_plugin_message_success', $langService->optionPage['main_menu_field6_name'] )
                ->set_attribute( 'placeholder', $langService->optionPage['main_menu_textarea_field_placeholder'] )
                ->set_rows( 3 )
                ->set_help_text( $langService->optionPage['main_menu_field6_description'] ),

            Field::make( 'textarea', 'calendar_plugin_message_error', $langService->optionPage['main_menu_field7_name'] )
                ->set_attribute( 'placeholder', $langService->optionPage['main_menu_textarea_field_placeholder'] )
                ->set_rows( 3 )
                ->set_help_text( $langService->optionPage['main_menu_field7_description'] ),

            Field::make( 'textarea', 'calendar_plugin_reservation_send-message', $langService->optionPage['main_menu_field8_name'] )
                ->set_attribute( 'placeholder', $langService->optionPage['main_menu_textarea_field_placeholder'] )
                ->set_rows( 3 )
                ->set_help_text( $langService->optionPage['main_menu_field8_description'] ),

            Field::make( 'select', 'calendar_plugin_start_at', $langService->optionPage['main_menu_field9_name'] )
            ->set_options([
                '00:00' => '00:00', '01:00' => '01:00', '02:00' => '02:00', '03:00' => '03:00',
                '04:00' => '04:00', '05:00' => '05:00', '06:00' => '06:00', '07:00' => '07:00',
                '08:00' => '08:00', '09:00' => '09:00', '10:00' => '10:00', '11:00' => '11:00',
                '12:00' => '12:00', '13:00' => '13:00', '14:00' => '14:00', '15:00' => '15:00',
                '16:00' => '16:00', '17:00' => '17:00', '18:00' => '18:00', '19:00' => '19:00',
                '20:00' => '20:00', '21:00' => '21:00', '22:00' => '22:00', '23:00' => '23:00',
            ])
            ->set_required( true ),

            Field::make( 'select', 'calendar_plugin_end_at', $langService->optionPage['main_menu_field10_name'] )
            ->set_options([
                '00:00' => '00:00', '01:00' => '01:00', '02:00' => '02:00', '03:00' => '03:00',
                '04:00' => '04:00', '05:00' => '05:00', '06:00' => '06:00', '07:00' => '07:00',
                '08:00' => '08:00', '09:00' => '09:00', '10:00' => '10:00', '11:00' => '11:00',
                '12:00' => '12:00', '13:00' => '13:00', '14:00' => '14:00', '15:00' => '15:00',
                '16:00' => '16:00', '17:00' => '17:00', '18:00' => '18:00', '19:00' => '19:00',
                '20:00' => '20:00', '21:00' => '21:00', '22:00' => '22:00', '23:00' => '23:00',
            ])
            ->set_required( true ),

            Field::make( 'select', 'calendar_plugin_interval', $langService->optionPage['main_menu_field11_name'] )
            ->set_options([
                '15' => '00:15', '30' => '00:30', '45' => '00:45', '60' => '01:00', 
            ])
            ->set_required( true ),
        ]);

    Container::make( 'theme_options', $langService->optionPage['activity_place_title'] )
        ->set_page_parent( $mainMenu )
        ->set_page_file( 'calendar-options-places' )
        ->add_fields($fields);

    Container::make( 'theme_options', $langService->optionPage['calendar_grid_data_title'] )
        ->set_page_parent( $mainMenu )
        ->set_page_file( 'calendar-options-activities' )
        ->add_fields([
            Field::make('complex', 'calendar_plugin_data', $langService->optionPage['calendar_grid_data_description'])
                ->set_layout( 'tabbed-horizontal' )
                ->add_fields([
                    Field::make( 'hidden', 'activity_hidden_id', __('ID') ),
                    Field::make( 'text', 'activity_name', $langService->optionPage['calendar_grid_data_field1_name'] )
                        ->set_required( true ),
                    Field::make( 'time', 'activity_start_at', $langService->optionPage['calendar_grid_data_field2_name'] )
                        ->set_storage_format('H:i')
                        ->set_required( true )
                        ->set_picker_options(['time_24hr' => true, 'altFormat' => 'H:i', 'enableSeconds' => false]),
                    Field::make( 'time', 'activity_end_at', $langService->optionPage['calendar_grid_data_field3_name'] )
                        ->set_storage_format('H:i')
                        ->set_required( true )
                        ->set_picker_options(['time_24hr' => true, 'altFormat' => 'H:i', 'enableSeconds' => false]),
                    Field::make( 'checkbox', 'activity_cyclic', $langService->optionPage['calendar_grid_data_field4_name'] )
                        ->set_option_value( '1' ),
                    Field::make( 'date', 'activity_date', $langService->optionPage['calendar_grid_data_field5_name'])
                        ->set_help_text( $langService->optionPage['calendar_grid_data_field5_description'] )
                        ->set_required( true )
                        ->set_picker_options([
                            'minDate' => date('Y-m-d'),
                            'monthSelectorType' => 'dropdown',
                            "firstDayOfWeek" => 1,
                            'altFormat' => 'F j, Y',
                            'altInput' => true,
                            'locale' => 'pl'
                        ])
                        ->set_conditional_logic([
                            [
                                'field' => 'activity_cyclic',
                                'value' => false,
                            ]
                        ]),
                    Field::make( 'multiselect', 'activity_day', $langService->optionPage['calendar_grid_data_field6_name'] )
                        ->set_options([
                            1 => $langService->days[0],
                            2 => $langService->days[1],
                            3 => $langService->days[2],
                            4 => $langService->days[3],
                            5 => $langService->days[4],
                            6 => $langService->days[5],
                            7 => $langService->days[6]
                        ])
                        ->set_required( true )
                        ->set_conditional_logic([
                            [
                                'field' => 'activity_cyclic',
                                'value' => true,
                            ]
                        ]),
                    Field::make( 'color', 'activity_bg_color', $langService->optionPage['calendar_grid_data_field7_name'] )
                        ->set_help_text( $langService->optionPage['calendar_grid_data_field7_description'] ),
                    Field::make( 'select', 'activity_type', $langService->optionPage['calendar_grid_data_field8_name'] )
                        ->set_options( $optionsPageService->get_activity_types() )
                        ->set_help_text( $langService->optionPage['calendar_grid_data_field8_description'] )
                        ->set_required( true ),
                    Field::make( 'text', 'activity_slot', $langService->optionPage['calendar_grid_data_field9_name'] )
                        ->set_attribute( 'type', 'number' )
                        ->set_attribute( 'min', 1 )
                        ->set_value(1)
                        ->set_required( true )
                        ->set_help_text( $langService->optionPage['calendar_grid_data_field9_description'] ),
                ]),
        ]);
}
