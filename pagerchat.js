var pagerReadyFlag = 0;
var pagerHistoryLastTime = 0;
var pagerHistoryFirstTime = 0;

function convert_invitation1(text) {
    var exp = /\binvite:\/\/([0-9]*)\@([0-9]*)/i;
    return text.replace(exp,"<button type=\"button\" onclick=\"return AddPrivateUser($1, $2)\">Добавить</button>"); 
}

function convert_invitation(text) {
    var exp = /\binvite:\/\/:([0-9]*)\@([0-9]*)/i;
    return convert_invitation1(text).replace(exp,"<button type=\"button\" onclick=\"return AddPrivateUser(0, $2, '$1')\">Добавить</button>"); 
}

function pagerInsertPost(el, data)
{
    let lock = '';
    let newm = '';

    if (data.n != 0) {
	newm = 'Новое';
    }

    if (data.e != 0) {
	const passphrase = localStorage.getItem(userName + '.passphrase');
	const privateKeyArmored = localStorage.getItem(userName + '.privkey');
	const publicKeyArmored1 = localStorage.getItem(userName + '.pubkey');
	const publicKeyArmored2 = (document.getElementById('pubkey2') != null) ? document.getElementById('pubkey2').value : publicKeyArmored1;

	const publicKeysArmored = [
		publicKeyArmored1,
		publicKeyArmored2
	];

	(async () => {
	    try {
		const { keys: [privateKey] } = await openpgp.key.readArmored(privateKeyArmored);
		if (passphrase != '') {
		    await privateKey.decrypt(passphrase);
		}

		const publicKeys = await Promise.all(publicKeysArmored.map(async (key) => {
		    return (await openpgp.key.readArmored(key)).keys;
		}));

		const { data: decrypted, signatures: signatures } = await openpgp.decrypt({
		    message: await openpgp.message.readArmored(data.p),  // parse armored message
		    publicKeys: openpgp.key.readArmored(publicKeyArmored2).keys,  // for verification (optional)
		    privateKeys: [privateKey]                            // for decryption
		});

		if (typeof signatures[0] === 'undefined') {
		    //lock = '&#x1F513;';
		    lock = `<svg viewBox="0 0 20 20" width="16px" class="svg_icon_error">
<title>Отсутствует подпись отправителя</title>
<path d="M4 8V6a6 6 0 1 1 12 0h-3v2h4a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-8c0-1.1.9-2 2-2h1zm5 6.73V17h2v-2.27a2 2 0 1 0-2 0zM7 6v2h6V6a3 3 0 0 0-6 0z"/>
</svg>`;
		} else if ((signatures[0].keyid.toHex() === publicKeys[0][0].keyPacket.keyid.toHex() && data.l === userName) ||
		    (signatures[0].keyid.toHex() === publicKeys[1][0].keyPacket.keyid.toHex() && data.l !== userName)) {
		    //lock = '&#x1f512;';
		    lock = `<svg viewBox="0 0 20 20" width="16px" class="svg_icon">
<title>Подпись отправителя проверена</title>
<path d="M4 8V6a6 6 0 1 1 12 0v2h1a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-8c0-1.1.9-2 2-2h1zm5 6.73V17h2v-2.27a2 2 0 1 0-2 0zM7 6v2h6V6a3 3 0 0 0-6 0z"/>
</svg>`;
		} else {
		    //lock = '&#x1F513;';
		    lock = `<svg viewBox="0 0 20 20" width="16px" class="svg_icon_error">
<title>Подпись отправителя отличается</title>
<path d="M4 8V6a6 6 0 1 1 12 0h-3v2h4a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-8c0-1.1.9-2 2-2h1zm5 6.73V17h2v-2.27a2 2 0 1 0-2 0zM7 6v2h6V6a3 3 0 0 0-6 0z"/>
</svg>`;
		}

		let text = '<div class="text_box_1_dialog"><div class="box_user"><span class="name">'
			+ data.l + lock + '</span> -&gt; <span>' + data.d + " " + newm + '</span></div></div>';
		text = text + '<div class="text_box_2_dialog">' + linkify(convert_invitation(convert_text(decrypted)), linkify_options) + '</div>';

		el.innerHTML = text;
	    } catch(err) {
		console.log(err.message);
		let text = '<div class="text_box_1_dialog"><div class="box_user"><span class="name">'
			+ data.l + lock + '</span> -&gt; <span>' + data.d + " " + newm + '</span></div></div>';
		text = text + '<div class="text_box_2_dialog"><span class="error">Ошибка расшифровки</span></div>';

		el.innerHTML = text;
	    }
	})();
    } else {
	let text = '<div class="text_box_1_dialog"><div class="box_user"><span class="name">'
		+ data.l + lock + '</span> -&gt; <span>' + data.d + " " + newm + '</span></div></div>';
	text = text + '<div class="text_box_2_dialog">' + linkify(convert_invitation(data.p), linkify_options) + '</div>';

	el.innerHTML = text;
    }
}

