<?php

namespace CalendarPlugin\src\classes;

class Utils
{
    private $adminEmail;
    private $adminName;

    public function __construct() {
        $this->adminEmail = strtolower(trim(get_calendar_plugin_options('calendar_plugin_recipients') ?? get_bloginfo('admin_email')));
        $this->adminName = get_bloginfo('name');
    }

    public function set_success_error_message_with_code($name, $code, $messageSuccess) {
        if($messageSuccess === false) {
            $message = get_calendar_plugin_options('calendar_plugin_message_error') ?? 'Wiadmość została wysłana. Rezerwacja odrzucona';
        }
        else {
            $message = get_calendar_plugin_options('calendar_plugin_message_success') ?? 'Wiadmość została wysłana. Rezerwacja dokonana';
        }
        $message = $this->set_up_polish_characters($message);
        return ["message" => str_replace('{name}', $name, $message), "code" => $code];
    }

    public function array_of_object_to_flat_array($array) {
        $toReturn = [];
        if(isset($array) && is_array($array)) {
            foreach($array as $key => $value) {
                if (count($value) > 0) {
                    $value = array_values($value);
                    $temp_key = strtolower(str_replace(" ", "_", $value[0]));
                    $temp_key = $this->remove_polish_letters($temp_key);
                    $toReturn[$temp_key] = $value[0];
                }
            }
        }
        return $toReturn;
    }

    public function send_email_with_store_data($message, $subject, $replayTo) {
        $headers = $this->set_custom_headers($replayTo);
        wp_mail(
            $this->adminEmail,
            $subject,
            $message,
            $headers
        );
    }

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
            'ż'
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
            'z'
        ];

        return str_replace($polishLetters, $toReplace, $string);
    }

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

    private function set_custom_headers($replayTo) {
        return [
            "From: {$this->adminName} <{$this->adminEmail}>",
            "Replay-to: {$replayTo['name']} <{$replayTo['email']}>",
            "Content-Type: text/html",
        ];
    }
}