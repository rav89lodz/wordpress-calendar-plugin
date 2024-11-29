<?php

namespace CalendarPlugin\src\classes\services;

class LanguageService
{
    public $addActivityFriendlyNames;
    public $reservationFriendlyNames;
    public $addActivityMenu;
    public $reservationMenu;
    public $addActivityMessage;
    public $reservationMessage;
    public $calendarLabels;
    public $modalFormFriendlyNames;
    public $days;
    public $months;
    public $optionPage;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct() {
        switch(get_locale()) {
            case 'pl_PL':
                $this->set_calendar_plugin_language('pl');
                break;
            default:
                $this->set_calendar_plugin_language('en');
                break;
        }    
    }

    /**
     * Set properties for current language
     */
    private function set_calendar_plugin_language($lang) {
        $config = include CALENDAR_PLUGIN_PATH . '/src/lang/' . $lang . '.php';

        $this->addActivityFriendlyNames = $config['addActivityFriendlyNames'];
        $this->reservationFriendlyNames = $config['reservationFriendlyNames'];
        $this->addActivityMenu = $config['addActivityMenu'];
        $this->reservationMenu = $config['reservationMenu'];
        $this->addActivityMessage = $config['addActivityMessage'];
        $this->reservationMessage = $config['reservationMessage'];
        $this->calendarLabels = $config['calendarLabels'];
        $this->modalFormFriendlyNames = $config['modalFormFriendlyNames'];
        $this->days = $config['days'];
        $this->months = $config['months'];
        $this->optionPage = $config['optionPage'];
    }    
}