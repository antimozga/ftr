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
	    document.getElementById(selField).value = '[re]' + output + '[/re]\n';
	}

	document.getElementById('heading').value=creator;
	document.getElementById(selField).focus();
}
