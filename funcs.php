<?php

function is_defined($name) {
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

function is_logged()
{
    global $database;

    $sth = $database->prepare('SELECT id, login, password FROM ForumUsers WHERE login="'.$_SESSION['myuser_name'].'"');
    $sth->execute();
    $row = $sth->fetch();

    if ($_SESSION['myuser_password'] == md5($row['password']) && ($row['id'] != 0)) {
	$_SESSION['myuser_id'] = $row['id'];

	$tim = time();
	$database->exec("UPDATE ForumUsers SET last_login = $tim WHERE id = ".$row['id'].";");

	return 1;
    }

    unset($_SESSION['myuser_name']);
    unset($_SESSION['myuser_password']);
    unset($_SESSION['myuser_id']);

    return 0;
}

function user_login($name, $password)
{
    global $database;

    $_SESSION['myuser_name'] = $name;
    $_SESSION['myuser_password'] = md5($password);

    if (is_logged()) {
	unset($_SESSION['user_temp_name']);
	header("location:index.php");
    }

    // can't login
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
    $search  = array('&',     '"',      '\'',     '<',    '>',    '[i]', '[/i]', '[b]', '[/b]', '[re]',   '[/re]'  );
    $replace = array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;', '<i>', '</i>', '<b>', '</b>', '<cite>', '</cite>');
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

function convert_youtube($string) {
    return preg_replace(
	"/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
	"<iframe width=\"420\" height=\"315\" src=\"//www.youtube.com/embed/$2\" allowfullscreen></iframe>",
	$string
    );
}

function convert_vkv($string) {
    return preg_replace(
	"/\s*[a-zA-Z\/\/:\.]*vk.com(\/[a-zA-Z0-9]+\?z=|\/)video([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
	" <iframe width=\"420\" height=\"315\" src=\"//video.vtomske.net/get_video.php?w=420&h=315&url=https://vk.com/video$2\" allowfullscreen></iframe>",
	$string
    );
}

function convert_tiktok($string) {
    return preg_replace(
	"/\s*[a-zA-Z\/\/:\.]*tiktok.com\/([\@a-zA-Z0-9\-_\.]+)\/video\/([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
	" <iframe width=\"420\" height=\"315\" src=\"//video.vtomske.net/get_video.php?w=420&h=315&url=https://www.tiktok.com/$1/video/$2\" allowfullscreen></iframe>",
	$string
    );
}

/**
 * Turn all URLs in clickable links.
 *
 * @param string $value
 * @param array  $protocols  http/https, ftp, mail, twitter
 * @param array  $attributes
 * @return string
 */
function linkify($value, $protocols = array('http', 'https'), array $attributes = array())
{
    // Link attributes
    $attr = '';
    foreach ($attributes as $key => $val) {
        $attr .= ' ' . $key . '="' . htmlentities($val) . '"';
    }

    $links = array();

    // Extract existing links and tags
    $value = preg_replace_callback('~(<a .*?>.*?</a>|<.*?>)~i', function ($match) use (&$links) { return '<' . array_push($links, $match[1]) . '>'; }, $value);

    // Extract text links for each protocol
    foreach ((array)$protocols as $protocol) {
	switch ($protocol) {
	    case 'http':
	    case 'https':   $value = preg_replace_callback('~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i', function ($match) use ($protocol, &$links, $attr) { if ($match[1]) $protocol = $match[1]; $link = $match[2] ?: $match[3]; return '<' . array_push($links, "<a $attr href=\"$protocol://$link\">$link</a>") . '>'; }, $value); break;
	    case 'mail':    $value = preg_replace_callback('~([^\s<]+?@[^\s<]+?\.[^\s<]+)(?<![\.,:])~', function ($match) use (&$links, $attr) { return '<' . array_push($links, "<a $attr href=\"mailto:{$match[1]}\">{$match[1]}</a>") . '>'; }, $value); break;
	    case 'twitter': $value = preg_replace_callback('~(?<!\w)[@#](\w++)~', function ($match) use (&$links, $attr) { return '<' . array_push($links, "<a $attr href=\"https://twitter.com/" . ($match[0][0] == '@' ? '' : 'search/%23') . $match[1]  . "\">{$match[0]}</a>") . '>'; }, $value); break;
	    default:        $value = preg_replace_callback('~' . preg_quote($protocol, '~') . '://([^\s<]+?)(?<![\.,:])~i', function ($match) use ($protocol, &$links, $attr) { return '<' . array_push($links, "<a $attr href=\"$protocol://{$match[1]}\">{$match[1]}</a>") . '>'; }, $value); break;
	}
    }

    // Insert all link
    return preg_replace_callback('/<(\d+)>/', function ($match) use (&$links) { return $links[$match[1] - 1]; }, $value);
}

function strip_tags_content($text, $tags = '', $invert = FALSE)
{
    preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
    $tags = array_unique($tags[1]);

    if(is_array($tags) AND count($tags) > 0) {
	if($invert == FALSE) {
	    return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
	} else {
	    return preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text);
	}
    } elseif ($invert == FALSE) {
	return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
    }

    return $text;
}

function remove_iframes($line)
{
    $endiframe = strpos($line, "</iframe>");
    if ($endiframe != FALSE) {
	$line1 = substr($line, $endiframe + 9);
	$line = substr($line, 0, $endiframe + 9);
	$line = $line.strip_tags_content($line1, "<b><i><cite><br>");
    }

    $line = preg_replace('#(<br\s?/?>){2,}#', '<br/><br/>', $line);
    return $line;
}

function clon_detector($str) {
    $arr1 = str_split($str,1);

    $u = 0;
    $c = 0;
    $l = 0;
    $o = 0;
    $s = 0;
    $sp = 0;

    if ((ord($arr1[0]) == 32) ||
	(ord($arr1[sizeof($arr1) - 1]) == 32)) {
	return true;
    }

    foreach ($arr1 as $letter) {
	$v = ord($letter);

	if ($sp == 1) {
	    //skip any char, but correct to skip 0x80-0xbf
	    $sp = 0;
	    continue;
	}

	if ($v == 0xc2 || $v == 0xc3) {
	    $sp = 1;
	    $s = $s + 1;
	}

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

    if (($l && $c) || $s) {
	return true;
    }

    return false;
}

function format_user_nick($post_nick, $post_nick_id, $user_login, $user_id)
{
    if (($post_nick == $user_login) && ($post_nick_id == $user_id) && ($user_id != 0)) {
	$clon = "";
	if (clon_detector($post_nick)) {
	    $clon = '<label class="cloned" title="Осторожно! Возможно фальшивый ник, смесь разных символов.">?</label>';
	}
	return '<a onclick="window.open(\'\',\'u\',\'scrollbars,width=620,height=350\');" target="u" href="showuser.php?id='.$user_id.'">'.$post_nick.'</a>'.$clon;
    }

    return $post_nick;
}

function redirect_without($wout)
{
    $uri = $_SERVER['REQUEST_URI'];
    $uri = substr($uri, 0, strpos($uri, $wout));
    header("Location: $uri", true, 301);
    exit();
}

?>
