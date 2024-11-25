window.addEventListener('DOMContentLoaded', () => {
    calendar_form_setup();
});

function calendar_form_setup() {
    let button = document.querySelector('#calendarFormModalAddActivityButton');
    if(button) {
        let get_rest_url = document.querySelector("#get_rest_url");
        let get_rest_url_value = null;
        if(get_rest_url) {
            get_rest_url_value = get_rest_url.value;
        }

        let modal_form = document.querySelector('#calendarFormModalAddActivity');
        if(modal_form) {
            let modal_form_object = new bootstrap.Modal(modal_form, {});

            button.addEventListener('click', () => {
                modal_form_object.show();
            });
    
            modal_form.addEventListener('click', (e) => {
                if(e.target.id == "calendarFormModalAddActivity" || e.target.hasAttribute('data-dismiss')) {
                    modal_form_object.hide();
                }
            });

            let url = get_rest_url_value + '/calendar-grid-form/add-activity'
            let submit_calendar_modal_form_add_activity = document.querySelector('#submit_calendar_modal_form_add_activity');

            submit_calendar_modal_form_add_activity.addEventListener('click', (e) => {
                e.preventDefault();
                calendar_form_submit(url, 'calendar_modal_form_add_activity');
                modal_form_object.hide();
            });
        }
    }
}