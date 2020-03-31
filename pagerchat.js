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
	const publicKeyArmored = localStorage.getItem(userName + '.pubkey');

	(async () => {
	    try {
		const { keys: [privateKey] } = await openpgp.key.readArmored(privateKeyArmored);
		if (passphrase != '') {
		    await privateKey.decrypt(passphrase);
		}

		const { data: decrypted } = await openpgp.decrypt({
		    message: await openpgp.message.readArmored(data.p),              // parse armored message
		    publicKeys: (await openpgp.key.readArmored(publicKeyArmored)).keys, // for verification (optional)
		    privateKeys: [privateKey]                                           // for decryption
		});

		lock = '&#x1f512;';
		let text = '<div class="text_box_1_dialog"><div class="box_user"><span class="name">'
			+ data.l + lock + '</span> -&gt; <span>' + data.d + " " + newm + '</span></div></div>';
		text = text + '<div class="text_box_2_dialog">' + linkify(convert_text(decrypted), linkify_options) + '</div>';

		el.innerHTML = text;
	    } catch(err) {
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
