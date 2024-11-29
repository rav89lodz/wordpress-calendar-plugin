<?php

namespace CalendarPlugin\src\classes\services;

use CalendarPlugin\src\classes\forms\CalendarHorizontalForm;
use CalendarPlugin\src\classes\forms\CalendarVerticalForm;
use CalendarPlugin\src\classes\models\CalendarModel;

class CalendarService
{
    public $calendar;
    public $showLeftArrows;
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
    }

    /**
     * Create table header
     * 
     * @return void
     */
    public function create_table_header() {
        if($this->calendar->get_horizontal_calendar_grid() === true) {
            $this->layout = new CalendarHorizontalForm($this->calendar, $this->shortCode);
            $this->layout->create_horizontal_table_header();
        }
        else {
            $this->layout = new CalendarVerticalForm($this->calendar, $this->shortCode);
            $this->layout->create_vertical_table_header();
        }
    }

    /**
     * Create table content
     * 
     * @return void
     */
    public function create_table_content() {
        if($this->calendar->get_horizontal_calendar_grid() === true) {
            $this->layout->create_horizontal_table_content();
        }
        else {
            $this->layout->create_vertical_table_content();
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