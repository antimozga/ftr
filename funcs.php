<?php
function isdefined($name) {
    if (isset($_REQUEST[$name])) {
	return true;
    }
    return false;
}

function is_session($var) {
    if (isset($_SESSION[$var])) {
	return true;
    } else {
	return false;
    }
}

function is_forum_admin() {
    global $FORUM_ADMIN;
    if (isset($_SESSION['myuser_name'])) {
	if ($_SESSION['myuser_name'] == $FORUM_ADMIN) {
	    return true;
	}
    }
    return false;
}

function convert_text($str)
{
    $search  = array('&',     '"',      '\'',     '<',    '>',    '[i]', '[/i]', '[b]', '[/b]', '[re]',                   '[/re]' );
    $replace = array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;', '<i>', '</i>', '<b>', '</b>', '<div class="box_cite">', '</div>');
    $newstr  = str_replace($search, $replace, $str);

    $order   = array("\r\n", "\n", "\r");
    $replace = '<br/>';
    return str_replace($order, $replace, $newstr);
}

function convert_string($str)
{
    $search  = array('&',     '"',      '\'',     '<',    '>');
    $replace = array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;');
    $newstr  = str_replace($search, $replace, $str);

    $order   = array("\r\n", "\n", "\r");
    $replace = '';
    return str_replace($order, $replace, $newstr);
}

function clon_detector($str) {
    $arr1 = str_split($str,1);

    $u = 0;
    $c = 0;
    $l = 0;
    $o = 0;

    foreach ($arr1 as $letter) {
	$v = ord($letter);
	if ($v >= 0xd0 && $v <= 0xd3 && $u == 0) {
	    $u = 1;
	    continue;
	}
	if ($v >= 0x80 && $v <= 0xbf && $u == 1) {
	    $c = $c + 1;
	} else {
	    if (($v >= 0x41 && $v <= 0x5a) || ($v >= 0x61 && $v <= 0x7a)) {
		$l = $l + 1;
	    } else {
		$o = $o + 1;
	    }
	}
	$u = 0;
    }
    if ($l && $c) {
	return true;
    }
    return false;
}

function format_user_nick($post_nick, $post_nick_id, $user_login, $user_id)
{
    if (($post_nick == $user_login) && ($post_nick_id == $user_id) && ($user_id != 0)) {
	$clon = "";
	if (clon_detector($post_nick)) {
	    $clon = '<label class="cloned" title="Осторожно! Возможно фальшивый ник, смесь кириллицы и латиницы.">?</label>';
	}
	return '<a onclick="window.open(\'\',\'u\',\'scrollbars,width=620,height=350\');" target="u" href="showuser.php?id='.$user_id.'">'.$post_nick.'</a>'.$clon;
    }

    return $post_nick;
}
?>
