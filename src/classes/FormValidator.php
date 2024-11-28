<?php

namespace CalendarPlugin\src\classes;

class FormValidator
{
    public function sanitize_data_array($data) {
        $array = [];
        foreach ((array) $data as $label => $value) {
            $array[$label] = $this->validate($value, $label);
        }
        return $array;
    }

    public function validate($field, $type) {
        switch ($type) {
            case 'email':
                return sanitize_email($field);
            case 'message':
                return sanitize_textarea_field($field);
            default:
                return sanitize_text_field($field);
        }
    }

    /**
     * Check value is an valid email
     * 
     * @param string email
     * @return bool
     */
    public function is_valid_email($email) {
        if($email !== null && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }

    /**
     * Check value is a valid url
     * 
     * @param string url
     * @return bool
     */
    public function is_valid_url($url) {
        if($url !== null && preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$url)) {
            return true;
        }
        return false;
    }

    /**
     * Check value is an valid array
     * 
     * @param array array
     * @return bool
     */
    public function is_valid_array($array)
    {
        if($array !== null && is_array($array) && count($array) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Check value is a valid number
     * 
     * @param mixed number
     * @return bool
     */
    public function is_valid_number($number)
    {
        if($number !== null && is_numeric($number)) {
            return true;
        }
        return false;
    }

    /**
     * Check value is a valid string
     * 
     * @param string value
     * @return bool
     */
    public function is_valid_string($string)
    {
        if($string !== null && is_string($string) && ! empty($string)) {
            return true;
        }
        return false;
    }

    /**
     * Check value is a valid boolean
     * 
     * @param bool value
     * @return bool
     */
    public function is_valid_bool($bool)
    {
        if($bool !== null && is_bool($bool)) {
            return true;
        }
        return false;
    }

    /**
     * Check value has correct date format
     * 
     * @param mixed value
     * @return bool
     */
    public function is_valid_date($date)
    {
        if($date !== null && preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
            $date = explode('-', $date);
            if(count($date) === 3 && checkdate($date[1], $date[2], $date[0])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check value is in array
     * 
     * @param mixed value
     * @param array array
     * @return bool
     */
    public function is_in_array($value, $array)
    {
        if($value !== null && $array !== null && count($array) > 0 && is_string($value) && ! empty($value) && in_array($value, $array)) {
            return true;
        }
        return false;
    }

    /**
     * Check value is correct timestamp
     * 
     * @param mixed value
     * @return bool
     */
    public function is_valid_timestamp($timestamp)
    {
        if($timestamp !== null && preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) [0-9]{2}:[0-9]{2}:[0-9]{2}$/", $timestamp)) {
            return true;
        }
        return false;
    }
}