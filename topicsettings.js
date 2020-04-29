function TopicSettingsSubmit(id_topic) {
    let formElement = document.getElementById('topic_settings_form');
    let formData = new FormData(formElement);
    formData.append("id_topic", id_topic);

    let request = new XMLHttpRequest();
    let url = 'topicsettings.php';

    console.log("url " + url);

    request.responseType = 'json';
    request.open("POST", url);

    document.getElementById('ts_submit').hidden = true;
    document.getElementById('ts_submit_process').hidden = false;

    request.onload = function() {
	let data = request.response;
	console.log("error = " + data.status);
	if (data.status != 'ok') {
	    document.getElementById('topic_settings_error').innerHTML = "Ошибка сервера.";
	    document.getElementById('ts_submit').hidden = false;
	    document.getElementById('ts_submit_process').hidden = true;
	} else {
//	    location.href = data.url;
	    location.reload();
	}
    }

    request.onerror = function() {
	console.log("error");
	document.getElementById('topic_settings_error').innerHTML = "Ошибка сети.";
	document.getElementById('ts_submit').hidden = false;
	document.getElementById('ts_submit_process').hidden = true;
    }

    request.send(formData);

    return false;
}

var busy_animation = `<svg width="19px" height="19px" viewBox="0 0 50 50">
<path fill="#33CCFF" d="M25,5A20.14,20.14,0,0,1,45,22.88a2.51,2.51,0,0,0,2.49,2.26h0A2.52,2.52,0,0,0,50,22.33a25.14,25.14,0,0,0-50,0,2.52,2.52,0,0,0,2.5,2.81h0A2.51,2.51,0,0,0,5,22.88,20.14,20.14,0,0,1,25,5Z">
<animateTransform attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.5s" repeatCount="indefinite"/>
</path>
</svg>`;

function UnbanSubmit(id_topic, id_user, id_session, el_id) {
    let formData = new FormData();
    formData.append("event", "unban");
    formData.append("id_topic", id_topic);
    formData.append("id_user", id_user);
    formData.append("id_session", id_session);

    let request = new XMLHttpRequest();
    let url = 'topicsettings.php';

    console.log("url " + url);

    request.responseType = 'json';
    request.open("POST", url);

    document.getElementById('unban_submit_process' + el_id).innerHTML = busy_animation;
    document.getElementById('unban_submit' + el_id).hidden = true;
    document.getElementById('unban_submit_process' + el_id).hidden = false;

    request.onload = function() {
	let data = request.response;
	console.log("error = " + data.status);
	if (data.status != 'ok') {
////	    document.getElementById('topic_settings_error').innerHTML = "Ошибка сервера.";
	    document.getElementById('unban_submit' + el_id).hidden = false;
	    document.getElementById('unban_submit_process' + el_id).hidden = true;
	} else {
//	    location.href = data.url;
	    modal_reload();
	}
    }

    request.onerror = function() {
	console.log("error");
////	document.getElementById('topic_settings_error').innerHTML = "Ошибка сети.";
	document.getElementById('unban_submit' + el_id).hidden = false;
	document.getElementById('unban_submit_process' + el_id).hidden = true;
    }

    request.send(formData);

    return false;
}

function AddSubmit(id_topic, id_user, id_session) {
    let formData = new FormData();
    formData.append("event", "add");
    formData.append("id_topic", id_topic);
    formData.append("id_user", id_user);
    formData.append("id_session", id_session);

    let request = new XMLHttpRequest();
    let url = 'topicsettings.php';

    console.log("url " + url);

    request.responseType = 'json';
    request.open("POST", url);

    document.getElementById('add_submit').hidden = true;
    document.getElementById('add_submit_process').hidden = false;

    request.onload = function() {
	let data = request.response;
	console.log("error = " + data.status);
	if (data.status != 'ok') {
	    document.getElementById('add_settings_error').innerHTML = "Ошибка сервера.";
	    document.getElementById('add_submit').hidden = false;
	    document.getElementById('add_submit_process').hidden = true;
	} else {
//	    location.href = data.url;
	    modal_toggle();
	}
    }

    request.onerror = function() {
	console.log("error");
	document.getElementById('add_settings_error').innerHTML = "Ошибка сети.";
	document.getElementById('add_submit').hidden = false;
	document.getElementById('add_submit_process').hidden = true;
    }

    request.send(formData);

    return false;
}
