Date.prototype.addOrSubtractDays = function(days, operator) {
    let date = new Date(this.valueOf());
    if(operator == '+') {
        date.setDate(date.getDate() + days);
        return date;
    }
    date.setDate(date.getDate() - days);
    return date;
}

Date.prototype.addOrSubtractMonth = function(months, operator) {
    let date = new Date(this.valueOf());

    if(operator == '+') {
        date.setMonth(date.getMonth() + months);
        return date;
    }
    date.setMonth(date.getMonth() - months);
    return date;
}

function send_request_for_month(month_date, url) {
    month_date = month_date.toISOString();
    send_request_with_data(month_date, url);
}

function send_request_for_week(week_date, url) {
    week_date = week_date.toISOString();
    send_request_with_data(week_date, url);
}

function send_request_with_data(data, url) {
    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function() {
        if (xmlhttp.readyState === 4) {
            change_grid_content(xmlhttp);
        }
    }
    let calendar_grid_short_code = document.querySelector('#calendar_grid_short_code');
    let object = {"data": data, "short_code": calendar_grid_short_code.value};

    xmlhttp.open("POST", url);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send(JSON.stringify(object));
}

function change_grid_content(xmlhttp) {
    try {
        let jsonResponse = JSON.parse(xmlhttp.responseText);
        let parser = new DOMParser();
        let response = parser.parseFromString(jsonResponse, "text/html");

        if (xmlhttp.status === 200) {
            document.querySelector("#calendar_form_grid1").innerHTML = response.querySelector('#calendar_form_grid1').innerHTML;
            calendar_setup();
            modal_setup();

            console.log(response.querySelector('#calendar_grid_short_code'));
        }
    }
    catch(error) {
        console.log(error);
    }
}

function create_calendar_data(object_name) {
    let calendar_modal_form = document.querySelector('#' + object_name);
    const form_data = new FormData(calendar_modal_form);
    let object = {};
    form_data.forEach((value, key) => {
        if(!Reflect.has(object, key)) {
            object[key] = value;
            return;
        }
        if(!Array.isArray(object[key])) {
            object[key] = [object[key]];
        }
        object[key].push(value);
    });
    return object;
}

function calendar_form_submit(url, object_name) {
    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function() {
        if (xmlhttp.readyState === 4) {
            let jsonResponse = JSON.parse(xmlhttp.responseText);
            let parser = new DOMParser();
            let response = parser.parseFromString(jsonResponse, "text/html");

            if (xmlhttp.status === 200) {
                document.querySelector('.my-alert-success').style.display = "block";
                document.querySelector("#form_success").innerText = response.firstChild.innerText;
            }
            else {
                document.querySelector('.my-alert-error').style.display = "block";
                document.querySelector("#form_error").innerText = response.firstChild.innerText;
            }
        }
    }

    xmlhttp.open("POST", url);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send(JSON.stringify(create_calendar_data(object_name)));
}
