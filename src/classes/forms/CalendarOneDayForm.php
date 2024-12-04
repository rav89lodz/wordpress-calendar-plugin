<?php

namespace CalendarPlugin\src\classes\forms;

class CalendarOneDayForm extends CalendarForm
{
    private $calendar;
    private $shortCode;
    private $currentWeekDay;
    public $currentDayName;

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
    }

    /**
     * Create table header
     * 
     * @return void
     */
    public function create_one_day_table_header() {
        $currentDay = $this->calendar->get_cuttent_date();
        $this->currentWeekDay = date('w', strtotime($currentDay));
        if($this->currentWeekDay == 0) {
            $this->currentWeekDay = 7;
        }

        echo '<input type="hidden" id="grid_vector" value="V">';
        $this->print_header_row($currentDay, 1);
        $this->datesOnThisWeek[] = $currentDay;
    }

    /**
     * Create table content
     * 
     * @return void
     */
    public function create_one_day_table_content() {
        $activities = $this->get_all_activities_models($this->shortCode);

        $endTime = strtotime($this->calendar->get_last_hour_on_calendar());
        $interval = "+" . $this->calendar->get_calendar_interval() . " minutes";
        
        $currentTime = strtotime($this->calendar->get_first_hour_on_calendar());
        while ($currentTime <= $endTime) {
            $activitiesGrouped = $this->group_activities_by_day_and_hour($activities, date('H:i', $currentTime));

            $this->print_row_with_data($activitiesGrouped, date('H:i', $currentTime), $this->currentWeekDay);

            for($min = 5; $min < $this->calendar->get_calendar_interval() - 4; $min += 5) {
                $minute = date('H:i', strtotime("+$min minutes", $currentTime));
                if (isset($activitiesGrouped[$minute])) {
                    $this->print_row_with_data($activitiesGrouped, $minute, $this->currentWeekDay);
                }
            }

            $currentTime = strtotime($interval, $currentTime);
        }
    }

    /**
     * Group activities by day and hour
     * 
     * @param array activities
     * @param string currentTime
     * @return array
     */
    private function group_activities_by_day_and_hour($activities, $currentTime) {
        $groupedActivities = [];
    
        foreach ($activities as $activity) {
            if ($activity->get_is_cyclic() == '1') {
                foreach ($activity->get_day() as $dayOfWeek) {
                    $groupedActivities = $this->attach_activity_to_array($groupedActivities, $activity, $currentTime, $dayOfWeek);
                }
            }
            else {
                if (in_array($activity->get_date(), $this->datesOnThisWeek)) {
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
            $groupedActivities[$currentTime][$dayOfWeek][] = $activity;
        }
        else {
            $minuteHour = date('H:i', strtotime($activity->get_start_at()));
            if (!isset($groupedActivities[$minuteHour])) {
                $groupedActivities[$minuteHour] = [];
            }
            $groupedActivities[$minuteHour][$dayOfWeek][] = $activity;
        }
        return $groupedActivities;
    }

    /**
     * Print table row with data
     * 
     * @param array groupedActivities
     * @param string currentTime
     * @param int day
     * @return void
     */
    private function print_row_with_data($groupedActivities, $currentTime, $day) {
        echo "<tr>";
        echo "<td style='width:15%'>" . $currentTime . "</td>";
    
        if (! empty($groupedActivities[$currentTime][$day])) {
            echo "<td><div class='flex-cell'>";
            foreach ($groupedActivities[$currentTime][$day] as $activity) {
                $this->get_cell_with_activity($this->calendar, $activity, $currentTime, 1);
            }
            echo "</div></td>";
        }
        else {
            echo "<td></td>";
        }
        echo "</tr>";
    }

    /**
     * Print header row
     * 
     * @param string date
     * @param string id
     * @return void
     */
    private function print_header_row($date, $id) {
        $id = "header_$id";
        echo "<span style='display:none' id='$id'>" . $date . "</span>";
    }
}