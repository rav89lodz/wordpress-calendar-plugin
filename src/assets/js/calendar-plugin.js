window.addEventListener('DOMContentLoaded', () => {
    calendar_setup();
    fluent_background_setup();
    modal_setup();
});

function calendar_setup() {
    let get_rest_url = document.querySelector("#get_rest_url");
    let get_rest_url_value = null;
    if(get_rest_url) {
        get_rest_url_value = get_rest_url.value;
    }

    let arrow_control = document.querySelector('#arrow_control');
    if(arrow_control) {
        let week_dates = document.querySelector('#week_dates');
        week_dates = week_dates.innerText.split(" <-> ");
        let final_date = new Date(week_dates[0]);

        let month_arrow_left = document.querySelector('#month_arrow_left');
        if(month_arrow_left) {
            month_arrow_left.addEventListener('click', (e) => {
                e.preventDefault();
                let url = get_rest_url_value + "/calendar-grid-change/month";
                send_request_for_month(final_date.addOrSubtractMonth(1, '-'), url);
            });
        }

        let month_arrow_right = document.querySelector('#month_arrow_right');
        month_arrow_right.addEventListener('click', (e) => {
            e.preventDefault();
            let url = get_rest_url_value + "/calendar-grid-change/month";
            send_request_for_month(final_date.addOrSubtractMonth(1, '+'), url);
        });

        let week_arrow_left = document.querySelector('#week_arrow_left');
        if(week_arrow_left) {
            week_arrow_left.addEventListener('click', (e) => {
                e.preventDefault();
                let url = get_rest_url_value + "/calendar-grid-change/week";
                send_request_for_week(final_date.addOrSubtractDays(7, '-'), url);
            });
        }

        let week_arrow_right = document.querySelector('#week_arrow_right');
        week_arrow_right.addEventListener('click', (e) => {
            e.preventDefault();
            let url = get_rest_url_value + "/calendar-grid-change/week";
            send_request_for_week(final_date.addOrSubtractDays(7, '+'), url);
        });
    }
}

function modal_setup() {
    let calendar_form_table = document.querySelector('#calendar_form_table');
    let my_modal = document.querySelector('#calendarFormModalCenter');
    if(calendar_form_table && my_modal) {
        let days = ["Poniedziałek", "Wtorek", "Środa", "Czwartek", "Piątek", "Sobota", "Niedziela"];
        let get_rest_url = document.querySelector("#get_rest_url");
        let get_rest_url_value = null;
        if(get_rest_url) {
            get_rest_url_value = get_rest_url.value;
        }

        let modal1 = document.querySelector('#calendarFormModalCenter');
        let modal_object1 = new bootstrap.Modal(modal1, {});
        let modal2 = document.querySelector('#calendarFormModalLimitOver')
        let modal_object2 = new bootstrap.Modal(modal2, {});

        let calendar_modal_day_name = document.querySelector('#calendar_modal_day_name');
        let calendar_modal_day_name_input = document.querySelector('#calendar_modal_day_name_input');
        let calendar_modal_hour = document.querySelector('#calendar_modal_hour');
        let calendar_modal_hour_input = document.querySelector('#calendar_modal_hour_input');
        let calendar_modal_hidden_id = document.querySelector('#calendar_modal_hidden_id');

        modal1.addEventListener('click', (e) => {
            if(e.target.id == "calendarFormModalCenter" || e.target.hasAttribute('data-dismiss')) {
                modal_object1.hide();
                calendar_modal_hidden_id.value = null;
                calendar_modal_day_name_input.value = null;
                calendar_modal_hour_input.value = null;
            }
        });

        modal2.addEventListener('click', (e) => {
            if(e.target.id == "calendarFormModalLimitOver" || e.target.hasAttribute('data-dismiss')) {
                modal_object2.hide();
            }
        });

        let url = get_rest_url_value + '/calendar-grid-form/registration-for-activity'
        let submit_calendar_modal_form = document.querySelector('#submit_calendar_modal_form');
        submit_calendar_modal_form.addEventListener('click', (e) => {
            e.preventDefault();
            calendar_form_submit(url, 'calendar_modal_form');
            document.querySelector('.my-alert-success').style.display = "none";
            document.querySelector('.my-alert-error').style.display = "none";
            modal_object1.hide();
            calendar_modal_hidden_id.value = null;
            calendar_modal_day_name_input.value = null;
            calendar_modal_hour_input.value = null;
        });

        calendar_form_table.addEventListener('click', (e) => {
            if(e.target.childNodes.length > 0) {

                let table_field = e.target;
                if(e.target.firstChild.nodeName == "#text") {
                    table_field = e.target.parentNode;
                }

                if(table_field.classList.contains('cursor-default')) {
                    modal_object2.show();
                }
                else {
                    let calendar_data = table_field.id.split('_');
                    let date_from_span = document.querySelector('#header_' + calendar_data[2]);
                    if(date_from_span) {
                        calendar_modal_day_name.innerText = date_from_span.innerText + " (" + days[calendar_data[2] - 1] + ")";
                        calendar_modal_day_name_input.value = date_from_span.innerText;
                        calendar_modal_hour.innerText = calendar_data[1];
                        calendar_modal_hour_input.value = calendar_data[1];
                        calendar_modal_hidden_id.value = calendar_data[0];
        
                        modal_object1.show();
                    }
                }
            }
        });
    }
}

function fluent_background_setup() {
    let table = document.querySelector("#calendar_form_table");
    let tbody = table.querySelector('tbody');
    
    set_fluent_backgroung(tbody);

    window.addEventListener('resize', function(event) {
        set_fluent_backgroung(tbody);
    }, true);

    
}

function set_fluent_backgroung(tbody) {
    let rows = tbody.querySelectorAll('tr');
    let data_hours = [];
    let data_elements = tbody.querySelectorAll('.calendar-event');

    rows.forEach((r) => {
        let td = r.querySelectorAll('td')[0];
        data_hours[td.innerHTML] = r.offsetHeight;
    });

    data_elements.forEach((e) => {
        let dates = e.getAttribute('data-info').split('|');

        if(dates[2] > 60) {
            let sum = -280;
            for (let k in data_hours) {
                if (k >= dates[0] && k <= dates[1]) {
                    sum += data_hours[k];
                }
            }
            let end = dates[1].split(':');
            switch(end[1]) {
                case "55":
                    sum += 125;
                    break;
                case "50":
                    sum += 110;
                    break;
                case "45":
                    sum += 95;
                    break;
                case "40":
                    sum += 87;
                    break;
                case "35":
                    sum += 80;
                    break;
                case "30":
                    sum += 70;
                    break;
                case "25":
                    sum += 59;
                    break;
                case "20":
                    sum += 47;
                    break;
                case "15":
                    sum += 35;
                    break;
                case "10":
                    sum += 28;
                    break;
                case "05":
                    sum += 15;
                    break;
            }
            e.style.setProperty('--after-height', sum + 'px');
        }
    });
}