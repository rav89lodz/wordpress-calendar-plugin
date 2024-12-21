<?php

return [
    'addActivityFriendlyNames' => [
        'add_activity_user_name' => 'Full name',
        'add_activity_user_email' => 'Email address',
        'add_activity_user_phone' => 'Phone number',
        'add_activity_date' => 'Proposed activity date',
        'add_activity_time_start' => 'Activity start time',
        'add_activity_time_end' => 'Activity end time',
        'add_activity_name' => 'Activity name',
    ],

    'reservationFriendlyNames' => [
        'user_name' => 'Full name',
        'user_email' => 'Email address',
        'reservation_date' => 'Activity reservation date',
        'reservation_time' => 'Activity reservation time',
        'activity_name' => 'Activity name',
        'reservation_id' => 'Unique activity ID',
    ],

    'addActivityMenu' => [
        'description' => 'Reservations for dates in the calendar',
        'name' => 'Reservations',
        'singular_name' => 'Reservation',
        'meta_box_title' => 'Activity reservation',
    ],

    'reservationMenu' => [
        'description' => 'Sign-ups for activities',
        'name' => 'Sign-ups for activities',
        'singular_name' => 'Sign-up for activity',
        'meta_box_title' => 'Sign-up for activity',
    ],

    'addActivityMessage' => [
        'subject' => 'Request to add activity to the calendar',
        'message_from' => 'Message from',
        'post_title' => 'Adding activity to the calendar: ',
        'message_beginning' => 'Request to add the following activity to the calendar: ',
    ],

    'reservationMessage' => [
        'subject' => 'Sign-up for activity from the reservation calendar',
        'message_from' => 'Message from',
        'post_title' => 'Sign-up for activity: ',
        'message_beginning_success' => 'Successfully signed up for the activity',
        'message_beginning_failure' => 'Failed to sign up for the activity due to exceeding the seat limit',
    ],

    'calendarLabels' => [
        'label_activity_start_at' => 'Activity start time',
        'label_activity_end_at' => 'Activity end time',
        'default_success_message' => 'Message sent. Reservation confirmed',
        'default_error_message' => 'Message sent. Reservation rejected',
        'email_error_message' => 'Problem sending email message',
        'config_error' => 'Incorrect dates / times in calendar configuration',
        'label_activity_from' => 'Activity time from',
        'label_activity_to' => 'to',
    ],

    'modalFormFriendlyNames' => [
        'send' => 'Send',
        'cancel' => 'Cancel',
        'date_calendar_add_activity_text_muted' => 'For recurring activities, specify the weekdays',
        'add_activity_title' => 'Activity submission form for the calendar',
        'reservation_title' => 'Sign-up for activity',
        'reservation_limit_over_title' => 'Sign-up for activity - limit reached',
        'reservation_limit_over_message' => 'Sign-up for the activity is not possible. The seat limit has been reached',
        'reservation_limit_over_confirm' => 'Ok',
        'add_activity_active_button' => 'Submit activity to the calendar',
        'user_name_calendar_add_activity' => 'Full name',
        'user_email_calendar_add_activity' => 'Email address',
        'user_phone_calendar_add_activity' => 'Phone number',
        'date_calendar_add_activity' => 'Proposed activity date',
        'time_start_calendar_add_activity' => 'Activity start time',
        'time_end_calendar_add_activity' => 'Activity end time',
        'name_calendar_add_activity' => 'Activity name',
        'reservation_day' => 'Sign-up for activity on',
        'reservation_hour' => 'at',
    ],

    'days' => [
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
        "Sunday"
    ],

    'months' => [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December"
    ],

    'optionPage' => [
        'main_menu_settings' => 'Reservation Calendar Settings',
        'main_short_code' => 'Use this code (shortcode) to display the calendar with full details on the page.',
        'main_short_code_form' => 'Use this code (shortcode) to display the contact form, through which you will receive messages when someone wants to book a date in the calendar.',
        'main_menu_field1_name' => 'Enable sign-up for activities by clicking on the calendar',
        'main_menu_field1_description' => 'When you click on a selected cell in the calendar, a sign-up form for the activity will appear. The form includes the seat limit for the activity.',
        'main_menu_field2_name' => 'Tile opacity with the activity color',
        'main_menu_field2_description' => 'The tile color will stretch across the activity duration. Enable this option if the activities do not overlap.',
        'main_menu_field3_name' => 'Display activity time',
        'main_menu_field3_description' => 'Enable this option if you want the activity time to be displayed in minutes on the tile.',
        'main_menu_field4_name' => 'Display activity end time',
        'main_menu_field4_description' => 'Enable this option if you want the end time of the activity to be displayed on the tile.',
        'main_menu_field5_name' => 'Email address',
        'main_menu_field5_description' => 'The email address where notifications will be sent.',
        'main_menu_field6_name' => 'Notification content for successful sign-up',
        'main_menu_field6_description' => 'Enter the message content you want the user to receive when their sign-up for an activity is accepted. You can use {name} as a placeholder.',
        'main_menu_field7_name' => 'Notification content for rejected sign-up',
        'main_menu_field7_description' => 'Enter the message content you want the user to receive when their sign-up for an activity is rejected. You can use {name} as a placeholder.',
        'main_menu_field8_name' => 'Notification content confirming form submission',
        'main_menu_field8_description' => 'Enter the message content you want the user to receive after submitting the reservation form for a date in the calendar. You can use {name} as a placeholder.',
        'main_menu_field9_name' => 'Calendar start time',
        'main_menu_field10_name' => 'Calendar end time',
        'main_menu_field11_name' => 'Calendar interval',
        'main_menu_field12_name' => 'Display activity location',
        'main_menu_field12_description' => 'Enable this option if you want the activity location to be shown on the tile.',
        'main_menu_field13_name' => 'Display calendar horizontally',
        'main_menu_field13_description' => 'Enable this option if you want the calendar to display horizontally. Dates and days of the week will be on the side, with hours at the top of the calendar grid.',
        'main_menu_field14_name' => 'Grant all users access to settings',
        'main_menu_field14_description' => 'Enable this option if you want to allow all users to manage the calendar. By default, only administrators have these privileges.',
        'main_menu_field15_name' => 'Enable additional scroll bar on the calendar',
        'main_menu_field15_description' => 'Enable this option if you want to add a scroll bar to the calendar area. The calendar will have a limited height/width.',
        'main_menu_field16_name' => 'Set calendar grid width in px',
        'main_menu_field16_description' => 'Specify the value for the calendar grid width in px according to CSS standards (e.g., 1200px). Leave empty to use the default value: 1200px.',
        'main_menu_field17_name' => 'Set calendar grid height in px',
        'main_menu_field17_description' => 'Specify the value for the calendar grid height in px according to CSS standards (e.g., 800px). Leave empty to use the default value: 100%.',
        'main_menu_field18_name' => 'Set calendar cell height in px',
        'main_menu_field18_description' => 'Specify the value for the calendar cell height in px according to CSS standards (e.g., 120px). Leave empty to use the default value: 130px.',
        'main_menu_field19_name' => 'Single day calendar view',
        'main_menu_field19_description' => 'Enable this option if you want to display the calendar in a single day view. The default view is a week view.',
        'main_menu_field20_name' => 'Display activity start time',
        'main_menu_field20_description' => 'Enable this option if you want the start time of the activity to be displayed on the tile.',
        'main_menu_textarea_field_placeholder' => 'Enter the message content',
        'short_code_activity_place' => 'Use this code (shortcode) to display the calendar with data for: ',
        'activity_place_title' => 'Activity locations',
        'activity_place_description' => 'Define the locations where activities take place',
        'activity_place_field1_name' => 'Location name',
        'activity_place_field1_description' => 'Define the names of the places where activities are held, e.g., sports hall, room 1, swimming pool.',
        'calendar_grid_data_title' => 'Activities in the calendar',
        'calendar_grid_data_description' => 'Define the reservation calendar',
        'calendar_grid_data_field1_name' => 'Activity name',
        'calendar_grid_data_field2_name' => 'Activity start time',
        'calendar_grid_data_field3_name' => 'Activity end time',
        'calendar_grid_data_field4_name' => 'Is the activity recurring',
        'calendar_grid_data_field5_name' => 'Activity date',
        'calendar_grid_data_field5_description' => 'Set the date for a one-time activity.',
        'calendar_grid_data_field6_name' => 'Select recurring activity day',
        'calendar_grid_data_field7_name' => 'Activity tile color',
        'calendar_grid_data_field7_description' => 'Choose the color you want to highlight the activity with in the calendar.',
        'calendar_grid_data_field8_name' => 'Select activity location',
        'calendar_grid_data_field8_description' => 'Select the location (from predefined ones) where the activity will take place.',
        'calendar_grid_data_field9_name' => 'Number of seats for the activity',
        'calendar_grid_data_field9_description' => 'Define how many times the activity can be booked. The minimum value is 1.',
    ],
];