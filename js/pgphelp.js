function pgpRegError(err)
{
    if (err != null) {
	    console.log(err.message);
	    if (err.message === 'Incorrect key passphrase') {
		document.getElementById('lbpassphrase').innerHTML = ' - Неправильный пароль!';
		document.getElementById('lbprivkey').innerHTML = '';
	    } else if (err.message === 'Error generating keypair: Invalid user id format') {
		document.getElementById('pgpregerror').innerHTML = 'Проверьте формат записи электронной почты!';
		document.getElementById('lbpassphrase').innerHTML = '';
		document.getElementById('lbprivkey').innerHTML = '';
	    } else if (err.message === 'Key packet is already decrypted.') {
		document.getElementById('pgpregerror').innerHTML = 'Ключ уже установлен!';
		document.getElementById('lbpassphrase').innerHTML = '';
		document.getElementById('lbprivkey').innerHTML = '';
	    } else {
		document.getElementById('lbpassphrase').innerHTML = '';
		document.getElementById('lbprivkey').innerHTML = ' - Неправильный ключ!';
	    }
    } else {
	document.getElementById('pgpregerror').innerHTML = '';
	document.getElementById('lbpassphrase').innerHTML = '';
	document.getElementById('lbprivkey').innerHTML = '';
	document.getElementById('pgpregwarn').innerHTML = '';
    }
}

function pgpRegGetPubKey()
{
    window.setTimeout(function() {
	passPhrase  = document.getElementById('passphrase').value;
	privateKeyArmored = document.getElementById('privkey').value;

	(async () => {
	    try {
		const { keys: [privateKey] } = await openpgp.key.readArmored(privateKeyArmored);
		await privateKey.decrypt(passPhrase);

		publicKeyArmored = privateKey.toPublic().armor();

		document.getElementById('pubkey').value = publicKeyArmored;
		pgpRegError(null);

		localStorage.setItem(userName + '.passphrase', passPhrase);
		localStorage.setItem(userName + '.privkey', privateKeyArmored); 
		localStorage.setItem(userName + '.pubkey', publicKeyArmored);

	    } catch(err) {
		pgpRegError(err);
	    }
	})();
    }, 100);
}

function pgpRegSetKey(submit = 0)
{
    passPhrase  = document.getElementById('passphrase').value;
    privateKeyArmored = document.getElementById('privkey').value;
    login = document.getElementById('login').value;
    email = document.getElementById('email').value;

    if (privateKeyArmored === '') {
	(async () => {
	    try {
		const { privateKeyArmored, publicKeyArmored, revocationCertificate } = await openpgp.generateKey({
		    userIds: [{ name: login, email: email }],
		    curve: 'ed25519',                                           // ECC curve name
		    passphrase: passPhrase
		});

		localStorage.setItem(userName + '.passphrase', passPhrase);
		localStorage.setItem(userName + '.privkey', privateKeyArmored);
		localStorage.setItem(userName + '.pubkey', publicKeyArmored);

		document.getElementById('passphrase').value = localStorage.getItem(userName + '.passphrase');
		document.getElementById('privkey').value = localStorage.getItem(userName + '.privkey');
		document.getElementById('pubkey').value = localStorage.getItem(userName + '.pubkey');

		pgpRegError(null);

		if (submit != 0) {
		    document.getElementById("regeditform").submit();
		} else {
		    pgpRegInit();
		}
	    } catch (err) {
		pgpRegError(err);
	    }
	})();
    } else {
	(async () => {
	    try {
		const { keys: [privateKey] } = await openpgp.key.readArmored(privateKeyArmored);
		if (passPhrase != '') {
		    await privateKey.decrypt(passPhrase);
		}

		publicKeyArmored = privateKey.toPublic().armor();

		document.getElementById('pubkey').value = publicKeyArmored;

		localStorage.setItem(userName + '.passphrase', passPhrase);
		localStorage.setItem(userName + '.privkey', privateKeyArmored);
		localStorage.setItem(userName + '.pubkey', publicKeyArmored);

		pgpRegError(null);

		if (submit != 0) {
		    document.getElementById("regeditform").submit();
		} else {
		    pgpRegInit();
		}
	    } catch(err) {
		pgpRegError(err);
	    }
	})();
    }

    return false;
}

function pgpRegResetKey()
{
    document.getElementById('pubkey').value = '';

    pgpRegError(null);

    document.getElementById("regeditform").submit();

    return false;
}

function pgpRegRemoveKey()
{
    document.getElementById('passphrase').value = '';
    document.getElementById('privkey').value = '';
    document.getElementById('pubkey').value = '';

    localStorage.removeItem(userName + '.passphrase');
    localStorage.removeItem(userName + '.privkey');
    localStorage.removeItem(userName + '.pubkey');

    pgpRegError(null);

    pgpRegInit();

    return false;
}

function pgpRegInit()
{
    document.getElementById('passphrase').value = localStorage.getItem(userName + '.passphrase');
    document.getElementById('privkey').value = localStorage.getItem(userName + '.privkey');
    document.getElementById('pubkey').value = localStorage.getItem(userName + '.pubkey');
    document.getElementById('x_pubkey').value = localStorage.getItem(userName + '.pubkey');

    let el = document.getElementById('addremove_key_button');

    if (document.getElementById('active_pubkey').value != '') {
	if (document.getElementById('pubkey').value === document.getElementById('active_pubkey').value) {
	    document.getElementById('privkey').readOnly = true;
	    document.getElementById('passphrase').readOnly = true;
	    el.innerHTML = '<button type="button" class="btn_reg_right" onclick="return pgpRegRemoveKey();">Удалить локально</button>';
	} else {
	    if (document.getElementById('pubkey').value != '') {
		document.getElementById('pgpregwarn').innerHTML = 'Используемый ключ шифрования отличается от добавленного локально. Удалите и добавьте правильный закрытый ключ или запретите текущий и активируйте локальный.';
		el.innerHTML = '<button type="button" class="btn_reg_right" onclick="return pgpRegRemoveKey();">Удалить локально</button>';
	    } else {
		document.getElementById('pgpregwarn').innerHTML = 'Шифрование включено на сервере, но не настроено на устройстве. Скопируйте ваш закрытый ключ, пароль и добавьте локально.';
		el.innerHTML = '<button type="button" class="btn_reg_right" onclick="return pgpRegSetKey();">Добавить локально</button>';
	    }
	}
    } else {
	if (document.getElementById('pubkey').value != '') {
	    el.innerHTML = '<button type="button" class="btn_reg_right" onclick="return pgpRegRemoveKey();">Удалить локально</button>';
	} else {
	    el.innerHTML = '<button type="button" class="btn_reg_right" onclick="return pgpRegSetKey();">Добавить локально</button>';
	}
    }
}
