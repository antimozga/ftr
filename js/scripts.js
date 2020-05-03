var selField = 'mess_text';

function doInsert(ibTag, ibClsTag, isSingle)
{
	let isClose = false;
	let obj_ta = eval('fombj.' + selField);

	if ( obj_ta.selectionEnd ) {
		let ss = obj_ta.selectionStart;
		let st = obj_ta.scrollTop;
		let es = obj_ta.selectionEnd;
		
		if (es <= 2) {
			es = obj_ta.textLength;
		}
		
		let start  = (obj_ta.value).substring(0, ss);
		let middle = (obj_ta.value).substring(ss, es);
		let end    = (obj_ta.value).substring(es, obj_ta.textLength);
		
		if (obj_ta.selectionEnd - obj_ta.selectionStart > 0) {
			middle = ibTag + middle + ibClsTag;
		} else {
			middle = ibTag + middle + ibClsTag;
		}
		
		obj_ta.value = start + middle + end;
		
		let cpos = ss + (middle.length);
		
		obj_ta.selectionStart = cpos + 2;
		obj_ta.selectionEnd   = cpos;
		obj_ta.scrollTop      = st;


	} else {
		obj_ta.value += ibTag + ibClsTag;
	}

	obj_ta.focus();
	return isClose;
}

function selectElementText(el)
{
	let range = document.createRange();
	range.selectNodeContents(el);
	let selection = window.getSelection();
	selection.removeAllRanges();
	selection.addRange(range);
}

function getSelectionText()
{
    let selectedText = "";
    if (window.getSelection) {
        selectedText = window.getSelection().toString();
    }
    return selectedText;
}

function reply(creator, msg_id)
{
	let output = getSelectionText();
/*
	if (output == "") {
		selectElementText(document.getElementById(msg_id));
		output = getSelectionText();
	}
 */
	if (output != "") {
	    document.getElementById(selField).value += '[re]' + output + '[/re]\n';
	}

	document.getElementById('heading').value=creator;
	document.getElementById(selField).focus();
}

function reply_cite(creator, msg_id)
{
	let output = getSelectionText();

	if (output == "") {
	    output = document.getElementById(msg_id).innerHTML;
	    output = output
			.replace(/<cite>/g,	'[re]' )
			.replace(/<\/cite>/g,	'[/re]')
			.replace(/<i>/g,	'[i]'  )
			.replace(/<\/i>/g,	'[/i]' )
			.replace(/<b>/g,	'[b]'  )
			.replace(/<\/b>/g,	'[/b]' )
			.replace(/<br>/g,	'\r\n' )
			.replace(/<br\/>/g,	'\r\n' )
			.replace(/<a.*>.*?<\/a>/ig,'')
			.replace(/<div.*>.*?<\/div>/ig,'')
			.replace(/<iframe.*>.*?<\/iframe>/ig,'')
			.replace(/<audio.*>.*?<\/audio>/ig,  '')
			.replace(/<video.*>.*?<\/video>/ig,  '');

//		selectElementText(document.getElementById(msg_id));
//		output = getSelectionText();
	}

	if (output != "") {
	    document.getElementById(selField).value += '[re]' + output + '[/re]\n';
	}

	document.getElementById('heading').value=creator;
	document.getElementById(selField).focus();
}

/*
function reply_cite(creator, msg_id)
{
	selectElementText(document.getElementById(msg_id));
	var output = getSelectionText();

	document.getElementById(selField).value += '[re]' + output + '[/re]\n';

	document.getElementById('heading').value=creator;
	document.getElementById(selField).focus();
}
 */

