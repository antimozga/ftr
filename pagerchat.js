var pagerReadyFlag = 0;
var pagerHistoryLastTime = 0;

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
	const publicKeyArmored2 = document.getElementById('pubkey2').value;;

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

		const { data: decrypted } = await openpgp.decrypt({
		    message: await openpgp.message.readArmored(data.p),  // parse armored message
		    publicKeys,                                          // for verification (optional)
		    privateKeys: [privateKey]                            // for decryption
		});

		lock = '&#x1f512;';
		let text = '<div class="text_box_1_dialog"><div class="box_user"><span class="name">'
			+ data.l + lock + '</span> -&gt; <span>' + data.d + " " + newm + '</span></div></div>';
		text = text + '<div class="text_box_2_dialog">' + linkify(convert_text(decrypted), linkify_options) + '</div>';

		el.innerHTML = text;
	    } catch(err) {
		console.log(err.message);
		el.innerHTML = '<span class="error">Ошибка расшифровки</span>';
	    }
	})();
    } else {
	let text = '<div class="text_box_1_dialog"><div class="box_user"><span class="name">'
		+ data.l + lock + '</span> -&gt; <span>' + data.d + " " + newm + '</span></div></div>';
	text = text + '<div class="text_box_2_dialog">' + data.p + '</div>';

	el.innerHTML = text;
    }
}

function pagerLoadJson()
{
    let el = document.getElementById('pager_history');
    let url = el.getAttribute('src');
    if (pagerHistoryLastTime != 0) {
	url = url + '&from=' + pagerHistoryLastTime;
    }
    fetch(url)
	.then(res => res.json())
	.then(data => {
	    let firstChild = el.firstChild;
	    for (let i in data) {
		if (i == 0) {
		    pagerHistoryLastTime = data[i].t;
		}
		let div1 = document.createElement('div');
		div1.className = 'dialog_box_text';
		if (pagerHistoryLastTime != 0) {
		    el.insertBefore(div1, firstChild);
		} else {
		    el.appendChild(div1);
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
}

function pgpSendMessage()
{
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