function pagerLoadJson(dir = 1)
{
    let el = document.getElementById('pager_history');
    let url = el.getAttribute('src');
    if (dir < 0) {
	if (pagerHistoryFirstTime != 0) {
	    url = url + '&before=' + pagerHistoryFirstTime;
	}
    } else {
	if (pagerHistoryLastTime != 0) {
	    url = url + '&from=' + pagerHistoryLastTime;
	}
    }
    fetch(url)
	.then(res => res.json())
	.then(data => {
	    let firstChild;
	    if (dir < 0) {
		firstChild = el.lastChild;
	    } else {
		firstChild = el.firstChild;
	    }
	    for (let i in data) {
		if (i == 0 && data[i].t > pagerHistoryLastTime) {
		    pagerHistoryLastTime = data[i].t;
		    if (pagerHistoryFirstTime == 0) {
			pagerHistoryFirstTime = pagerHistoryLastTime;
		    }
		}
		if (data[i].t < pagerHistoryFirstTime) {
		    pagerHistoryFirstTime = data[i].t;
		}

		let div1 = document.createElement('div');
		div1.className = 'dialog_box_text';
		if (dir < 0) {
//		    if (pagerHistoryFirstTime != 0) {
//			el.insertAfter(div1, firstChild);
//		    } else {
			el.appendChild(div1);
//		    }
		} else {
		    if (pagerHistoryLastTime != 0) {
			el.insertBefore(div1, firstChild);
		    } else {
			el.appendChild(div1);
		    }
		}
		pagerInsertPost(div1, data[i]);
	    }
	})
    .catch(err => console.error(err));
}


function pagerHistoryReset()
{
    pagerReadyFlag = 0;
}

function pagerHistoryLoad()
{
    pagerHistoryLastTime = 0;


    if (document.getElementById('pubkey2') &&
	document.getElementById('pubkey2').value != '') {
	document.getElementById('current_pubkey').value = localStorage.getItem(userName + '.pubkey');
	let statusIcon = `<a href="?reg=3#pager"><svg viewBox="0 0 20 20" width="16px" class="svg_button">
<title>Настройте ключ для защищенной переписки</title>
<path d="M4 8V6a6 6 0 1 1 12 0h-3v2h4a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-8c0-1.1.9-2 2-2h1zm5 6.73V17h2v-2.27a2 2 0 1 0-2 0zM7 6v2h6V6a3 3 0 0 0-6 0z"/>
</svg></a>`;

	if (document.getElementById('active_pubkey').value != '' &&
	    (document.getElementById('active_pubkey').value === document.getElementById('current_pubkey').value)) {
	    statusIcon = `<svg viewBox="0 0 20 20" width="16px" class="svg_icon">
<title>Переписка защищена шифрованием</title>
<path d="M4 8V6a6 6 0 1 1 12 0v2h1a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-8c0-1.1.9-2 2-2h1zm5 6.73V17h2v-2.27a2 2 0 1 0-2 0zM7 6v2h6V6a3 3 0 0 0-6 0z"/>
</svg>`;
	}
	document.getElementById('encrypted_status').innerHTML = statusIcon;
    }

    pagerLoadJson();

    pagerReadyFlag = 1;
}

function pagerHistoryUpdate()
{
    if (!pagerReadyFlag) {
	return;
    }

    pagerLoadJson();
}

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
	    document.getElementById('dialog_mess').value = '';
	    document.getElementById('dialog_mess2').value = '';
	    pagerHistoryUpdate();
	});
    });

    e.preventDefault();
    return false;
}

function pgpSendMessage()
{
    if (document.getElementById('active_pubkey').value == '' ||
	(document.getElementById('active_pubkey').value !== document.getElementById('current_pubkey').value)) {
	    pager_post_submit(event, document.getElementById("pager_message_form_default"));
	    return false;
    }

    let message = document.getElementById('dialog_mess').value;
    const passphrase = localStorage.getItem(userName + '.passphrase');
    const privateKeyArmored = localStorage.getItem(userName + '.privkey');
    const publicKeyArmored1 = localStorage.getItem(userName + '.pubkey');
    const publicKeyArmored2 = document.getElementById('pubkey2').value;;

    const publicKeysArmored = [
	    publicKeyArmored1,
	    publicKeyArmored2
	];

    (async () => {
	try {
	    openpgp.config.compression = openpgp.enums.compression.zlib;

	    const { keys: [privateKey] } = await openpgp.key.readArmored(privateKeyArmored);
	    if (passphrase != '') {
		await privateKey.decrypt(passphrase);
	    }

	    const publicKeys = await Promise.all(publicKeysArmored.map(async (key) => {
		return (await openpgp.key.readArmored(key)).keys[0];
	    }));

	    const { data: encrypted } = await openpgp.encrypt({
		message: openpgp.message.fromText(message),
		publicKeys,
		privateKeys: [privateKey]                                           // for signing (optional)
	    });

	    document.getElementById('dialog_mess2').value = encrypted;

	    pager_post_submit(event, document.getElementById("pager_message_form"));
	} catch(err) {
	    console.log(err.message);
	}
    })();

    return false;
}

function pagerHistoryScroll(el)
{
    if (el.scrollHeight - el.offsetHeight - el.scrollTop < 1) {
	pagerLoadJson(-1);
    }
}
