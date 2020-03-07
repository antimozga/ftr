var selField = 'mess_text';

function doInsert(ibTag, ibClsTag, isSingle)
{
	var isClose = false;
	var obj_ta = eval('fombj.' + selField);

	if ( obj_ta.selectionEnd ) {
		var ss = obj_ta.selectionStart;
		var st = obj_ta.scrollTop;
		var es = obj_ta.selectionEnd;
		
		if (es <= 2) {
			es = obj_ta.textLength;
		}
		
		var start  = (obj_ta.value).substring(0, ss);
		var middle = (obj_ta.value).substring(ss, es);
		var end    = (obj_ta.value).substring(es, obj_ta.textLength);
		
		if (obj_ta.selectionEnd - obj_ta.selectionStart > 0) {
			middle = ibTag + middle + ibClsTag;
		} else {
			middle = ibTag + middle + ibClsTag;
		}
		
		obj_ta.value = start + middle + end;
		
		var cpos = ss + (middle.length);
		
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
	var range = document.createRange();
	range.selectNodeContents(el);
	var selection = window.getSelection();
	selection.removeAllRanges();
	selection.addRange(range);
}

function getSelectionText()
{
    var selectedText = "";
    if (window.getSelection) {
        selectedText = window.getSelection().toString();
    }
    return selectedText;
}

function reply(creator, msg_id)
{
	var output = getSelectionText();
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
	var output = getSelectionText();

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
			.replace(/<iframe.*>.*?<\/iframe>/ig,'')
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

function show_date()
{
	var days = ['воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'];
	var months = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
	var d = new Date();
	var day = days[d.getDay()];
	var date = d.getDate();
	var month = months[d.getMonth()];
	var year = d.getFullYear();

	document.getElementById("curr_day").innerHTML = day;
	document.getElementById("curr_date").innerHTML = date + " " + month + " " + year;
}

function show_weather()
{
	fetch('getweather.php').then(function(response) {
	    return response.text().then(function(text) {
		text += "document.getElementById(\"w_temp\").innerHTML = temp;"
		text += "document.getElementById(\"w_sign\").innerHTML = weatherSignWord;"
		text += "document.getElementById(\"w_press\").innerHTML = pressure;"
		text += "document.getElementById(\"w_wind\").innerHTML = wind;"
//		console.log(text);
		var x = document.createElement("SCRIPT");
		var t = document.createTextNode(text);
		x.appendChild(t);
		document.body.appendChild(x);
	    });
	});
}

function post(path, params, method, target) {
    method = method || "post"; // Set method to post by default if not specified.

    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);
    if (target != null) {
	form.setAttribute("target", target);
    }

    for(var key in params) {
	if(params.hasOwnProperty(key)) {
	    var hiddenField = document.createElement("input");
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
    var el = document.createElement('textarea');
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
var allOSB = [];
var mxh = '';

function init_cut() {
    // Set Variables
    allOSB = document.getElementsByClassName("text_box_2_mess");

    if (allOSB.length > 0) {
	mxh = window.getComputedStyle(allOSB[0]).getPropertyValue('max-height');
	mxh = parseInt(mxh.replace('px', ''));

	// Add read-more button to each OSB section
	for (var i = 0; i < allOSB.length; i++) {
	    var el = document.createElement("button");
	    el.innerHTML = "показать";
	    el.setAttribute("type", "button");
	    el.setAttribute("class", "read-more hid");

	    insertAfter(allOSB[i], el);
	}
    }

    // Add click function to buttons
    var readMoreButtons = document.getElementsByClassName("read-more");
    for (var i = 0; i < readMoreButtons.length; i++) {
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
 * modal functions
 *****************************************************************************/

var modal_stack = [];
var modal_stack_last = "";
var modal_in_use = 0;

var modal;
var modal_trigger;
var modal_closeButton;

var svg_loader = "<svg width=\"50\" height=\"50\" viewBox=\"0 0 50 50\">\
<path fill=\"#33CCFF\" d=\"M25,5A20.14,20.14,0,0,1,45,22.88a2.51,2.51,0,0,0,2.49,2.26h0A2.52,2.52,0,0,0,50,22.33a25.14,25.14,0,0,0-50,0,2.52,2.52,0,0,0,2.5,2.81h0A2.51,2.51,0,0,0,5,22.88,20.14,20.14,0,0,1,25,5Z\">\
<animateTransform attributeName=\"transform\" type=\"rotate\" from=\"0 25 25\" to=\"360 25 25\" dur=\"0.5s\" repeatCount=\"indefinite\"/>\
</path>\
</svg>";

function init_modal() {
    modal = document.querySelector(".modal");
    trigger = document.querySelector(".trigger");
    closeButton = document.querySelector(".close-button");

    //trigger.addEventListener("click", toggleModal);
    closeButton.addEventListener("click", modal_toggle);
    //window.addEventListener("click", windowOnClick);
}

function modal_toggle() {
    el = document.getElementById("modal-content");

    if (Array.isArray(modal_stack) && modal_stack.length) {
	el.innerHTML = svg_loader;
	modal_stack_last = modal_stack.pop();
	fetch(modal_stack_last).then(function(response) {
	    return response.text().then(function(text) {
		el = document.getElementById("modal-content");
		el.innerHTML = text;
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
		el = document.getElementById("modal-content");
		el.innerHTML = text;
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
    el = document.getElementById("modal-content");
    el.innerHTML = text;
    modal.classList.toggle("show-modal");
}

function load_modal(url) {
    el = document.getElementById("modal-content");
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
	    el = document.getElementById("modal-content");
	    el.innerHTML = text;
	});
    });
}

/*****************************************************************************
 * popup window
 *****************************************************************************/

function popup(id, text) {
    var popup = document.getElementById(id);
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
 * pager send
 *****************************************************************************/

function pager_post_submit(e, form)
{
    fetch(
	form.action,
	{
	    method: 'post',
	    body: new FormData(form)
	}
    ).then(function(response) {
	return response.text().then(function(text) {
//	    console.log("sent...");
	    modal_reload();
	});
    });

    e.preventDefault();
}

/*****************************************************************************
 * page update timer
 *****************************************************************************/

var page_timer = setInterval(page_updater, 10000);

async function page_fetch(url, el) {
	fetch(url).then(function(response) {
	    return response.text().then(function(text) {
//		const el = element;
		el.innerHTML = text;
	    });
	});
}

function page_updater() {
//    console.log("page_updater");
    var elements = document.getElementsByClassName("autorefresh");
    var names = '';
    for(var i = 0; i < elements.length; i++) {
	var url = elements[i].getAttribute("src");
	var element = elements[i];
	page_fetch(url, element);
/*
//	console.log("autorefresh " + url);
	fetch(url).then(function(response) {
	    return response.text().then(function(text) {
		const el = element;
//		if (el == null) {
//		    console.log("... null ...");
//		}
		el.innerHTML = text;
	    });
	});
 */
    }
}

function page_updater_stop() {
    clearInterval(page_timer);
}

/*
 *
 */

window.onload = function() {
    init_cut();
    init_modal();
}

