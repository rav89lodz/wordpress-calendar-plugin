<?php

namespace CalendarPlugin\src\classes\services;

use CalendarPlugin\src\classes\forms\CalendarHorizontalForm;
use CalendarPlugin\src\classes\forms\CalendarOneDayForm;
use CalendarPlugin\src\classes\forms\CalendarVerticalForm;
use CalendarPlugin\src\classes\models\CalendarModel;

class CalendarService
{
    public $calendar;
    public $showLeftArrows;
    public $currentDayName;
    private $shortCode;
    private $layout;

    /**
     * Constructor
     *
     * @param string|int monthNumber
     * @param string startDate
     * @param mixed shortCode
     * @return void
     */
    public function __construct($monthNumber = null, $startDate = null, $shortCode = null)
    {
        $this->calendar = new CalendarModel($monthNumber, $startDate);
        $this->showLeftArrows = $this->show_left_arrows($monthNumber, $startDate);
        $this->shortCode = $shortCode;
        $this->currentDayName = $this->set_current_day_name();
    }

    private function set_current_day_name() {
        $langService = new LanguageService;
        $days = $langService->days;

        $currentDay = $this->calendar->get_cuttent_date();
        $currentWeekDay = date('w', strtotime($currentDay));

        if($currentWeekDay == 0) {
            $currentWeekDay = 7;
        }
        return $days[$currentWeekDay - 1];
    }

    /**
     * Create table header
     * 
     * @return void
     */
    public function create_table_header() {
        switch(true) {
            case $this->calendar->get_calendar_one_day_view():
                $this->layout = new CalendarOneDayForm($this->calendar, $this->shortCode);
                $this->layout->create_one_day_table_header();
                break;
            case $this->calendar->get_horizontal_calendar_grid():
                $this->layout = new CalendarHorizontalForm($this->calendar, $this->shortCode);
                $this->layout->create_horizontal_table_header();
                break;
            default:
                $this->layout = new CalendarVerticalForm($this->calendar, $this->shortCode);
                $this->layout->create_vertical_table_header();
                break;
        }
    }

    /**
     * Create table content
     * 
     * @return void
     */
    public function create_table_content() {
        switch(true) {
            case $this->calendar->get_calendar_one_day_view():
                $this->layout->create_one_day_table_content();
                break;
            case $this->calendar->get_horizontal_calendar_grid():
                $this->layout->create_horizontal_table_content();
                break;
            default:
                $this->layout->create_vertical_table_content();
                break;
        }
    }

    /**
     * Check left arrows can be shown
     * 
     * @param string month
     * @param string startDate
     * @return array
     */
    private function show_left_arrows($month, $startDate) {
        if($month !== null) {
            if(date('Y-m', strtotime($month)) > date('Y-m')) {
                return [true, true];
            }
        }
        if($startDate !== null && $startDate > date('Y-m-d')) {
            if(date('Y-m', strtotime($startDate)) > date('Y-m')) {
                return [true, true];
            }
            return [false, true];
        }
        return [false, false];
    }
}