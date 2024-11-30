<?php

namespace CalendarPlugin\src\classes\forms;

class CalendarHorizontalForm extends CalendarForm
{
    private $calendar;
    private $shortCode;
    private $hours;
    private $activities;

    /**
     * Constructor
     *
     * @param object|null calendar
     * @param mixed shortCode
     * @return void
     */
    public function __construct($calendar = null, $shortCode = null )
    {
        parent::__construct();
        $this->calendar = $calendar;        
        $this->shortCode = $shortCode;
        $this->hours = [];
        $this->activities = $this->get_all_activities_models($this->shortCode);
    }

    /**
     * Create table header
     * 
     * @return void
     */
    public function create_horizontal_table_header() {
        $currentTime = strtotime($this->calendar->get_first_hour_on_calendar());
        $endTime = strtotime($this->calendar->get_last_hour_on_calendar());
        $interval = "+" . $this->calendar->get_calendar_interval() . " minutes";

        $this->hours = $this->get_all_start_time_values();

        while ($currentTime <= $endTime) {
            $this->hours[] = date('H:i', $currentTime);
            $currentTime = strtotime($interval, $currentTime);
        }

        $this->hours = array_unique($this->hours);
        sort($this->hours);

        echo '<tr><th scope="col"></th>';
        foreach($this->hours as $hour) {
            echo '<th scope="col">' . $hour . '</th>';
        }
        echo '</tr>';
    }

    /**
     * Create table content
     * 
     * @return void
     */
    public function create_horizontal_table_content() {        
        $days = $this->langService->days;
        $currentDay = $this->calendar->get_cuttent_monday_date();

        for($i = 0; $i < 7; $i ++) {
            echo '<tr><th scope="col">';
            if($i > 0) {
                $currentDay = $this->calendar->get_monday_plus_days($i);
            }
            $this->print_horizontal_header_row($days[$i], $currentDay, $i + 1);
            echo "</th>";
            foreach($this->hours as $hour) {
                $activitiesGrouped = $this->group_activities_by_day_and_hour($this->activities, $hour, $currentDay);

                $this->print_row_horizontal_with_data($activitiesGrouped, $hour, $i + 1);
            }
            echo "</tr>";
        }
    }

    /**
     * Group activities by day and hour
     * 
     * @param array activities
     * @param string currentTime
     * @return array
     */
    private function group_activities_by_day_and_hour($activities, $currentTime, $currentDay) {
        $groupedActivities = [];

        foreach ($activities as $activity) {
            if ($activity->get_is_cyclic() == '1') {
                foreach ($activity->get_day() as $dayOfWeek) {
                    $groupedActivities = $this->attach_activity_to_array($groupedActivities, $activity, $currentTime, $dayOfWeek);
                }
            }
            else {
                if ($activity->get_date() == $currentDay) {
                    $dayOfWeek = date('N', strtotime($activity->get_date()));
                    $groupedActivities = $this->attach_activity_to_array($groupedActivities, $activity, $currentTime, $dayOfWeek);
                }
            }
        }
    
        return $groupedActivities;
    }

    /**
     * Attach single activity to passed array on specific position
     * 
     * @param array groupedActivities
     * @param object|null activity
     * @param string currentTime
     * @param string|int dayOfWeek
     * @return array
     */
    private function attach_activity_to_array($groupedActivities, $activity, $currentTime, $dayOfWeek) {
        if ($activity->get_start_at() == $currentTime) {
            $groupedActivities[$dayOfWeek][$currentTime][] = $activity;
        }
        else {
            $minuteHour = date('H:i', strtotime($activity->get_start_at()));
            if (!isset($groupedActivities[$dayOfWeek][$minuteHour])) {
                $groupedActivities[$dayOfWeek][$minuteHour] = [];
            }
            $groupedActivities[$dayOfWeek][$minuteHour][] = $activity;
        }
        return $groupedActivities;
    }

    /**
     * Print table row with data
     * 
     * @param array groupedActivities
     * @param string currentTime
     * @return void
     */
    private function print_row_horizontal_with_data($groupedActivities, $currentTime, $day) {
        if (! empty($groupedActivities[$day][$currentTime])) {
            echo "<td>";
            foreach ($groupedActivities[$day][$currentTime] as $activity) {
                $this->get_cell_with_activity($this->calendar, $activity, $currentTime, $day);
            }
            echo "</td>";
        }
        else {
            echo "<td></td>";
        }
    }

    /**
     * Print header row
     * 
     * @param string dayName
     * @param string date
     * @param string id
     * @return void
     */
    private function print_horizontal_header_row($dayName, $date, $id) {
        $id = "header_$id";
        echo "<p>" . $dayName . "</p><span id='$id'>" . $date . "</span>";
    }

    /**
     * Get all start time values of all activities on current week
     * 
     * @return array
     */
    private function get_all_start_time_values() {
        $toReturn = [];
        $this->datesOnThisWeek[] = $this->calendar->get_cuttent_monday_date();
        
        for($i = 1; $i < 7; $i ++) {
            $this->datesOnThisWeek[] = $this->calendar->get_monday_plus_days($i);
        }

        foreach($this->activities as $activity) {
            if(in_array($activity->get_date(), $this->datesOnThisWeek)) {
                $toReturn[] = $activity->get_start_at();
            }
            if($activity->get_is_cyclic() !== null && ! empty($activity->get_is_cyclic())) {
                $toReturn[] = $activity->get_start_at();
            }
        }

        return $toReturn;
    }
}