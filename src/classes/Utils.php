<?php

namespace CalendarPlugin\src\classes;

use CalendarPlugin\src\classes\models\MessageModel;
use CalendarPlugin\src\classes\services\LanguageService;

class Utils
{
    private $adminEmail;
    private $adminName;
    private $model;
    private $service;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct() {
        $this->adminEmail = strtolower(trim(get_calendar_plugin_options('calendar_plugin_recipients') ?? get_bloginfo('admin_email')));
        $this->adminName = get_bloginfo('name');
        $this->model = new MessageModel;
        $this->service = new LanguageService;
    }

    /**
     * Set success or error message with response code
     * 
     * @param string name
     * @param int code
     * @param int message option
     * @return array
     */
    public function set_success_error_message_with_code($name, $code, $messageOption = 99) {
        switch($messageOption) {
            case 1:
                $message = $this->model->get_message_success() ?? $this->service->calendarLabels['default_success_message'];
                break;
            case 2:
                $message = $this->model->get_message_form_sended() ??  $this->service->calendarLabels['default_success_message'];
                break;
            default:
                $message = $this->model->get_message_error() ??  $this->service->calendarLabels['default_error_message'];
                break;
        }
        
        $message = $this->set_up_polish_characters($message);
        return ["message" => str_replace('{name}', $name, $message), "code" => $code];
    }

    /**
     * Make array of objects to flat array
     * 
     * @param array array
     * @return array
     */
    public function array_of_object_to_flat_array($array) {
        $toReturn = [];

        if(isset($array) && is_array($array)) {
            for($i = 0; $i < count($array); $i += 2) {
                if(! isset($array[$i + 1]) || $array[$i + 1]['option_value'] === null) {
                    continue;
                }
                $toReturn[$array[$i + 1]['option_value']] = $array[$i]['option_value'];
            }
        }

        return $toReturn;
    }

    /**
     * Send email with data
     * 
     * @param string message
     * @param string subject
     * @param string replayTo
     * @return bool
     */
    public function send_email_with_data($message, $subject, $replayTo) {
        $headers = $this->set_custom_headers($replayTo);
        return wp_mail(
            $this->adminEmail,
            $subject,
            $message,
            $headers
        );
    }

    /**
     * Set up polish html characters
     * 
     * @param string string
     * @return string
     */
    public function set_up_polish_characters($string) {
        $specialChars = [
            '\u0105', # ą
            '\u0107', # ć
            '\u0119', # ę
            '\u0142', # ł
            '\u0144', # ń
            '\u00f3', # ó
            '\u015b', # ś
            '\u017a', # ź
            '\u017c', # ż
            '\u0104', # Ą
            '\u0106', # Ć
            '\u0118', # Ę
            '\u0141', # Ł
            '\u0143', # Ń
            '\u00d3', # Ó
            '\u015a', # Ś
            '\u0179', # Ż
            '\u017b', # Ż
        ];
    
        $polishHtmlCodes = [
            '&#261;', # ą
            '&#263;', # ć
            '&#281;', # ę
            '&#322;', # ł
            '&#324;', # ń
            '&#243;', # ó
            '&#347;', # ś
            '&#378;', # ź
            '&#380;', # ż
            '&#260;', # Ą
            '&#262;', # Ć
            '&#280;', # Ę
            '&#321;', # Ł
            '&#323;', # Ń
            '&#211;', # Ó
            '&#346;', # Ś
            '&#377;', # Ż
            '&#379;', # Ż
        ];

        $result = str_replace($specialChars, $polishHtmlCodes, json_encode($string));
        return json_decode($result);
    }
    
    /**
     * Replace polish letters to conventional letters
     * 
     * @param string string
     * @return string
     */
    public function remove_polish_letters($string) {
        $polishLetters = [
            'ą',
            'ć',
            'ę',
            'ł',
            'ń',
            'ó',
            'ś',
            'ź',
            'ż',
            'Ą',
            'Ć',
            'Ę',
            'Ł',
            'Ń',
            'Ó',
            'Ś',
            'Ź',
            'Ż'
        ];

        $toReplace = [
            'a',
            'c',
            'e',
            'l',
            'n',
            'o',
            's',
            'z',
            'z',
            'A',
            'C',
            'E',
            'L',
            'N',
            'O',
            'S',
            'Z',
            'Z'
        ];
        
        return str_replace($polishLetters, $toReplace, $string);
    }

    /**
     * Prepare shot code for current page
     * 
     * @param mixed shortCodes
     * @return array|null
     */
    public function prepare_current_short_codes($shortCodes) {
        $preDefinedToSkip = ['[calendar-grid1]', '[contact-form-calendar1]'];
        $toSet = null;
        if(str_contains($shortCodes, "|*|")) {
            $shortCodes = str_replace('|*|', '], [', $shortCodes);
        }
        preg_match_all('/\[([^\]]+)\]/', $shortCodes, $matches);
        
        if(count($matches) > 0) {
            $toSet = [];
            foreach($matches[0] as $short) {
                if(in_array($short, $preDefinedToSkip)) {
                    continue;
                }
                $toSet[] = str_replace(['[', ']'], "", $short);
            }
        }
        return $toSet;
    }

    /**
     * Set custom headers for email
     * 
     * @param string replayTo
     * @return array
     */
    private function set_custom_headers($replayTo) {
        return [
            "From: {$this->adminName} <{$this->adminEmail}>",
            "Replay-to: {$replayTo['name']} <{$replayTo['email']}>",
            "Content-Type: text/html",
        ];
    }
}