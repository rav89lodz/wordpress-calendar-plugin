<?php

if (! defined('ABSPATH')) {
    exit;
}

use CalendarPlugin\src\classes\services\OptionsPageService;
use Carbon_Fields\Carbon_Fields;
use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action('after_setup_theme', 'load_carbon_fields_for_calendar_plugin');
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

    $fields = [];
    foreach($optionsPageService->get_activity_types() as $key => $value) {
        add_shortcode($key, 'show_calendar_grid');
        $fields[] = Field::make( 'html', $key )
                    ->set_html( '<h2>[' . $key . ']</h2><p>Użyj tego kodu (short_code), żeby wyświetlić na stronie kalendarz z danymi dla: ' . $value . '</p>' );

    }

    $fields[] = Field::make( 'complex', 'calendar_plugin_types', __( 'Zdefiniuj miejsca, w których odbywają się zajęcia' ) )->add_fields( array(
                    Field::make( 'text', 'type_name', __( 'Nazwa miejsca' ) )
                        ->set_required( true )
                        ->set_help_text( 'Zdefiniuj nazwy miejesc, gdzie odbywają się zajęcia. Np. hala sportowa, sala nr 1, pływalnia' ),
                    Field::make( 'hidden', 'type_key', "" ),
                ));

    $mainMenu = Container::make('theme_options', __( 'Ustawienia Rezerwacji'))
        ->set_page_menu_position(80)
        ->set_page_file( 'calendar-options-global' )
        ->set_icon('dashicons-calendar-alt')
        ->add_fields([
            Field::make( 'html', 'calendar_plugin_short_code' )
                ->set_html( '<h2>[calendar-grid1]</h2><p>Użyj tego kodu (short_code), żeby wyświetlić na stronie kalendarz z pełnymi danymi.</p>' ),

            Field::make( 'html', 'calendar_plugin_contact_form_short_code' )
                ->set_html( '<h2>[contact-form-calendar1]</h2><p>Użyj tego kodu (short_code), żeby wyświetlić formularz kontaktowy, dzięki któremu będziesz otrzymywać wiadomości, że ktoś chce zarezerwować termin w kalendarzu.</p>' ),

            Field::make( 'checkbox', 'calendar_plugin_make_rsv_by_calendar', __( 'Umożliwić zapis na zajęcia poprzez kliknięcie na kalendarzu' ) )
                ->set_option_value( '1' )
                ->set_help_text( 'Po kliknięciu na wybrane pole w kalendarzu pojawi się formularz zapisu na zajęcia. Formularz uwzględnia limit miejsc na zajęciach' ),

            Field::make( 'text', 'calendar_plugin_recipients', __( 'Adres e-mail' ) )
                ->set_attribute( 'placeholder', 'email@email.com' )
                ->set_help_text( 'Adres e-mail, na który będą wysłane powiadomienia' ),

            Field::make( 'textarea', 'calendar_plugin_message_success', __( 'Treść wiadomości dokonania rezerwacji' ) )
                ->set_attribute( 'placeholder', 'Wpisz treść wiadomości' )
                ->set_rows( 3 )
                ->set_help_text( 'Wpisz wiadomość, którą chcesz, aby nadawca otrzymał w przypdaku dokonania rezerwacji. Możesz użyć {name} jako zmiennej' ),

            Field::make( 'textarea', 'calendar_plugin_message_error', __( 'Treść wiadomości odrzucenia rezerwacji' ) )
                ->set_attribute( 'placeholder', 'Wpisz treść wiadomości' )
                ->set_rows( 3 )
                ->set_help_text( 'Wpisz wiadomość, którą chcesz, aby nadawca otrzymał w przypdaku odrzucenia rezerwacji. Możesz użyć {name} jako zmiennej' ),

            Field::make( 'select', 'calendar_plugin_start_at', __( 'Początkowa godzina w kalendarzu' ) )
            ->set_options([
                '00:00' => '00:00', '01:00' => '01:00', '02:00' => '02:00', '03:00' => '03:00',
                '04:00' => '04:00', '05:00' => '05:00', '06:00' => '06:00', '07:00' => '07:00',
                '08:00' => '08:00', '09:00' => '09:00', '10:00' => '10:00', '11:00' => '11:00',
                '12:00' => '12:00', '13:00' => '13:00', '14:00' => '14:00', '15:00' => '15:00',
                '16:00' => '16:00', '17:00' => '17:00', '18:00' => '18:00', '19:00' => '19:00',
                '20:00' => '20:00', '21:00' => '21:00', '22:00' => '22:00', '23:00' => '23:00',
            ])
            ->set_required( true ),

            Field::make( 'select', 'calendar_plugin_end_at', __( 'Końcowa godzina w kalendarzu' ) )
            ->set_options([
                '00:00' => '00:00', '01:00' => '01:00', '02:00' => '02:00', '03:00' => '03:00',
                '04:00' => '04:00', '05:00' => '05:00', '06:00' => '06:00', '07:00' => '07:00',
                '08:00' => '08:00', '09:00' => '09:00', '10:00' => '10:00', '11:00' => '11:00',
                '12:00' => '12:00', '13:00' => '13:00', '14:00' => '14:00', '15:00' => '15:00',
                '16:00' => '16:00', '17:00' => '17:00', '18:00' => '18:00', '19:00' => '19:00',
                '20:00' => '20:00', '21:00' => '21:00', '22:00' => '22:00', '23:00' => '23:00',
            ])
            ->set_required( true ),
        ]);

    Container::make( 'theme_options', __( 'Miejsca zajęć' ) )
        ->set_page_parent( $mainMenu )
        ->set_page_file( 'calendar-options-places' )
        ->add_fields($fields);

    Container::make( 'theme_options', __( 'Zajęcia w kalendarzu' ) )
        ->set_page_parent( $mainMenu )
        ->set_page_file( 'calendar-options-activities' )
        ->add_fields([
            Field::make('complex', 'calendar_plugin_data', __( 'Zdefiniuj kalendarz rezerwacji' ))
                ->set_layout( 'tabbed-horizontal' )
                ->add_fields([
                    Field::make( 'hidden', 'activity_hidden_id', __('ID') ),
                    Field::make( 'text', 'activity_name', __( 'Nazwa zajęć' ) )
                        ->set_required( true ),
                    Field::make( 'time', 'activity_start_at', __( 'Godzina rozpoczęcia' ) )
                        ->set_storage_format('H:i')
                        ->set_picker_options(['time_24hr' => true, 'altFormat' => 'H:i', 'enableSeconds' => false]),
                    Field::make( 'time', 'activity_duration', __( 'Czas trwania zajęć' ) )
                        ->set_storage_format('H:i')
                        ->set_picker_options(['time_24hr' => true, 'altFormat' => 'H:i', 'enableSeconds' => false]),
                    Field::make( 'checkbox', 'activity_cyclic', __( 'Czy zajęcia cykliczna' ) )
                        ->set_option_value( '1' ),
                    Field::make( 'date', 'activity_date', __( 'Data zajęcia' ))
                        ->set_help_text( 'Ustaw datę dla zajęcia występującego jednorazowo' )
                        ->set_required( true )
                        ->set_conditional_logic([
                            [
                                'field' => 'activity_cyclic',
                                'value' => false,
                            ]
                        ]),
                    Field::make( 'multiselect', 'activity_day', __( 'Wybierz dzień zajęć cyklicznych' ) )
                        ->set_options([
                            1 => 'Poniedziałek',
                            2 => 'Wtorek',
                            3 => 'Środa',
                            4 => 'Czwartek',
                            5 => 'Piątek',
                            6 => 'Sobota',
                            7 => 'Niedziela'
                        ])
                        ->set_required( true )
                        ->set_conditional_logic([
                            [
                                'field' => 'activity_cyclic',
                                'value' => true,
                            ]
                        ]),
                    Field::make( 'color', 'activity_bg_color', 'Kolor zajęć' )
                        ->set_help_text( 'Wybierz kolor, którym chcesz wyróżnić zajęcia w kalendarzu' ),
                    Field::make( 'select', 'activity_type', __( 'Wybierz typ zajęć' ) )
                        ->set_options( $optionsPageService->get_activity_types() )
                        ->set_help_text( 'Wybierz, dla którego pomieszczenia są zajęcia' )
                        ->set_required( true ),
                    Field::make( 'text', 'activity_slot', __( 'Ile miejsc na zajęcia' ) )
                        ->set_attribute( 'type', 'number' )
                        ->set_attribute( 'min', 1 )
                        ->set_value(1)
                        ->set_required( true )
                        ->set_help_text( 'Zdefiniuj ile razy można zarezerować zajęcia. Minimalna wartość to 1' ),
                ]),
        ]);
}
