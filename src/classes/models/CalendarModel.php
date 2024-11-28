<?php

namespace CalendarPlugin\src\classes\models;

use CalendarPlugin\src\classes\services\LanguageService;

class CalendarModel
{
    private $currentDate;
    private $currentTime;
    private $currentMondayDate;
    private $firstHourOnCalendar;
    private $lastHourOnCalendar;
    private $currentMonthName;
    private $calendarInterval;
    private $calendarReservation;
    private $durationOnGrid;
    private $placeActivityOnGrid;
    private $endTimeOnGrid;
    private $fulentCalendarGrid;

    public function __construct($monthNumber = null, $startDate = null) {
        if($monthNumber !== null && $startDate === null) {
            $this->currentMondayDate = $this->get_first_monday($monthNumber);
        }
        else {
            $this->currentMondayDate = $this->set_current_monday_date_form_param($startDate);
        }

        if($monthNumber === null && $startDate !== null) {
            $this->currentMonthName = $this->set_current_month_name($startDate);
        }
        else {
            $this->currentMonthName = $this->set_current_month_name($monthNumber);
        }
        $this->currentDate = $this->set_current_date();
        $this->currentTime = $this->set_current_time();
        $this->firstHourOnCalendar = $this->set_first_hour_on_calendar();
        $this->lastHourOnCalendar = $this->set_last_hour_on_calendar();
        $this->calendarInterval = $this->set_calendar_interval_option();
        $this->calendarReservation = $this->set_calendar_option('calendar_plugin_make_rsv_by_calendar');
        $this->durationOnGrid = $this->set_calendar_option('calendar_plugin_duration_time_on_grid');
        $this->placeActivityOnGrid = $this->set_calendar_option('calendar_plugin_activity_place_on_grid');
        $this->endTimeOnGrid = $this->set_calendar_option('calendar_plugin_end_time_on_grid');
        $this->fulentCalendarGrid = $this->set_calendar_option('calendar_plugin_fluent_calendar_grid');
    }

    public function get_cuttent_date() {
        return $this->currentDate;
    }

    public function get_cuttent_time() {
        return $this->currentTime;
    }

    public function get_cuttent_monday_date() {
        return $this->currentMondayDate;
    }

    public function get_first_hour_on_calendar() {
        return $this->firstHourOnCalendar;
    }

    public function get_last_hour_on_calendar() {
        return $this->lastHourOnCalendar;
    }

    public function get_monday_plus_days($days) {
        return date('Y-m-d', strtotime($this->currentMondayDate . "+$days days"));
    }

    public function get_cuttent_month_name() {
        return $this->currentMonthName;
    }

    public function get_calendar_interval() {
        return $this->calendarInterval;
    }

    public function get_calendar_reservation() {
        return $this->calendarReservation;
    }

    public function get_duration_on_grid() {
        return $this->durationOnGrid;
    }

    public function get_place_activity_on_grid() {
        return $this->placeActivityOnGrid;
    }

    public function get_end_time_on_grid() {
        return $this->endTimeOnGrid;
    }

    public function get_fluent_calendar_grid() {
        return $this->fulentCalendarGrid;
    }

    private function set_current_monday_date_form_param($startDate = null) {
        if($startDate == null) {
            return $this->set_current_monday_date();
        }

        $timestamp = strtotime($startDate);
        $dayOfWeek = date('N', $timestamp);
        $difference = ($dayOfWeek - 1) * 86400; // 86400 sekund = 1 dzień
        $mondayTimestamp = $timestamp - $difference;
        return date('Y-m-d', $mondayTimestamp);
    }

    private function get_first_monday($dateString) {
        list($year, $month, $day) = explode('-', $dateString);
        $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
        $dayOfWeek = date('w', $firstDayOfMonth);
        $offset = ($dayOfWeek == 0) ? 1 : (8 - $dayOfWeek);
        $firstMonday = mktime(0, 0, 0, $month, 1 + $offset, $year);
        return date('Y-m-d', $firstMonday);
    }
    
    private function set_calendar_option($option) {
        if(empty(get_calendar_plugin_options($option))) {
            return false;
        }
        return true;
    }

    private function set_calendar_interval_option() {
        if(empty(get_calendar_plugin_options('calendar_plugin_interval'))) {
            return 60;
        }
        return get_calendar_plugin_options('calendar_plugin_interval');
    }

    private function set_current_date() {
        return date('Y-m-d');
    }

    private function set_current_time() {
        return date('H:i:s');
    }

    private function set_current_monday_date() {
        $monday = strtotime('next Monday -1 week');
        return date('w', $monday) == date('w') ? date('Y-m-d', strtotime(date("Y-m-d", $monday)." +7 days")) : date('Y-m-d', $monday);
    }

    private function set_first_hour_on_calendar() {
        global $wpdb;
        $obj = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = %s", '_calendar_plugin_start_at' ));
        return $obj !== null ? $obj->option_value : "05:00";
    }

    private function set_last_hour_on_calendar() {
        global $wpdb;
        $obj = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = %s", '_calendar_plugin_end_at' ));
        return $obj !== null ? $obj->option_value : "23:30";
    }

    private function set_current_month_name($month = null) {
        $service = new LanguageService;
        $months = $service->months;
        if($month !== null) {
            $monthNumber = date('m', strtotime($month));
            if($monthNumber > 0 && $monthNumber < 13) {
                return $months[$monthNumber - 1];
            }
        }
        return $months[date('m') - 1];
    }
}