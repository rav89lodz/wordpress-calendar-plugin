<?php

use CalendarPlugin\src\classes\services\CalendarService;
use CalendarPlugin\src\classes\services\LanguageService;

$initMonth = null;
$initDate = null;
$shortCode = null;

if(isset($_POST['calendar_grid_change_month'])) {
    $initMonth = $_POST['calendar_grid_change_month'];
    unset($_POST['calendar_grid_change_month']);
}

if(isset($_POST['calendar_grid_change_date'])) {
    $initDate = $_POST['calendar_grid_change_date'];
    unset($_POST['calendar_grid_change_date']);
}

if(isset($_POST['calendar_grid_short_code'])) {
    $shortCode = $_POST['calendar_grid_short_code'];
    unset($_POST['calendar_grid_short_code']);
}

$insertShort = implode('|*|', $shortCode);
$calendarService = new CalendarService($initMonth, $initDate, $shortCode);

$service = new LanguageService;

$width = $calendarService->calendar->get_calendar_grid_width() . "px";
$height = $calendarService->calendar->get_calendar_grid_height() . "px";

$divStyle = 'style="height: ' . $height . '; width: ' . $width . '"';

if($calendarService->calendar->get_add_scroll_to_table() === true) {
    $divStyle = ' style="overflow: auto; height: ' . $height . '; width: ' . $width . '"';
    if($calendarService->calendar->get_horizontal_calendar_grid() === true) {
        $divStyle = ' style="overflow: auto; height: ' . $height . '; width: ' . $width . '"';
        echo '<style> table th { min-width: 150px; } </style>';
    }
}

if(! empty($calendarService->calendar->get_calendar_cell_min_height())) {
    echo '<style> .calendar-table td {height: ' . $calendarService->calendar->get_calendar_cell_min_height() . 'px } .calendar-event {height: ' . $calendarService->calendar->get_calendar_cell_min_height() . 'px } </style>';
}

if ($calendarService->calendar->get_calendar_one_day_view() === true) {
    echo '<style> .calendar-event { width: 22%; } </style>';
}

?>

<div class="alert alert-success text-center my-alert-success" role="alert">
    <h4 class="alert-heading" id="form_success"></h4>
</div>
<div class="alert alert-danger text-center my-alert-error" role="alert">
    <h4 class="alert-heading" id="form_error"></h4>
</div>

<input type="hidden" id="get_rest_url" value="<?= get_rest_url(null, 'v1') ?>">
<input type="hidden" id="calendar_grid_short_code" value="<?= $insertShort ?>">
<input type="hidden" id="calendar_grid_interval" value="<?= $calendarService->calendar->get_calendar_interval() ?>">

