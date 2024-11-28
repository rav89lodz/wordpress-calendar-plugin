<?php

use CalendarPlugin\src\classes\services\LanguageService;

$service = new LanguageService;

?>

<button type="button" id="calendarFormModalAddActivityButton" class="btn btn-primary" data-toggle="modal" data-target="#calendarFormModalAddActivity"><?= $service->modalFormFriendlyNames['add_activity_active_button'] ?></button>

<div class="modal fade" id="calendarFormModalAddActivity" tabindex="-1" role="dialog" aria-labelledby="calendarFormModalAddActivityTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row w-100">
                    <div class="col-10">
                        <h5 class="modal-title"><?= $service->modalFormFriendlyNames['add_activity_title'] ?></h5>
                    </div>
                    <div class="col-2">
                        <div class="modal-plugin-close-button2">
                            <button type="button" class="close btn btn-sm btn-danger" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" data-dismiss="modal">&times;</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <form id="calendar_modal_form_add_activity">
                <div class="modal-body">
                    <div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon76"><?= $service->modalFormFriendlyNames['user_name_calendar_add_activity'] ?></span>
                            </div>
                            <input type="text" class="form-control" name="user_name_calendar_add_activity" aria-describedby="basic-addon76" required>
                        </div>

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon77"><?= $service->modalFormFriendlyNames['user_email_calendar_add_activity'] ?></span>
                            </div>
                            <input type="email" class="form-control" name="user_email_calendar_add_activity" aria-describedby="basic-addon77" required>
                        </div>

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon78"><?= $service->modalFormFriendlyNames['user_phone_calendar_add_activity'] ?></span>
                            </div>
                            <input type="tel" class="form-control" name="user_phone_calendar_add_activity" aria-describedby="basic-addon78" required>
                        </div>

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon79"><?= $service->modalFormFriendlyNames['date_calendar_add_activity'] ?></span>
                            </div>
                            <input type="text" class="form-control" name="date_calendar_add_activity" aria-describedby="basic-addon79" required>
                            <small class="text-muted w-100"><?= $service->modalFormFriendlyNames['date_calendar_add_activity_text_muted'] ?></small>
                        </div>

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon80"><?= $service->modalFormFriendlyNames['time_start_calendar_add_activity'] ?></span>
                            </div>
                            <input type="time" step="300" class="form-control" name="time_start_calendar_add_activity" aria-describedby="basic-addon80" required>
                        </div>

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon81"><?= $service->modalFormFriendlyNames['time_end_calendar_add_activity'] ?></span>
                            </div>
                            <input type="time" step="300" class="form-control" name="time_end_calendar_add_activity" aria-describedby="basic-addon81" required>
                        </div>

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon82"><?= $service->modalFormFriendlyNames['name_calendar_add_activity'] ?></span>
                            </div>
                            <input type="text" class="form-control" name="name_calendar_add_activity" aria-describedby="basic-addon82" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="close btn btn-secondary" data-dismiss="modal"><?= $service->modalFormFriendlyNames['cancel'] ?></button>
                    <button type="button" id="submit_calendar_modal_form_add_activity" class="btn btn-success"><?= $service->modalFormFriendlyNames['send'] ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
