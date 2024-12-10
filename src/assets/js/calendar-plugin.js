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

    let calendar_date_picker = document.querySelector('#calendar_date_picker');
    if(calendar_date_picker) {
        calendar_date_picker.addEventListener('change', (e) => {
            e.preventDefault();
            let final_date = new Date(e.target.value);
            let url = get_rest_url_value + "/calendar-grid-change/week";
            send_request_for_dates(final_date, url);
        });
    }

    let arrow_control = document.querySelector('#arrow_control');
    if(arrow_control) {
        let one_day_name = document.querySelector('#one_day_name');

        if(one_day_name) {
            let final_date = new Date(one_day_name.innerHTML);

            let one_day_arrow_left = document.querySelector('#one_day_arrow_left');
            if(one_day_arrow_left) {
                one_day_arrow_left.addEventListener('click', (e) => {
                    e.preventDefault();
                    let url = get_rest_url_value + "/calendar-grid-change/week";
                    send_request_for_dates(final_date.addOrSubtractDays(1, '-'), url);
                });
            }

            let one_day_arrow_right = document.querySelector('#one_day_arrow_right');
            if(one_day_arrow_right) {
                one_day_arrow_right.addEventListener('click', (e) => {
                    e.preventDefault();
                    let url = get_rest_url_value + "/calendar-grid-change/week";
                    send_request_for_dates(final_date.addOrSubtractDays(1, '+'), url);
                });
            }
        }

        let week_dates = document.querySelector('#week_dates');

        if(week_dates) {
            week_dates = week_dates.innerText.split(" <-> ");
            let final_date = new Date(week_dates[0]);

            let month_arrow_left = document.querySelector('#month_arrow_left');
            if(month_arrow_left) {
                month_arrow_left.addEventListener('click', (e) => {
                    e.preventDefault();
                    let url = get_rest_url_value + "/calendar-grid-change/month";
                    send_request_for_dates(final_date.addOrSubtractMonth(1, '-'), url);
                });
            }

            let month_arrow_right = document.querySelector('#month_arrow_right');
            if(month_arrow_right) {
                month_arrow_right.addEventListener('click', (e) => {
                    e.preventDefault();
                    let url = get_rest_url_value + "/calendar-grid-change/month";
                    send_request_for_dates(final_date.addOrSubtractMonth(1, '+'), url);
                });
            }

            let week_arrow_left = document.querySelector('#week_arrow_left');
            if(week_arrow_left) {
                week_arrow_left.addEventListener('click', (e) => {
                    e.preventDefault();
                    let url = get_rest_url_value + "/calendar-grid-change/week";
                    send_request_for_dates(final_date.addOrSubtractDays(7, '-'), url);
                });
            }

            let week_arrow_right = document.querySelector('#week_arrow_right');
            if(week_arrow_right) {
                week_arrow_right.addEventListener('click', (e) => {
                    e.preventDefault();
                    let url = get_rest_url_value + "/calendar-grid-change/week";
                    send_request_for_dates(final_date.addOrSubtractDays(7, '+'), url);
                });
            }
        }
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
			window.scrollTo(0,0);
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
    set_fluent_backgroung();

    window.addEventListener('resize', () => {
        set_fluent_backgroung();
    }, true);
}

function set_fluent_backgroung() {
    let grid_vector = document.querySelector('#grid_vector');
    let table = document.querySelector("#calendar_form_table");

    if(grid_vector){
        if(grid_vector.value == "V") {
            vertical_grid_fluent(table);
        }
        else {
            horizontal_grid_fluent(table);
        }
    }
}

function horizontal_grid_fluent(table) {
    let thead = table.querySelector('thead');
    let rows = thead.querySelectorAll('th');

    let tbody = table.querySelector('tbody');
    let data_elements = tbody.querySelectorAll('.calendar-event');

    let data_hours = [];
    let iterator = 0;
    rows.forEach((r) => {
        if(r.innerHTML.includes('hidden')) {
            return;
        }
        data_hours[iterator] = r.innerHTML + ">>" + r.offsetWidth;
        iterator++;
    });

    data_elements.forEach((e) => {
        let dates = e.getAttribute('data-info').split('|');
        let sum = sum_fluent_cells(data_hours, dates);
        e.style.setProperty('--after-height', sum + 'px');
    });
}

function vertical_grid_fluent(table) {
    let tbody = table.querySelector('tbody');

    let data_elements = tbody.querySelectorAll('.calendar-event');
    let rows = tbody.querySelectorAll('tr');

    let data_hours = [];
    let iterator = 0;
    rows.forEach((r) => {
        let td = r.querySelectorAll('td')[0];
        data_hours[iterator] = td.innerHTML + ">>" + r.offsetHeight;
        iterator++;
    });

    data_elements.forEach((e) => {
        let dates = e.getAttribute('data-info').split('|');
        let sum = sum_fluent_cells(data_hours, dates);
        e.style.setProperty('--after-height', sum + 'px');
    });
}

function sum_fluent_cells(data_hours, dates) {
    let sum = 0;
    let last_hour_key = 0;

    for(let i = 0; i < data_hours.length; i ++) {
        let key_val = data_hours[i].split('>>');

        if(key_val[0] > dates[0] && key_val[0] <= dates[1]) {
            sum += parseInt(key_val[1]);
            last_hour_key = i;
        }
    }

    let last_hour = data_hours[last_hour_key].split('>>');
    const [hours1, minutes1] = dates[1].split(':').map(Number);
    const [hours2, minutes2] = last_hour[0].split(':').map(Number);

    const totalMinutes1 = hours1 * 60 + minutes1;
    const totalMinutes2 = hours2 * 60 + minutes2;

    if(totalMinutes1 > totalMinutes2) {
        let next_hour = 140;
        if(data_hours[last_hour_key + 1] !== undefined) {
            next_hour = data_hours[last_hour_key + 1].split('>>');
        }
        sum += sum_for_interval(minutes1, next_hour[1]);
    }
    
    return sum - last_hour[1];
}

function sum_for_interval(minutes, td_height) {
    let calendar_grid_interval = document.querySelector('#calendar_grid_interval');
    let interval = 60;
    
    if(calendar_grid_interval) {
        interval = calendar_grid_interval.value;
    }

    return (td_height / interval) * minutes;
}