<div id="calendar_form_grid1" class="mt-5 mb-5 d-flex justify-content-center">
    <?php if ($calendarService->calendar->get_calendar_one_day_view() === true): ?>
        <div>
            <?php
                if($calendarService->calendar->get_first_hour_on_calendar() < $calendarService->calendar->get_last_hour_on_calendar()) {
            ?>
                <div>
                    <div id="arrow_control" class="text-center mt-3 w-100">
                        <div class="row mb-5" style="height: var(--c-plugin-arrow-height) !important;">
                            <div class="col-3" style="height: var(--c-plugin-arrow-height) !important;">

                            </div>
                            <div class="col-6" style="height: var(--c-plugin-arrow-height) !important;">
                                <div class="row" style="height: var(--c-plugin-arrow-height) !important;">
                                    <div class="col-3" style="height: var(--c-plugin-arrow-height) !important;">
                                        <?php
                                            if($calendarService->showLeftArrows[1]) {
                                                echo '<button id="one_day_arrow_left" class="btn btn-primary" style="font-size: 18px;"><strong><<</strong></button>';
                                            }
                                        ?>
                                    </div>
                                    <div class="col-6" style="height: var(--c-plugin-arrow-height) !important;">
                                        <h5 class="text-wrap"><strong id="one_day_name"><?php echo $calendarService->calendar->get_cuttent_date() ?></strong></h5>
                                        <p class="text-wrap"><strong><?php echo $calendarService->currentDayName ?></strong></p>
                                    </div>
                                    <div class="col-3" style="height: var(--c-plugin-arrow-height) !important;">
                                        <button id="one_day_arrow_right" class="btn btn-primary" style="font-size: 18px;"><strong>>></strong></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3" style="height: var(--c-plugin-arrow-height) !important;">

                            </div>
                        </div>
                    </div>
                    <div class="row mt-3 mb-4 w-100">
						<div class="col-lg-3 col-lg-offset-3">
						</div>
    					<div class="col-lg-6 col-lg-offset-6">
        					<div class="input-group">
                        		<input class="form-control w-50" type="date" id="calendar_date_picker" min="<?= date('Y-m-d') ?>" />
							</div>
						</div>
						<div class="col-lg-3 col-lg-offset-3">
						</div>
                    </div>
                </div>
                <div <?= $divStyle ?>>
                    <table class="table table-striped table-bordered text-center calendar-table" id="calendar_form_table">
                        <?php
                            echo '<thead class="calendar-table-header-one-day">';
                                $calendarService->create_table_header();
                            echo '</thead><tbody>';
                                $calendarService->create_table_content();
                            echo '</tbody>';
                        ?>
                    </table>
                </div>
            <?php
                }
                else {
                    echo "<div><h4 style='color: red !important; text-align: center !important;'>" . $service->calendarLabels['config_error'] . "</h4></div>";
                }
            ?>
        </div>
    <?php else: ?>
        <div>
            <?php
                if($calendarService->calendar->get_first_hour_on_calendar() < $calendarService->calendar->get_last_hour_on_calendar()) {
            ?>
                <div id="arrow_control" class="text-center mt-3 w-100">
                    <div class="row mb-3" style="height: var(--c-plugin-arrow-height) !important;">
                        <div class="col-3" style="height: var(--c-plugin-arrow-height) !important;">

                        </div>
                        <div class="col-6" style="height: var(--c-plugin-arrow-height) !important;">
                            <div class="row" style="height: var(--c-plugin-arrow-height) !important;">
                                <div class="col-3" style="height: var(--c-plugin-arrow-height) !important;">
                                    <?php
                                        if($calendarService->showLeftArrows[0]) {
                                            echo '<button id="month_arrow_left" class="btn btn-primary" style="font-size: 22px;"><strong><<</strong></button>';
                                        }
                                    ?>
                                </div>
                                <div class="col-6" style="height: var(--c-plugin-arrow-height) !important;">
                                    <h3 id="month_name" class="text-wrap"><strong><?php echo $calendarService->calendar->get_cuttent_month_name() ?></strong></h3>
                                </div>
                                <div class="col-3" style="height: var(--c-plugin-arrow-height) !important;">
                                    <button id="month_arrow_right" class="btn btn-primary" style="font-size: 22px;"><strong>>></strong></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-3" style="height: var(--c-plugin-arrow-height) !important;">

                        </div>
                    </div>
                    <div class="row mb-3" style="height: var(--c-plugin-arrow-height) !important;">
                        <div class="col-3" style="height: var(--c-plugin-arrow-height) !important;">

                        </div>
                        <div class="col-6" style="height: var(--c-plugin-arrow-height) !important;">
                            <div class="row" style="height: var(--c-plugin-arrow-height) !important;">
                                <div class="col-3" style="height: var(--c-plugin-arrow-height) !important;">
                                    <?php
                                        if($calendarService->showLeftArrows[1]) {
                                            echo '<button id="week_arrow_left" class="btn btn-secondary btn-sm" style="font-size: 18px;"><strong><<</strong></button>';
                                        }
                                    ?>
                                </div>
                                <div class="col-6" style="height: var(--c-plugin-arrow-height) !important;">
                                    <h5 id="week_dates" class="text-wrap"><?php echo $calendarService->calendar->get_cuttent_monday_date() . " <-> " . $calendarService->calendar->get_monday_plus_days(6) ?></h5>
                                </div>
                                <div class="col-3" style="height: var(--c-plugin-arrow-height) !important;">
                                    <button id="week_arrow_right" class="btn btn-secondary btn-sm" style="font-size: 18px;"><strong>>></strong></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-3" style="height: var(--c-plugin-arrow-height) !important;">

                        </div>
                    </div>
                </div>
                <div <?= $divStyle ?>>
                    <table class="table table-striped table-bordered text-center calendar-table" id="calendar_form_table">
                        <?php
                            echo '<thead class="calendar-table-header">';
                                $calendarService->create_table_header();
                            echo '</thead><tbody>';
                                $calendarService->create_table_content();
                            echo '</tbody>';
                        ?>
                    </table>
                <div>
            <?php
                }
                else {
                    echo "<div><h4 style='color: red !important; text-align: center !important;'>" . $service->calendarLabels['config_error'] . "</h4></div>";
                }
            ?>
        </div>
    <?php endif; ?>
