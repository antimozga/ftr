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
	selectElementText(document.getElementById(msg_id));
	var output = getSelectionText();

	document.getElementById(selField).value += '[re]' + output + '[/re]\n';

	document.getElementById('heading').value=creator;
	document.getElementById(selField).focus();
}

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
