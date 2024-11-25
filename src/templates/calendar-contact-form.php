<button type="button" id="calendarFormModalAddActivityButton" class="btn btn-primary" data-toggle="modal" data-target="#calendarFormModalAddActivity">Zgłoś zajęcia do kalendarza</button>

<div class="modal fade" id="calendarFormModalAddActivity" tabindex="-1" role="dialog" aria-labelledby="calendarFormModalAddActivityTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row w-100">
                    <div class="col-10">
                        <h5 class="modal-title">Formularz zgłoszenia zajęć w kalendarzu</h5>
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
                                <span class="input-group-text" id="basic-addon77">Imię i nazwisko</span>
                            </div>
                            <input type="text" class="form-control" name="user_name_calendar_add_activity" aria-describedby="basic-addon77">
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon78">Adres email</span>
                            </div>
                            <input type="text" class="form-control" name="user_email_calendar_add_activity" aria-describedby="basic-addon78">
                        </div>

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon79">Proponowana data zajęć</span>
                            </div>
                            <input type="text" class="form-control" name="date_calendar_add_activity" aria-describedby="basic-addon79">
                            <small class="text-muted w-100">W przypadku zajęć cyklicznych należy podać dni tygodnia</small>
                        </div>

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon80">Godzina rozpoczęcia</span>
                            </div>
                            <input type="text" class="form-control" name="time_calendar_add_activity" aria-describedby="basic-addon80">
                        </div>

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon81">Czas trwania zajęć w minutach</span>
                            </div>
                            <input type="number" class="form-control" name="duration_calendar_add_activity" aria-describedby="basic-addon81">
                        </div>

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon82">Nazwa zajęć</span>
                            </div>
                            <input type="text" class="form-control" name="name_calendar_add_activity" aria-describedby="basic-addon82">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="close btn btn-secondary" data-dismiss="modal">Anuluj</button>
                    <button type="button" id="submit_calendar_modal_form_add_activity" class="btn btn-success">Wyślij</button>
                </div>
            </form>
        </div>
    </div>
</div>