</div>

<?php if ($calendarService->calendar->get_calendar_reservation() === true): ?>

<div class="d-flex justify-content-center">
    <div class="modal fade" id="calendarFormModalCenter" tabindex="-1" role="dialog" aria-labelledby="calendarFormModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="row w-100">
                        <div class="col-10">
                            <h5 class="modal-title"><?= $service->modalFormFriendlyNames['reservation_title']?></h5>
                        </div>
                        <div class="col-2">
                            <div class="modal-plugin-close-button">
                                <button type="button" class="close btn btn-sm btn-danger" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true" data-dismiss="modal">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <form id="calendar_modal_form">
                    <div class="modal-body">
                        <div>
                            <p><?= $service->modalFormFriendlyNames['reservation_day']?> <strong id="calendar_modal_day_name"></strong> <?= $service->modalFormFriendlyNames['reservation_hour']?> <strong id="calendar_modal_hour"></strong></p>
                        </div>
                        <div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><?= $service->modalFormFriendlyNames['user_name_calendar_add_activity']?></span>
                                </div>
                                <input type="text" class="form-control" name="user_name_calendar_modal" id="user_name_calendar_modal" aria-describedby="basic-addon1" required>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon2"><?= $service->modalFormFriendlyNames['user_email_calendar_add_activity']?></span>
                                </div>
                                <input type="email" class="form-control" name="user_email_calendar_modal" id="user_email_calendar_modal" aria-describedby="basic-addon2" required>
                            </div>
                            <input type="hidden" name="calendar_modal_hidden_id" id="calendar_modal_hidden_id">
                            <input type="hidden" name="calendar_modal_day_name" id="calendar_modal_day_name_input">
                            <input type="hidden" name="calendar_modal_hour" id="calendar_modal_hour_input">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="close btn btn-secondary" data-dismiss="modal"><?= $service->modalFormFriendlyNames['cancel']?></button>
                        <button type="submit" id="submit_calendar_modal_form" class="btn btn-success"><?= $service->modalFormFriendlyNames['send']?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="calendarFormModalLimitOver" tabindex="-1" role="dialog" aria-labelledby="calendarFormModalLimitOverTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="row w-100">
                        <div class="col-10">
                            <h5 class="modal-title"><?= $service->modalFormFriendlyNames['reservation_limit_over_title']?></h5>
                        </div>
                        <div class="col-2">
                            <div class="modal-plugin-close-button">
                                <button type="button" class="close btn btn-sm btn-danger" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true" data-dismiss="modal">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <form id="calendar_modal_form">
                    <div class="modal-body">
                        <div>
                            <h5><?= $service->modalFormFriendlyNames['reservation_limit_over_message']?></h5>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="close btn btn-secondary" data-dismiss="modal"><?= $service->modalFormFriendlyNames['reservation_limit_over_confirm']?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>
