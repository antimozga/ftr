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

function UnbanSubmit(id_topic, id_user, id_session) {
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

//    document.getElementById('ts_submit').hidden = true;
    document.getElementById('ts_submit_process').hidden = false;

    request.onload = function() {
	let data = request.response;
	console.log("error = " + data.status);
	if (data.status != 'ok') {
	    document.getElementById('topic_settings_error').innerHTML = "Ошибка сервера.";
//	    document.getElementById('ts_submit').hidden = false;
	    document.getElementById('ts_submit_process').hidden = true;
	} else {
//	    location.href = data.url;
	    modal_reload();
	}
    }

    request.onerror = function() {
	console.log("error");
	document.getElementById('topic_settings_error').innerHTML = "Ошибка сети.";
//	document.getElementById('ts_submit').hidden = false;
	document.getElementById('ts_submit_process').hidden = true;
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
