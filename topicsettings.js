function TopicSettingsSubmit(id_topic) {
    let formElement = document.getElementById('topic_settings_form');
    let formData = new FormData(formElement);
    let request = new XMLHttpRequest();
    let url = 'topicsettings.php?t=' + id_topic;

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