function convert_text(text)
{
    return text
	.replace(/&/g,		'&amp;')
	.replace(/"/g,		'&quot;')
	.replace(/'/g,		'&apos;')
	.replace(/</g,		'&lt;')
	.replace(/>/g,		'&gt;')
	.replace(/\[i\]/g,	'<i>')
	.replace(/\[\/i\]/g,	'</i>')
	.replace(/\[b\]/g,	'<b>')
	.replace(/\[\/b\]/g,	'</b>')
	.replace(/\[re\]/g,	'<cite>')
	.replace(/\[\/re\]/g,	'</cite>')
	.replace(/\r\n/g,	'<br/>')
	.replace(/\n/g,		'<br/>')
	.replace(/\r/g,		'<br/>');
}

// Fix for mcedit color syntax parser issue: close previous escaped doublequote "

function show_date()
{
	let days = ['воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'];
	let months = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
	let d = new Date();
	let day = days[d.getDay()];
	let date = d.getDate();
	let month = months[d.getMonth()];
	let year = d.getFullYear();

	document.getElementById("curr_day").innerHTML = day;
	document.getElementById("curr_date").innerHTML = date + " " + month + " " + year;
}

function post(path, params, method, target) {
    method = method || "post"; // Set method to post by default if not specified.

    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
    let form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);
    if (target != null) {
	form.setAttribute("target", target);
    }

    for(let key in params) {
	if(params.hasOwnProperty(key)) {
	    let hiddenField = document.createElement("input");
	    hiddenField.setAttribute("type", "hidden");
	    hiddenField.setAttribute("name", key);
	    hiddenField.setAttribute("value", params[key]);

	    form.appendChild(hiddenField);
	}
    }

    document.body.appendChild(form);
    form.submit();
}

function copyStringToClipboard(str) {
    let el = document.createElement('textarea');
    el.value = str;
    el.setAttribute('readonly', '');
    el.style = {position: 'absolute', left: '-9999px'};
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
}

/*****************************************************************************
 * cut functions
 *****************************************************************************/

// Create Variables
let allOSB = [];
let mxh = '';

function init_cut() {
    // Set Variables
    allOSB = document.getElementsByClassName("text_box_2_mess");

    if (allOSB.length > 0) {
	mxh = window.getComputedStyle(allOSB[0]).getPropertyValue('max-height');
	mxh = parseInt(mxh.replace('px', ''));

	// Add read-more button to each OSB section
	for (let i = 0; i < allOSB.length; i++) {
	    let el = document.createElement("button");
	    el.innerHTML = "показать";
	    el.setAttribute("type", "button");
	    el.setAttribute("class", "read-more hid");

	    insertAfter(allOSB[i], el);
	}
    }

    // Add click function to buttons
    let readMoreButtons = document.getElementsByClassName("read-more");
    for (let i = 0; i < readMoreButtons.length; i++) {
	readMoreButtons[i].addEventListener("click", function() { 
	    revealThis(this);
	}, false);
    }

    // Update buttons so only the needed ones show
    updateReadMore();
}

// Update on resize
window.onresize = function() {
    updateReadMore();
}

// show only the necessary read-more buttons
function updateReadMore() {
    if (allOSB.length > 0) {
	for (var i = 0; i < allOSB.length; i++) {
	    if (allOSB[i].scrollHeight > mxh) {
		if (allOSB[i].hasAttribute("style")) {
		    updateHeight(allOSB[i]);
		}
		allOSB[i].nextElementSibling.className = "read-more";
	    } else {
		allOSB[i].nextElementSibling.className = "read-more hid";
	    }
	}
    }
}

function revealThis(current) {
    var el = current.previousElementSibling;
    if (el.hasAttribute("style")) {
	current.innerHTML = "показать";
	el.removeAttribute("style");
	el.parentElement.previousElementSibling.scrollIntoView({behavior: "smooth"});
    } else {
	updateHeight(el);
	current.innerHTML = "спрятать";
    }
}

function updateHeight(el) {
    el.style.maxHeight = el.scrollHeight + "px";
}

// thanks to karim79 for this function
// http://stackoverflow.com/a/4793630/5667951
function insertAfter(referenceNode, newNode) {
    referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}

/*****************************************************************************
 * responsible button
 *****************************************************************************/

function mobileMenu(name, eclass) {
    let x = document.getElementById(name);
    if (x.className === eclass) {
	x.className += " responsive";
    } else {
	x.className = eclass;
    }
}

/*****************************************************************************
 * modal functions
 *****************************************************************************/

let modal_stack = [];
let modal_stack_last = "";
let modal_in_use = 0;

let modal;
let modal_trigger;
let modal_closeButton;

let svg_loader = "<svg width=\"50\" height=\"50\" viewBox=\"0 0 50 50\">\
<path fill=\"#33CCFF\" d=\"M25,5A20.14,20.14,0,0,1,45,22.88a2.51,2.51,0,0,0,2.49,2.26h0A2.52,2.52,0,0,0,50,22.33a25.14,25.14,0,0,0-50,0,2.52,2.52,0,0,0,2.5,2.81h0A2.51,2.51,0,0,0,5,22.88,20.14,20.14,0,0,1,25,5Z\">\
<animateTransform attributeName=\"transform\" type=\"rotate\" from=\"0 25 25\" to=\"360 25 25\" dur=\"0.5s\" repeatCount=\"indefinite\"/>\
</path>\
</svg>";

function init_modal() {
    modal = document.querySelector(".modal");
    modal_trigger = document.querySelector(".trigger");
    modal_closeButton = document.querySelector(".close-button");

    //modal_trigger.addEventListener("click", toggleModal);
    modal_closeButton.addEventListener("click", modal_toggle);
    //modal_window.addEventListener("click", windowOnClick);
}

function modal_toggle() {
    let el = document.getElementById("modal-content");

    let el_param = document.getElementById("modal-content-args");
    if (el_param != null) {
	if (el_param.getAttribute("reloadPageOnClose") != null) {
	    console.log("reload document on modal close");
	    location.reload();
	}
    }

    if (Array.isArray(modal_stack) && modal_stack.length) {
	el.innerHTML = svg_loader;
	modal_stack_last = modal_stack.pop();
	fetch(modal_stack_last).then(function(response) {
	    return response.text().then(function(text) {
		let el = document.getElementById("modal-content");
		el.innerHTML = text;
		page_updater_onload(el);
	    });
	});
    } else {
	modal.classList.toggle("show-modal");
	el.innerHTML = "";
	modal_stack_last = "";
	modal_in_use = 0;
    }
}

function modal_reload()
{
    if (modal_stack_last != "") {
	fetch(modal_stack_last).then(function(response) {
	    return response.text().then(function(text) {
		let el = document.getElementById("modal-content");
		el.innerHTML = text;
		page_updater_onload(el);
	    });
	});
    }
}

/*
function windowOnClick(event) {
    if (event.target === modal) {
        toggleModal();
    }
}
 */

function show_modal(text) {
    let el = document.getElementById("modal-content");
    el.innerHTML = text;
    modal.classList.toggle("show-modal");
}

function load_modal(url) {
    let el = document.getElementById("modal-content");
    el.innerHTML = svg_loader;

    if (modal_in_use != 0) {
	modal_stack.push(modal_stack_last);
    } else {
	modal_in_use = 1;
	modal.classList.toggle("show-modal");
    }

    modal_stack_last = url;

    fetch(url).then(function(response) {
	return response.text().then(function(text) {
	    let el = document.getElementById("modal-content");
	    el.innerHTML = text;
	    page_updater_onload(el);
	});
    });
}

function update_modal(url) {
    let el = document.getElementById("modal-content");
    el.innerHTML = svg_loader;

    if (modal_in_use == 0) {
	return;
    }

    fetch(url).then(function(response) {
	return response.text().then(function(text) {
	    let el = document.getElementById("modal-content");
	    el.innerHTML = text;
	    page_updater_onload(el);
	});
    });
}

/*****************************************************************************
 * popup window
 *****************************************************************************/

function popup(id, text) {
    let popup = document.getElementById(id);
    popup.innerHTML = text;
    popup.classList.toggle("show");
    setTimeout(function() {
	popup.classList.toggle("show");
	popup.innerHTML = "";
    }, 2000);
}

function popup_copy(id) {
    popup(id, "Ссылка скопирована в буфер обмена");
}

/*****************************************************************************
 * page update timer
 *****************************************************************************/

let page_timer;

async function page_fetch(el) {
    let preexec = el.getAttribute("preexec");
    if (preexec != null) {
	if (typeof window[preexec] === "function") {
	    window[preexec](el);
	} else {
	    console.log("Function " + preexec + " not found!");
	}
	el.setAttribute("preexec", "");
    }
    let url = el.getAttribute("src");
    if (url !== null && url.length > 0) {
	fetch(url).then(function(response) {
	    return response.text().then(function(text) {
		while (el.firstChild) {
		    el.removeChild(el.lastChild);
		}

		if (url.substring(url.lastIndexOf('.') + 1) == 'js') {
		    let script = document.createElement("script");
		    script.type="text/javascript";
		    script.innerHTML = text;
		    el.appendChild(script);
		} else {
		    let span = document.createElement("span");
		    span.innerHTML = text;
		    el.appendChild(span);
		    page_updater_onload(el);
		}

		let exec = el.getAttribute("exec");
		if (exec != null) {
		    if (typeof window[exec] === "function") {
			window[exec](el);
		    } else {
			console.log("Function " + exec + " not found!");
		    }
		}
	    });
	});
    } else {
	let exec = el.getAttribute("exec");
	if (exec != null) {
	    if (typeof window[exec] === "function") {
		window[exec](el);
	    } else {
		console.log("Function " + exec + " not found!");
	    }
	}
    }
}

function page_updater_onload(doc) {
    let elements;
    while ((elements = doc.getElementsByClassName("refreshnow")).length > 0) {
	let element = elements[0];
	elements[0].className = elements[0].className.replace(/\brefreshnow\b/g, "");
	page_fetch(element);
    }
}

function page_updater() {
    let elements = document.getElementsByClassName("autorefresh");
    for (let i = 0; i < elements.length; i++) {
	let element = elements[i];
	page_fetch(element);
    }
}

function page_updater_stop() {
    clearInterval(page_timer);
}

/*
 * MediaRecorder
 */

const recordAudio = () =>
  new Promise(async resolve => {
    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
    const mediaRecorder = new MediaRecorder(stream);
    const audioChunks = [];
    let audioTime = 0;

    mediaRecorder.addEventListener("dataavailable", event => {
      audioChunks.push(event.data);
      audioTime++;
    });

    const start = () => mediaRecorder.start(1000);

    const stop = () =>
      new Promise(resolve => {
        mediaRecorder.addEventListener("stop", () => {
          const audioBlob = new Blob(audioChunks);
          const audioUrl = URL.createObjectURL(audioBlob);
          const audio = new Audio(audioUrl);
          const play = () => audio.play();
          const duration = () => {return audioTime;}
          resolve({ audioBlob, audioUrl, play, duration });
        });

        mediaRecorder.stop();
      });

    const status = () => { return audioTime; }

    resolve({ start, stop, status });
  });

let myRecorder, myRecorderData = null, myRecordedAudio = null, myRecorderTimeout, myRecorderInterval;

let myRecorderStopCallback, myRecorderUpdateCallback;

const myRecorderMaxTime = 30000; // 30 s.

const myRecorderStart = async(updateCallback = null, stopCallback = null) => {
    myRecorderUpdateCallback = updateCallback;
    myRecorderStopCallback = stopCallback;
    myRecorderData = null;
    myRecorder = await recordAudio();
    let actionButton = document.getElementById('action_recstart');
    actionButton.hidden = true;
    actionButton = document.getElementById('action_recstop');
    actionButton.hidden = false;
    actionButton = document.getElementById('action_recplay');
    actionButton.hidden = true;
    myRecorderTimeout = setTimeout(myRecorderStop, myRecorderMaxTime);
    myRecorder.start();
    myRecorderInterval = setInterval(function() {
	if (myRecorderUpdateCallback != null) {
	    myRecorderUpdateCallback(myRecorderMaxTime, myRecorder.status());
	}
    }, 500);
}

const myRecorderStop = async() => {
    clearInterval(myRecorderInterval);
    clearTimeout(myRecorderTimeout);
    myRecorderData = await myRecorder.stop();
    if (myRecorderStopCallback != null) {
	myRecorderStopCallback(myRecorderData.audioUrl);
    }
    let actionButton = document.getElementById('action_recstart');
    actionButton.hidden = false;
    actionButton = document.getElementById('action_recstop');
    actionButton.hidden = true;
    actionButton = document.getElementById('action_recplay');
    actionButton.hidden = false;
}

const myRecorderPlay = async() => {
    if (myRecordedAudio != null) {
	myRecordedAudio.pause();
	myRecordedAudio = null;
	let actionButton = document.getElementById('action_playstart');
	actionButton.hidden = false;
	actionButton = document.getElementById('action_playstop');
	actionButton.hidden = true;
	actionButton = document.getElementById('action_recstart');
	actionButton.hidden = false;
	return;
    }
//    myRecorderData.play();
    myRecordedAudio = new Audio(myRecorderData.audioUrl);
    let actionButton = document.getElementById('action_playstart');
    actionButton.hidden = true;
    actionButton = document.getElementById('action_playstop');
    actionButton.hidden = false;
    actionButton = document.getElementById('action_recstart');
    actionButton.hidden = true;
    myRecordedAudio.onended = function() {
	let actionButton = document.getElementById('action_playstart');
	actionButton.hidden = false;
	actionButton = document.getElementById('action_playstop');
	actionButton.hidden = true;
	actionButton = document.getElementById('action_recstart');
	actionButton.hidden = false;
	myRecordedAudio = null;
//	console.log("ended");
    }
    myRecordedAudio.play();
}

function reControl(id) {
    if (typeof MediaRecorder === 'undefined') {
        document.getElementById('mess_post_error').innerHTML = 'Для использования записи требуется Firefox 30+ или Chrome 47+.';
	return;
    }

    let el = document.getElementById(id);
    if (el.hidden) {
	el.hidden = false;
    } else {
	el.hidden = true;
    }
}

/*
 *
 */

function formSubmit2(url, id) {
    let formElement = document.getElementById(id);
    let formData = new FormData(formElement);
    let request = new XMLHttpRequest();
    request.responseType = 'json';
    request.open("POST", url);

    if (myRecorderData != null) {
	console.log("BLOB");
	formData.append("image", myRecorderData.audioBlob, "myfile.mp4a");
    }

    request.onload = function() {
	let data = request.response;
	console.log("error = " + data.error);
	console.log("url   = " + data.url);
	if (data.error != '') {
	    document.getElementById('mess_post_error').innerHTML = data.error;
	    document.getElementById('mess_submit').hidden = false;
	    document.getElementById('mess_submit_process').hidden = true;
	} else {
	    location.href = data.url;
	}
    }

    request.onerror = function() {
	document.getElementById('mess_post_error').innerHTML = 'Ошибка передачи данных!';
	document.getElementById('mess_submit').hidden = false;
	document.getElementById('mess_submit_process').hidden = true;
    }

    request.send(formData);
}

/*
 * BAN SUBMIT
 */

function banSubmit(id_post) {
    let formData = new FormData();
    formData.append("event", "ban");
    formData.append("id_post", id_post);

    let request = new XMLHttpRequest();
    let url = 'topicsettings.php';

    console.log("url " + url);

    request.responseType = 'json';
    request.open("POST", url);

//    document.getElementById('ts_submit').hidden = true;
//    document.getElementById('ts_submit_process').hidden = false;

    request.onload = function() {
	let data = request.response;
	console.log("error = " + data.status);
	if (data.status != 'ok') {
//	    document.getElementById('topic_settings_error').innerHTML = "Ошибка сервера.";
//	    document.getElementById('ts_submit').hidden = false;
//	    document.getElementById('ts_submit_process').hidden = true;
	} else {
//	    location.href = data.url;
	    location.reload();
	}
    }

    request.onerror = function() {
	console.log("error");
//	document.getElementById('topic_settings_error').innerHTML = "Ошибка сети.";
//	document.getElementById('ts_submit').hidden = false;
//	document.getElementById('ts_submit_process').hidden = true;
    }

    request.send(formData);

    return false;
}

/*
 *
 */

window.onload = function() {
    init_cut();
    init_modal();
    page_updater_onload(document);
    page_timer = setInterval(page_updater, 10000);

    if (typeof pgpRegInit === "function") {
	pgpRegInit();
    }
}

const linkify_options = {
    callback: function( text, href ) {
	return href ? '<a href="' + href + '" title="' + href + '" target="_blank">' + text + '<\/a>' : text;
    },
    punct_regexp: /(?:[!?.,:;'"]|(?:&|&amp;)(?:lt|gt|quot|apos|raquo|laquo|rsaquo|lsaquo);)$/
};

/*
 * JavaScript Linkify - v0.3 - 6/27/2009
 * http://benalman.com/projects/javascript-linkify/
 * 
 * Copyright (c) 2009 "Cowboy" Ben Alman
 * Dual licensed under the MIT and GPL licenses.
 * http://benalman.com/about/license/
 * 
 * Some regexps adapted from http://userscripts.org/scripts/review/7122
 */
window.linkify=(function(){var k="[a-z\\d.-]+://",h="(?:(?:[0-9]|[1-9]\\d|1\\d{2}|2[0-4]\\d|25[0-5])\\.){3}(?:[0-9]|[1-9]\\d|1\\d{2}|2[0-4]\\d|25[0-5])",c="(?:(?:[^\\s!@#$%^&*()_=+[\\]{}\\\\|;:'\",.<>/?]+)\\.)+",n="(?:ac|ad|aero|ae|af|ag|ai|al|am|an|ao|aq|arpa|ar|asia|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|biz|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|cat|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|coop|com|co|cr|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|info|int|in|io|iq|ir|is|it|je|jm|jobs|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mil|mk|ml|mm|mn|mobi|mo|mp|mq|mr|ms|mt|museum|mu|mv|mw|mx|my|mz|name|na|nc|net|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pro|pr|ps|pt|pw|py|qa|re|ro|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tel|tf|tg|th|tj|tk|tl|tm|tn|to|tp|travel|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|xn--0zwm56d|xn--11b5bs3a9aj6g|xn--80akhbyknj4f|xn--9t4b11yi5a|xn--deba0ad|xn--g6w251d|xn--hgbk6aj7f53bba|xn--hlcj6aya9esc7a|xn--jxalpdlp|xn--kgbechtv|xn--zckzah|ye|yt|yu|za|zm|zw)",f="(?:"+c+n+"|"+h+")",o="(?:[;/][^#?<>\\s]*)?",e="(?:\\?[^#<>\\s]*)?(?:#[^<>\\s]*)?",d="\\b"+k+"[^<>\\s]+",a="\\b"+f+o+e+"(?!\\w)",m="mailto:",j="(?:"+m+")?[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@"+f+e+"(?!\\w)",l=new RegExp("(?:"+d+"|"+a+"|"+j+")","ig"),g=new RegExp("^"+k,"i"),b={"'":"`",">":"<",")":"(","]":"[","}":"{","B;":"B+","b :":"b 9"},i={callback:function(q,p){return p?'<a href="'+p+'" title="'+p+'">'+q+"</a>":q},punct_regexp:/(?:[!?.,:;'"]|(?:&|&amp;)(?:lt|gt|quot|apos|raquo|laquo|rsaquo|lsaquo);)$/};return function(u,z){z=z||{};var w,v,A,p,x="",t=[],s,E,C,y,q,D,B,r;for(v in i){if(z[v]===undefined){z[v]=i[v]}}while(w=l.exec(u)){A=w[0];E=l.lastIndex;C=E-A.length;if(/[\/:]/.test(u.charAt(C-1))){continue}do{y=A;r=A.substr(-1);B=b[r];if(B){q=A.match(new RegExp("\\"+B+"(?!$)","g"));D=A.match(new RegExp("\\"+r,"g"));if((q?q.length:0)<(D?D.length:0)){A=A.substr(0,A.length-1);E--}}if(z.punct_regexp){A=A.replace(z.punct_regexp,function(F){E-=F.length;return""})}}while(A.length&&A!==y);p=A;if(!g.test(p)){p=(p.indexOf("@")!==-1?(!p.indexOf(m)?"":m):!p.indexOf("irc.")?"irc://":!p.indexOf("ftp.")?"ftp://":"http://")+p}if(s!=C){t.push([u.slice(s,C)]);s=E}t.push([A,p])}t.push([u.substr(s)]);for(v=0;v<t.length;v++){x+=z.callback.apply(window,t[v])}return x||u}})();
