<?php

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

setcookie ('PHPSESSID', $_COOKIE['PHPSESSID'], time() + 60 * 60 * 24 * 7, '/');
session_start();

if (isset($_SESSION['ajsner'])) {
    if ($_SESSION['ajsner'] > 100) {
	header($_SERVER["SERVER_PROTOCOL"]." 503 Service Temporarily Unavailable", true, 503);
	$retryAfterSeconds = 240;
	header('Retry-After: ' . $retryAfterSeconds);
	echo '<h1>503 Service Temporarily Unavailable</h1>';
	exit;
    }
}

$time_now = microtime_float();

$time_diff = $time_now - $_SESSION['lkasdas'];

//error_log("access to ".$_SERVER['HTTP_HOST']." ".$_SERVER['REQUEST_URI']." ".$_COOKIE['PHPSESSID']." ".$_SESSION['lkasdas']." ".$time_diff." ".$_SESSION['ajsner']);
//error_log("time diff ".$time_diff);

$_SESSION['lkasdas'] = $time_now;

if ($time_diff < 1) {
    if (!isset($_SESSION['ajsner'])) {
	$_SESSION['ajsner'] = 1;
    } else {
	$_SESSION['ajsner']++;
    }

    if ($_SESSION['ajsner'] > 1) {
	header($_SERVER["SERVER_PROTOCOL"]." 503 Service Temporarily Unavailable", true, 503);
	$retryAfterSeconds = 240;
	header('Retry-After: ' . $retryAfterSeconds);
	echo '<h1>503 Service Temporarily Unavailable</h1>';
	exit;
    }
} else {
	$_SESSION['ajsner'] = 0;
}

$debug = false;

require_once('config.php');
require_once('config_user.php');


include('funcs.php');
include('header.php');
include('footer.php');

function start_page($title) {
global $FORUM_RULES_LINK;
global $database;

show_header($title);
echo 
'<div class="block_menu">
    <div class="menu">
	<div>';

$group_edit = "";
if(!is_session('myuser_name')) {
    echo '  <form action="" method="post"><input type="hidden" name="event" value="login"/>
	    <div class="form_box_text">
		<input type="text" autocomplete="username" style="width:10%;" name="user[name]" onfocus="if(this.value == \'Имя\') { this.value = \'\'; }" value="Имя"/>
		<input type="password" autocomplete="current-password" style="width:10%;" name="user[password]" onfocus="if(this.value == \'Пароль\') { this.value = \'\'; }" value="Пароль"/>
		<input class="btn_group_sel" type="submit" value="&nbsp;" />
	    </div>
	    </form>
	</div>
	<div class="sep"><div></div></div>
	<div><a href="?reg=1">Регистрация</a></div>';
} else {
    $pt = $database->query('SELECT COUNT(*) FROM ForumPager WHERE id_user = '.$_SESSION['myuser_id'].';')->fetchColumn();
    $pn = $database->query('SELECT COUNT(*) FROM ForumPager WHERE id_user = '.$_SESSION['myuser_id'].' AND new = 1;')->fetchColumn();
    echo '<a class="name_m" href="?logout">'.$_SESSION['myuser_name'].' Выход</a>
	</div>
	<div class="sep"><div></div></div>
	<div><a href="?pager">Пейджер (<span>'.$pn.'</span>&nbsp;|&nbsp;'.$pt.')</a></div>
	<div class="sep"><div></div></div>
	<div><a href="?reg=3">Настройки</a></div>';
    if (is_forum_admin()) {
	$group_edit = '<div class="sep"><div></div></div><div><a href="groups.php">Редактор групп тем</a></div>';
    }
}

echo '<div class="sep"><div></div></div>
	<div><a href="?users">Пользователи</a><a href="./?banlist">(Скрытые)</a></div>
	<div class="sep"><div></div></div>
	<div><a href="'.$FORUM_RULES_LINK.'">Правила</a></div>'
.$group_edit.
'<!--	<div><a href="contacts.php">Контакты</a></div> -->
    </div>
</div>';
}

function show_menu($database) {
    echo '<div class="block_menu_m">
<form class="group_sel" action="">
<select onchange="document.location=\'?g=\'+this.value;" name="g">
<option value="0" selected="selected">Группы тем:</option>
<option value="0">-----------------------</option>
    ';

    $group_query = "SELECT * FROM ForumGroups ORDER BY grp ASC;";
    foreach ($database->query($group_query) as $row) {
	print ("<option value='{$row['id']}'>{$row['grp']}</option>");
    }

    echo '</select>
<input class="btn_group_sel" type="submit" value="&nbsp;" />
</form>
<div class="menu">
<div><a href="./?g=0">Группы тем</a></div>
<div class="sep"><div></div></div>
<div><a href="./">Горячее</a></div>
<div class="sep"><div></div></div>
<div><a href="./?s=1">Топ общения</a></div>';
    if (is_session('myuser_name')) {
	echo '<div class="sep"><div></div></div>
<div><a href="./?m=1">Избранное</a></div>';
    }
    echo '<div class="sep"><div></div></div>
    <div><a href="./?search">Поиск</a></div>
</div></div>';

//<div><form action=""><input type="text" name="search" onfocus="if(this.value == \'Поиск по темам...\') { this.value = \'\'; }" value="Поиск по темам..."/><input class="btn_group_sel" type="submit" value="&nbsp;"/></form></div>

}

function show_banner()
{

    echo '<div class="block1">';

    echo '<div class="logo"><a href="./"><img src="images/ftrclogo.png"></a></div>';
//    echo '<div class="logo"><a href="./"><img src="images/logo.png"></a></div>';

    echo '<div class="weather">';

echo '<span class="w_title">Погода</span>
<span class="w_temp" id="w_temp"></span>
<br/><span id="w_sign"></span>
<br/><span id="w_press"></span>
<br/><span id="w_wind"></span>
<br/>
<br/>
<b id="curr_day"></b>
<br/>
<span id="curr_date"></span>
<script>show_date(); show_weather();</script>';

    echo '</div>';

    echo '</div>';
}

function show_nav_path($topic, $ctrlink="") {
    global $FORUM_NAME;
    echo '<div class="navigation">
    <div class="box_path">:: <a href="./">'.$FORUM_NAME.'</a> &nbsp;/&nbsp; '.$topic;
    if ($ctrlink != "") {
	echo $ctrlink;
    }
    echo '</div></div>';
}

function show_postbox($type) {
    global $database;
    global $debug;
    global $RECAPTCHA_SITE_KEY;
    $error = "";
    $name = "";
    $subj = "";
    $post = "";
    $edit_info = "";

    if (is_defined('editpost')) {
	$edit_post_id = $_REQUEST['editpost'];
	$edit_post_id = ($edit_post_id * 10) / 10;

	$id_session = md5(session_id());

//echo '<!-- SELECT id, time, nick, subj, post, mod_time FROM ForumPosts WHERE id_session="'.$id_session.'"; -->';

	$sth = $database->prepare("SELECT id, time, nick, subj, post, modtime".
				  " FROM ForumPosts".
				  " WHERE id_session=\"$id_session\" AND id=$edit_post_id");
	$sth->execute();
	$row = $sth->fetch();
	if ($row['id'] != "") {
//	    echo "-> ".$row['id']." ".$row['login']." ".$row['password']." <-\n";
	    $_SESSION['user_temp_name'] = $row['nick'];
	    $_SESSION['user_temp_subj'] = $row['subj'];
	    $_SESSION['user_temp_post'] = reconvert_text($row['post']);
	    $edit_info = '<input type="hidden" name="edit_post_info" value="'.$edit_post_id.'">';
	}
    } else if (is_session('user_edit_post')) {
	$edit_info = '<input type="hidden" name="edit_post_info" value="'.$_SESSION['user_edit_post'].'">';
    }

    if (isset($_SESSION['user_temp_name'])) {
	$name = $_SESSION['user_temp_name'];
    }

    if (isset($_SESSION['user_temp_subj'])) {
	$subj = $_SESSION['user_temp_subj'];
    }

    if (isset($_SESSION['post_error_message'])) {
	$error = "<div class=\"error1\">* ".$_SESSION['post_error_message']."</div>";
	unset($_SESSION['post_error_message']);
    }

    if (isset($_SESSION['user_temp_post'])) {
	$post = $_SESSION['user_temp_post'];
    }

    if ($type == 'topic') {
	$h = 'Заголовок темы';
	$b = 'Добавить тему';
    } else {
	$h = 'Заголовок сообщения';

	if (is_defined('editpost') || is_session('user_edit_post')) {
	    $b = 'Исправить сообщение';
	    unset($_SESSION['user_edit_post']);
	} else {
	    $b = 'Добавить сообщение';
	}
    }
echo '
<script src="https://www.google.com/recaptcha/api.js?render='.$RECAPTCHA_SITE_KEY.'"></script>
<script>
function formSubmit () {';

if ($debug) {
    echo '    document.getElementById(\'formMessage\').submit();';
} else {
    echo '    grecaptcha.ready(function () {
	grecaptcha.execute(\''.$RECAPTCHA_SITE_KEY.'\', { action: \'post\' }).then(function (token) {
	    var recaptchaResponse = document.getElementById(\'recaptchaResponse\');
	    recaptchaResponse.value = token;
	    document.getElementById(\'formMessage\').submit();
	});
    });';
}

echo '    return false;
}
</script>
<div class="line1"></div>
<div>
	<form action="" method="post" class="form_mess" name="formMessage" id="formMessage" enctype="multipart/form-data" onsubmit="return formSubmit()">
	<input type="hidden" name="event" value="forumcreatesubj">
	<div class="form_box">
		<div class="form_box_name">
			<label for="name" class="l_inp_text_name"> Ваше имя:</label>
			<input class="inp_text_name" id="name" maxlength="20" name="message[author]" value="'.$name.'" type="text">
		</div>
		<div class="form_box_title">
		    <label for="heading" class="l_inp_text_name">'.$h.':</label>
		    <input class="inp_text_name" id="heading" maxlength="80" name="message[caption]" value="'.$subj.'" type="text">
		</div>
		<div class="form_box_mess">
			<textarea maxlength="8192" class="area_text" id="mess_text" name="message[content]" onFocus="javascript: textFocus = true;" onBlur="javascript: textFocus = false;">'.$post.'</textarea>
		</div>
		<div class="form_box_btn">
			<input class="btn_form" value="'.$b.'" type="submit">
		</div>
		<div class="format">
			<div id="web"></div>
			<span id="mess_emo"><a href="" onclick="doInsert(\'[b]\',\'[/b]\', true); return false;" class="for1" id="bold">Жирный</a>&nbsp;-&nbsp;<a href="" onclick="doInsert(\'[i]\',\'[/i]\', false); return false;" class="for2">Курсив</a>&nbsp;-&nbsp;<a href="" onclick="doInsert(\'[re]\',\'[/re]\', false); return false;" class="for3">Цитата</a>&nbsp;-&nbsp;</span>
		</div>
		<input type="hidden" name="MAX_FILE_SIZE" value="3145728">
		    <table class="form_box_image_btn">
		    <tr><td>
		    <input name="image" type="file">
		    </td></tr>
		    <tr><td>
		    <label class="upload_file" for="image" >Картинка JPG,PNG,GIF,WEBP/Видео MP4,OGV,WEBM (макс. размер 3МБ)</label>
		    </td></tr>
		    </table>
	</div>
	<input type="hidden" name="recaptcha_response" id="recaptchaResponse">
'.$edit_info.'
	</form>
'.$error.'
</div>
<script language="javascript" type="text/javascript">
<!--var 
fombj = document.getElementById("formMessage");
//-->
</script>

<script src="js/emojiPicker.js"></script>
<script>
    (() => {
      new EmojiPicker(document.getElementById(\'mess_text\'), document.getElementById(\'mess_emo\'))
    })()
</script>
<div class="line1"></div>
';
}

function show_page_control($type, $page, $pages, $pageprev, $pagenext)
{
    echo '<div class="paging">
    <form class="paging_sel" action="" method="post">
	<b>Страница: </b>&nbsp;
	<select class="pagsel" name="p">';
    if ($pages > 0) {
	$cnt = 1;
	$total = $pages;
	while ($total > 0) {
	    if ($page == $cnt) {
		echo '<option selected value="'.$cnt.'">';
	    } else {
		echo '<option value="'.$cnt.'">';
	    }
	    echo $total.'</option>';
	    $cnt = $cnt + 1;
	    $total = $total - 1;
	}
    }
    echo '
	</select>
	<input class="btn_paging_sel" value="&nbsp;" type="submit">
	&nbsp;из '.$pages.'
    </form>';
    if ($pagenext != "") {
	echo '<span class="prev"><a href="'.$pagenext.'">Назад »</a></span>';
    } else {
	echo '<span class="prev">Назад »</span>';
    }
    if ($pageprev != "") {
	echo '<span class="next"><a href="'.$pageprev.'">« Вперед</a></span>';
    } else {
	echo '<span class="next">« Вперед</span>';
    }
    if ($type == 'down') {
	echo '<a name="ftop"></a><span class="up_down">';
	echo '<a href="#bottom">Вниз</a></span>';
    } else {
	echo '<a name="bottom"></a><span class="up_down">';
	echo '<a href="#ftop">Вверх</a></span>';
    }
echo '</div>';
}

$database = new PDO("sqlite:" . DBASEFILE);

if (!$database) {
    print("<b>Ошибка базы данных.</b>");
} else {
    $query = "CREATE TABLE IF NOT EXISTS ForumPosts " .
	     "(id INTEGER PRIMARY KEY, time DATE, id_grp INTEGER, id_topic INTEGER, id_user INTEGER, nick NVARCHAR, subj NVARCHAR, post NVARCHAR, id_session NVARCHAR, attachment NVARCHAR, modtime INTEGER);";
    $database->exec($query);

    $query = "CREATE TABLE IF NOT EXISTS ForumTopics " .
	     "(id INTEGER PRIMARY KEY, id_grp INTEGER, id_user INTEGER, nick VARCHAR, topic VARCHAR, view INTEGER DEFAULT 0, id_session NVARCHAR);";
    $database->exec($query);

    $query = "CREATE TABLE IF NOT EXISTS ForumGroups " .
	     "(id INTEGER PRIMARY KEY, grp VARCHAR, note VARCHAR);";
    $database->exec($query);

    $query = "CREATE TABLE IF NOT EXISTS ForumUserLike " .
	     "(id INTEGER PRIMARY KEY, id_user INTEGER, id_like INTEGER, type INTEGER);";
    $database->exec($query);

    $query = "CREATE TABLE IF NOT EXISTS ForumUsers " .
	     "(id INTEGER PRIMARY KEY, login VARCHAR, password VARCHAR, email VARCHAR, fio VARCHAR, gender INTEGER, description VARCHAR, time INTEGER, last_login INTEGER);";
    $database->exec($query);

    $query = "REPLACE INTO ForumUsers (id, login) VALUES (0, 'Анонимно');";
    $database->exec($query);

    $query = "CREATE TABLE IF NOT EXISTS ForumPager " .
	     "(id INTEGER PRIMARY KEY, id_user INTEGER,  id_from_user INTEGER, new INTEGER, time INTEGER, subj VARCHAR, post VARCHAR);";
    $database->exec($query);

    is_logged();

    {
	$show_groups = 0;
	$id_grp = 0;
	$id_topic = 0;
	$id_user = 0;
	$show_hot = 0;
	$page = 1;
	$topic = "ТОП ".$MAX_PAGE_ENTRIES." ГОРЯЧИХ ТЕМ";
	$search_opt = "";
	$reg_mode = 0;
	$show_search = 0;
	$show_users = 0;
	$show_pager = 0;
	$show_trash_topics = $SHOW_TRASH_TOPICS;
	$show_mylist = 0;
	$show_banlist = 0;

	if (is_defined('unban')) {
	    $session_id = addslashes($_REQUEST['unban']);
	    if (is_session('banlist')) {
		$arr = $_SESSION['banlist'];
		$pos = array_search($session_id, $arr);
		unset($arr[$pos]);
		$arr = array_values($arr);
		$_SESSION['banlist'] = $arr;
		$uri = $_SERVER['REQUEST_URI'];
		$uri = substr($uri, 0, strpos($uri, '&unban'));
		header("Location: $uri", true, 301);
		exit();
	    }
	} elseif (is_defined('ban')) {
	    $session_id = addslashes($_REQUEST['ban']);
	    if ($session_id != "") {
		if (is_session('banlist')) {
		    array_push($_SESSION['banlist'], $session_id);
		} else {
		    $_SESSION['banlist'] = array($session_id);
		}
	    }
	    $uri = $_SERVER['REQUEST_URI'];
	    $uri = substr($uri, 0, strpos($uri, '&ban'));
	    header("Location: $uri", true, 301);
	    exit();
	}

	if (is_defined('sdel')) {
	    if (is_forum_admin()) {
		$session_id = addslashes($_REQUEST['sdel']);
		if ($session_id != "") {
		    $database->exec("DELETE FROM ForumPosts  WHERE id_topic IN (SELECT id FROM ForumTopics WHERE id_session = \"$session_id\")");
		    $database->exec("DELETE FROM ForumTopics WHERE id_session = \"$session_id\"");
		}
	    }
	    $uri = $_SERVER['REQUEST_URI'];
	    $uri = substr($uri, 0, strpos($uri, '&sdel'));
	    header("Location: $uri", true, 301);
	    exit();
	}


	if (is_defined("logout")) {
	    unset($_SESSION['myuser_name']);
	    unset($_SESSION['myuser_password']);
	    unset($_SESSION['myuser_id']);
	    unset($_SESSION['user_temp_name']);

	    header("location:index.php");
	}

	if (isset($_SESSION['myuser_id'])) {
	    $id_user = $_SESSION['myuser_id'];
	}

	unset($_SESSION['user_temp_subj']);
	unset($_SESSION['user_temp_post']);

	if (is_defined("event")) {
	    $cmd = $_REQUEST["event"];
	    if ($cmd == "login") {
		$myuser_name		= convert_string($_REQUEST["user"]["name"]);
		$myuser_password	= convert_string($_REQUEST["user"]["password"]);

		user_login($myuser_name, $myuser_password);
	    }
	}

	if (is_forum_admin()) {
	    if (is_defined("dp")) {
		$dp = $_REQUEST["dp"];
		$dp = ($dp * 10) / 10;
		$dt = $database->query("SELECT id_topic FROM ForumPosts WHERE id = ".$dp)->fetchColumn();
		$query = "DELETE FROM ForumPosts WHERE id = ".$dp.";";
		$database->exec($query);
		/* remove topic if last post was removed */
		$query = "DELETE FROM ForumTopics WHERE id = ".$dt." AND (SELECT COUNT(*) FROM ForumPosts WHERE id_topic = ".$dt.") = 0;";
		$database->exec($query);
	    }
	    if (is_defined("dt")) {
		$dt = $_REQUEST["dt"];
		$dt = ($dt * 10) / 10;
		if (is_defined("trash")) {
		    $query = "UPDATE ForumPosts SET id_grp = ".$FORUM_TRASH_GID." WHERE id_topic = ".$dt.";";
		    $database->exec($query);
		    $query = "UPDATE ForumTopics SET id_grp = ".$FORUM_TRASH_GID." WHERE id = ".$dt.";";
		    $database->exec($query);
		} else {
		    $query = "DELETE FROM ForumPosts WHERE id_topic = ".$dt.";";
		    $database->exec($query);
		    $query = "DELETE FROM ForumTopics WHERE id = ".$dt.";";
		    $database->exec($query);
		}
	    }
	}

	if (is_defined("p")) {
	    $page = $_REQUEST["p"];
	    if ($page < 1 ) {
		$page = 1;
	    }
	}

	if (is_defined("g")) {
	    $topic = "ГРУППЫ ТЕМ";
	    $id_grp = $_REQUEST["g"];
	    $id_grp = ($id_grp * 10) / 10;
	    if ($id_grp != 0) {
		$group_query = "SELECT grp FROM ForumGroups WHERE id = $id_grp;";
		foreach ($database->query($group_query) as $row) {
		    $topic = $row['grp'];
		}
	    } else {
		$topic = "ГРУППЫ ТЕМ";
		$show_groups = 1;
	    }

	    start_page($topic);
	    show_banner();
	    show_menu($database);
	    show_nav_path("<a href=\"?g=".$id_grp."\">".$topic."</a>");
	} else if (is_defined("t")) {
	    $id_topic = $_REQUEST["t"];
	    $id_topic = ($id_topic * 10) / 10;
	
	    if ($id_user != 0 && is_defined("like")) {
		$id_like = $_REQUEST["like"];
		$id_like = ($id_like * 10) / 10;
		if ($id_like) {
		    $like_request = "REPLACE INTO ForumUserLike(id_user, id_like, type) VALUES($id_user, $id_topic, 0);";
		    $database->exec($like_request);
		} else {
		    $like_request = "DELETE FROM ForumUserLike".
				    " WHERE id_user = $id_user AND id_like = $id_topic AND type = 0;";
		    $database->exec($like_request);
		}
	    }
	
	    $topic_query = "SELECT ForumTopics.id_grp AS id_grp, ForumTopics.topic AS topic, ForumTopics.id AS id_topic, ForumGroups.grp AS grp FROM ForumTopics, ForumGroups WHERE ForumTopics.id = $id_topic AND ForumTopics.id_grp = ForumGroups.id;";
	    foreach ($database->query($topic_query) as $row) {
		$topic = $row['topic'];
		$id_topic = $row['id_topic'];
		$group = $row['grp'];
		$id_group = $row['id_grp'];
	    }
	    $ctrlink = "";
	    if ($id_user != 0) {
		if ($database->query("SELECT id_user FROM ForumUserLike WHERE id_user = ".$id_user." AND id_like = ".$id_topic." AND type = 0;")->fetchColumn() == $id_user) {
		    $ctrlink = '<a style="float: right" href="?t='.$id_topic.'&like=0">-</a>';
		} else {
		    $ctrlink = '<a style="float: right" href="?t='.$id_topic.'&like=1">+</a>';
		}
	    }

	    start_page($topic);
	    show_banner();
	    show_menu($database);
	    show_nav_path("<a href=\"?g=".$id_group."\">".$group."</a> &nbsp;/&nbsp; <a href=\"?t=".$id_topic."\">".$topic."</a>", $ctrlink);
	} else if (is_defined("reg")) {
	    $reg_mode = $_REQUEST["reg"];
	    if ($reg_mode == 3 && $id_user == 0) {
		$reg_mode = 1;
	    }
	    if ($reg_mode == 1 || $reg_mode == 2) {
	        $topic = "РЕГИСТРАЦИЯ ПОЛЬЗОВАТЕЛЯ";
	    } else {
	        $topic = "НАСТРОЙКИ ПОЛЬЗОВАТЕЛЯ";
	    }

	    start_page($topic);
	    show_banner();
	    show_menu($database);
	    show_nav_path($topic);
	} else if (is_defined("users")) {
	    $show_users = 1;
	    $show_users_string = convert_string($_REQUEST["users"]);

	    $topic = "ПОЛЬЗОВАТЕЛИ";

	    start_page($topic);
	    show_banner();
	    show_menu($database);
	    show_nav_path("Список пользователей");
	} else if (is_defined("pager")) {
	    $show_pager = 1;

	    $topic = "ПЕЙДЖЕР";

	    start_page($topic);
	    show_banner();
	    show_menu($database);
	    show_nav_path("Пейджер");
	} else if (is_defined("search")) {
	    $s = convert_string($_REQUEST["search"]);

	    if ($s == "") {
		$show_search = 1;

		$topic = "ПОИСК";
	    } else {
		$search_opt = " AND ForumTopics.topic LIKE '%".$s."%'";
	    }

	    start_page($topic);
	    show_banner();
	    show_menu($database);
	    show_nav_path("Поиск");
	} else if (is_defined("banlist")) {
	    $show_banlist = 1;

	    $topic = "СКРЫТЫЕ";

	    start_page($topic);
	    show_banner();
	    show_menu($database);
	    show_nav_path("Скрытые");
	} else {
	    if (is_defined("s")) {
		$show_hot = 1;
		$topic = "ТОП ".$MAX_PAGE_ENTRIES." ОБЩЕНИЯ";
	    }
	    if (is_defined("m")) {
		$show_mylist = 1;
		$topic = "МОИ ИЗБРАННЫЕ ТЕМЫ";
	    }

	    start_page($topic);
	    show_banner();
	    show_menu($database);
	    show_nav_path($topic);
	}

	if (is_defined("event")) {
	    $cmd = $_REQUEST["event"];
	    if ($cmd == "forumcreatesubj") {
		$nick = convert_string($_REQUEST["message"]["author"]);
		$subj = convert_string($_REQUEST["message"]["caption"]);
		$post = convert_text($_REQUEST["message"]["content"]);
		$post_id = "";
		$id_session = md5(session_id());

		if (strlen($post) > 8192) {
		    $post = substr($post, 0, 8191);
		}

		if (isset($_POST['recaptcha_response']) || $debug) {
		    // Build POST request:
		    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
		    //    $recaptcha_secret = 'YOUR_RECAPTCHA_SECRET_KEY';
		    $recaptcha_response = $_POST['recaptcha_response'];

		    // Make and decode POST request:
		    $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $RECAPTCHA_SERV_KEY . '&response=' . $recaptcha_response);
		    $recaptcha = json_decode($recaptcha);

		    // Take action based on the score returned:
		    echo "<!-- recaptcha $recaptcha->success $recaptcha->score -->";

		    if ($recaptcha->score >= 0.5 || $debug) {
			if ($nick == "") {
			    unset($_SESSION['user_temp_name']);
			} else {
			    $_SESSION['user_temp_name'] = $nick;
			}

			$tim = time();

			if ($nick == "" && $id_user != 0) {
			    $nick = $database->query("SELECT login FROM ForumUsers WHERE id = '$id_user'")->fetchColumn();
			}

			if ($nick == "") {
			    $nick = "Анонимно";
			}
			if ($id_topic != 0) {
			    if ($post != "") {
				if (isset($_POST['edit_post_info'])) {
				    $edit_post_id = $_POST['edit_post_info'];
				    $edit_post_id = ($edit_post_id * 10) / 10;

				    $sth = $database->prepare("SELECT id, time, nick, subj, post, modtime".
							      " FROM ForumPosts".
							      " WHERE id_session=\"$id_session\" AND id=$edit_post_id");
				    $sth->execute();
				    $row = $sth->fetch();
				    if ($row['id'] != "") {
					$modtime = time();

					$database->exec("UPDATE ForumPosts SET id_user='$id_user', nick='$nick', subj='$subj', post='$post', modtime=$modtime  WHERE id = $edit_post_id");
					$post_id = $row['id'];
				    }
				} else {
				    $query = "INSERT INTO ForumPosts (id, time, id_grp, id_topic, id_user, nick, subj, post, id_session)" .
					     "VALUES (NULL, '$tim', (SELECT id_grp FROM ForumTopics WHERE id = '$id_topic'), $id_topic, $id_user, '$nick', '$subj', '$post', '$id_session');";
				    $database->exec($query);
				    $post_id = $database->lastInsertId();
				}
			    }
			} else {
			    if ($post != "" && $subj != "") {
				if ($database->query("SELECT id FROM ForumTopics WHERE topic = '$subj'")->fetchColumn() == "") {
				    $query = "REPLACE INTO ForumTopics (id, id_grp, id_user, nick, topic, view, id_session)" .
					     "VALUES ((SELECT id FROM ForumTopics WHERE topic = '$subj'), $id_grp,".
					     " coalesce((SELECT id_user FROM ForumTopics WHERE topic = '$subj'), $id_user),".
					     " coalesce((SELECT nick FROM ForumTopics WHERE topic = '$subj'),'$nick'), '$subj',".
					     " coalesce((SELECT view FROM ForumTopics WHERE topic = '$subj'), 0),".
					     " coalesce((SELECT id_session FROM ForumTopics WHERE topic = '$subj'), '$id_session'));";
				    $database->exec($query);
				    $query = "INSERT INTO ForumPosts (id, time, id_grp, id_topic, id_user, nick, subj, post, id_session)" .
					     "VALUES (NULL, '$tim', $id_grp, (SELECT id FROM ForumTopics WHERE topic = '$subj'), $id_user, '$nick', '$subj', '$post', '$id_session');";
				    $database->exec($query);
				    $post_id = $database->lastInsertId();
				} else {
				    $_SESSION['post_error_message'] = "Тема с таким заголовком уже существует!";
				}
			    } else {
				if ($subj == "") {
				    $_SESSION['post_error_message'] = "Заголовок темы не может быть пустым!";
				}
			    }
			}

			if ($post == "") {
			    $_SESSION['post_error_message'] = "Сообщение не может быть пустым!";
			}

			if (is_session('post_error_message')) {
			    $_SESSION['user_temp_name'] = $nick;
			    $_SESSION['user_temp_subj'] = $subj;
			    $_SESSION['user_temp_post'] = $_REQUEST["message"]["content"];

			    if (isset($_POST['edit_post_info'])) {
				$_SESSION['user_edit_post'] = $_POST['edit_post_info'];
			    }
			} else {
			    $image = $_FILES['image']['name'];
			    $image_tmp = $_FILES['image']['tmp_name'];
			    $image_ext = strtolower(substr(strrchr($image, '.'), 1));

			    if ($post != "" && $post_id != "") {
				if ($image_ext == 'jpg'  || $image_ext == 'jpeg' || $image_ext == 'gif'   || $image_ext == 'png' ||
				    $image_ext == 'webp' ||
				    $image_ext == 'mp4'  || $image_ext == 'mpg4' || $image_ext == 'mpeg4' || $image_ext == 'ogv' ||
				    $image_ext == 'webm') {

				    $img_file = "att-$post_id.$image_ext";

				    if ($image_ext == 'jpg'  || $image_ext == 'jpeg'  || $image_ext == 'gif' || $image_ext == 'png' ||
					$image_ext == 'webp') {
					system("convert -resize 80x60 -quality 85 ".$image_tmp." ".$UPLOAD_DIR."/small-".$img_file);
				    }

				    move_uploaded_file($image_tmp, "$UPLOAD_DIR/$img_file");

				    $database->exec("UPDATE ForumPosts SET attachment = \"$img_file\" WHERE id = $post_id;");
				}
			    }

			    unset($_SESSION['user_temp_subj']);
			    unset($_SESSION['user_temp_post']);
			    unset($_SESSION['user_edit_post']);
			}
		    } else {
			// Not verified - show form error
			$_SESSION['user_temp_name'] = $nick;
			$_SESSION['user_temp_subj'] = $subj;
			$_SESSION['user_temp_post'] = $_REQUEST["message"]["content"];

			if (isset($_POST['edit_post_info'])) {
			    $_SESSION['user_edit_post'] = $_POST['edit_post_info'];
			}

			$_SESSION['post_error_message'] = "Робот обнаружен? Попробуйте изменить текст или отправить его чуть позже.";
		    }
		}
	    } else if ($cmd == "createuser" || $cmd == "updateuser") {
		$user_name		= convert_string($_REQUEST["user"]["user_name"]);
		$user_password		= convert_string($_REQUEST["user"]["user_password"]);
		$user_password_confirm	= convert_string($_REQUEST["user"]["user_password_confirm"]);
		$user_email		= convert_string($_REQUEST["user"]["user_email"]);
		$user_fio		= convert_string($_REQUEST["user"]["user_fio"]);
		$user_gender		= convert_string($_REQUEST["user"]["user_gender"]);
		$user_description	= convert_text($_REQUEST["user"]["description"]);
		if ($user_name == "") {
		    $user_name_warning = '<div class="error">Имя пользователя не может быть пустым.</div>';
		} else if ($user_password == "") {
		    $user_password_warning = '<div class="error">Пароль не может быть пустым.</div>';
		    $user_password = "";
		    $user_password_config = "";
		} else if ($user_password != $user_password_confirm) {
		    $user_password_warning = '<div class="error">Пароль и его подтверждение не совпадают.</div>';
		    $user_password = "";
		    $user_password_config = "";
		} else if ($user_email == "") {
		    $user_email_warning = '<div class="error">E-mail не может быть пустым.</div>';
		} else if ($cmd == "updateuser") {
			$query = "UPDATE ForumUsers SET login = '$user_name', password = '$user_password', email = '$user_email', fio = '$user_fio', gender = '$user_gender', description = '$user_description' WHERE id = $id_user;";
			$database->exec($query);
			$reg_mode = 4;
		} else {
		    $view_query = "SELECT login FROM ForumUsers WHERE login LIKE '$user_name' ;";
		    foreach ($database->query($view_query) as $row) {
			$login = $row['login'];
		    }
		    if ($login != $user_name) {
			$tim = time();
			$query = "REPLACE INTO ForumUsers (id, login, password, email, fio, gender, description, time, last_login)" .
			    "VALUES (NULL, '$user_name', '$user_password', '$user_email', '$user_fio', '$user_gender', '$user_description', $tim, 0);";
			$database->exec($query);
			$reg_mode = 2;
		    } else {
			$user_name_warning = '<div class="error">Имя пользователя уже занято.</div>';
			$user_name = "";
		    }
		}

		$image = $_FILES['image']['name'];
		$image_tmp = $_FILES['image']['tmp_name'];
		$image_ext = strtolower(substr(strrchr($image, '.'), 1));

		if ($reg_mode == 2 || $reg_mode == 4) {
		    if ($image_ext == "jpg" || $image_ext == "jpeg" || $image_ext == "gif" || $image_ext == "png") {
			$view_query = "SELECT id FROM ForumUsers WHERE login LIKE '$user_name' ;";
			foreach ($database->query($view_query) as $row) {
			    $image_id = $row['id'];
			}
			$img_file = "id".$image_id.".".$image_ext;
			if (is_uploaded_file($image_tmp)) {
			    copy($image_tmp, $UPLOAD_DIR."/".$img_file);
			    $tmpimg = tempnam("/tmp", "MKUP");
			    system("convert ".$image_tmp." pnm:".$tmpimg);
			    system("pnmscale -xy 70 70 ".$tmpimg." | cjpeg -qual 85 >".$image_tmp);
			    copy($image_tmp, $UPLOAD_DIR."/small-".$img_file);
			    unlink($tmpimg);
			}
		    }
		}
	    }
	}

	if ($id_grp != 0) {
	    show_postbox('topic');
	} else if ($id_topic != 0) {
	    show_postbox('post');
	}

	if ($show_pager == 1) {
	    function make_href($href, $id, $name) {
		return '<a onclick="window.open(\'\',\'u\',\'scrollbars,width=620,height=350\');" target="u" href="'.$href.$id.'">'.$name.'</a>';
	    }
	    $pager_query = "SELECT COUNT(*) as total, ForumPager.id_from_user AS id_from_user, ForumUsers.login AS login FROM ForumPager,ForumUsers"
		." WHERE ForumPager.id_user = ".$id_user." AND ForumUsers.id = ForumPager.id_from_user GROUP BY ForumPager.id_from_user;";
	    echo '<table class="userstable"><tr><th>&nbsp;</th><th colspan=2 align="left">Пользователь</th><th>Сообщений</th></tr>';
	    foreach ($database->query($pager_query) as $row) {
		$pn = $database->query('SELECT COUNT(*) FROM ForumPager WHERE id_user = '.$id_user.' AND id_from_user = '.$row['id_from_user'].' AND new = 1;')->fetchColumn();
		echo '<tr><td></td><td>'.make_href("showuser.php?id=", $row['id_from_user'], $row['login']).'</td><td>'.$pn.'</span>&nbsp;|&nbsp;'.$row['total'].'</td><td>'.make_href("pager.php?new=", $row['id_from_user'], "Читать").'</td></tr>';
	    }
	    echo '</table>';
	} else if ($show_search == 1) {
	    echo '
<div class="block1">
	<form action="">
		<input type="text" name="search" onfocus="if(this.value == \'Поиск по темам...\') { this.value = \'\'; }" value="Поиск по темам..."/><input class="btn_group_sel" type="submit" value="&nbsp;"/>
	</form>
</div>';
	} else if ($show_users == 1) {
	    function lower_ru($str) {
		return mb_strtolower($str, 'utf-8');
	    }
	    function mb_str_split( $string ) {
        	return preg_split('/(?<!^)(?!$)/u', $string );
	    }
	    function show_letters_links($str) {
		foreach (mb_str_split($str) as $letter) {
		    echo '<a href="?users='.$letter.'">'.$letter.'</a> ';
		}
	    }
	    echo '<div class="box_alfavit">';
	    $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	    show_letters_links($str);
	    echo ' &nbsp;&nbsp; ';
	    $str = "АБВГДЕЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ";
	    show_letters_links($str);
	    echo '</div>
<table class="userstable">
<tbody>
<tr>
    <td colspan="5">
    <form action="" method="get">
	<span>Поиск по нику:</span>
        <input id="user_filter" name="users" value="" type="text"/>
	<input value="Поиск" type="submit"/>
    </form>
    </td>
</tr>
<tr><th>На форуме</th>
<th>Пол</th>
<th>Фото</th>
<th>Время посещения</th>
<th>Дата регистрации</th>
</tr>';
	    if (mb_strlen($show_users_string, 'utf-8') > 1) {
		$view_query = "SELECT id, login, last_login, time, gender FROM ForumUsers WHERE login LIKE '".$show_users_string."%';";
	    } else {
		$view_query = "SELECT id, login, last_login, time, gender FROM ForumUsers WHERE login LIKE '".$show_users_string."%' OR login LIKE '".lower_ru($show_users_string)."%';";
	    }
	    $g = array(1 => 'Не имеет значения', 2 => 'Мужской', 3 => 'Женский', 4 => 'Средний');
	    foreach ($database->query($view_query) as $row) {
		if ($row['id'] == 0) {
		    continue;
		}
		echo '<tr>';
		echo '<td>'.format_user_nick($row['login'], $row['id'], $row['login'], $row['id']).'</td>';
		echo '<td class="tdw1">'.$g[$row['gender']].'</td>';
		$avatar = $UPLOAD_DIR."/small-id".$row['id'].".jpg";
		if (file_exists($avatar)) {
		    echo '<td class="tdw2">'.'ЕСТЬ'.'</td>';
		} else {
		    echo '<td class="tdw2">'.'НЕТ'.'</td>';
		}
		echo '<td class="tdu3">'.date('d.m.Y (H:i)', $row['last_login']).'</td>';
		echo '<td class="tdu3">'.date('d.m.Y (H:i)', $row['time']).'</td>';
		echo '</tr>';
	    }
		echo '</tbody>
</table>
';
	} else if ($reg_mode == 1 || $reg_mode == 3) {
	    echo '
<div class="box_pasport">
	<div class="box_pasport_bg">';

	    $user_password = "";

	    if ($reg_mode == 1) {
		echo '
		<h3>Регистрация пользователя</h3>
		Для регистрации на Форуме Вам необходимо заполнить форму. Поля, обязательные для заполнения, обозначены значком (*).
	<form action="" method="post" class="form_reg" name="registration" enctype="multipart/form-data">
	<input type="hidden" name="event" value="createuser">
	<label for="login">* Имя пользователя (login) '.$user_name_warning.'</label>
	<input type="text" autocomplete="username" class="inp_text_reg" name="user[user_name]" id="login" maxlength="20" value="'.$user_name.'">
	<div class="box_small_text">Если выбранное Вами имя уже зарегистрировано, Вы сможете просто ввести другое имя, при этом остальные заполненные поля будут сохранены.</div>
		';
	    } else {
		$user_query = "SELECT login, password, email, fio, gender, description, last_login FROM ForumUsers WHERE id = $id_user;";
		foreach ($database->query($user_query) as $row) {
		    $user_name = $row['login'];
		    $user_password = $row['password'];
		    $user_email = $row['email'];
		    $user_fio = $row['fio'];
		    $user_gender = $row['gender'];
		    $user_description = $row['description'];
		}

		echo '
		<h3>Настройки пользователя</h3>
		Изменение личных настроек пользователя.
	<form action="" method="post" class="form_reg" name="registration" enctype="multipart/form-data">
	<input type="hidden" name="event" value="updateuser">
	<label for="login">* Имя пользователя </label>
	<input type="text" class="inp_text_reg" name="user[user_name]" id="login" maxlength="20" value="'.$user_name.'" readonly="readonly">
	<div class="box_small_text">Вы не можете изменить имя пользователя.</div>
		';
	    }
	    echo '
	<div class="line2"><div></div></div>
	<label for="password1">* Пароль '.$user_password_warning.'</label>
	<input type="password" autocomplete="new-password" class="inp_text_reg" name="user[user_password]" id="password1" maxlength="100" value="'.$user_password.'">
	<label for="password2">* Подтверждение пароля </label>
	<input type="password" autocomplete="new-password" class="inp_text_reg" name="user[user_password_confirm]" id="password2" maxlength="100" value="'.$user_password.'">
	<div class="box_small_text">При наборе пароля допускаются любые буквы (как русские, так и латинские) и символы. Пароль регистрозависим (советуем перед набором глянуть на Caps Lock)</div>
	<div class="line2"><div></div></div>
	<label for="email">* Ваш e-mail '.$user_email_warning.'</label>
	<input type="text" class="inp_text_reg" name="user[user_email]" id="email"  value="'.$user_email.'">
	<div class="line2"><div></div></div>
	<label for="fio">Имя Фамилия Отчество</label>
	<input type="text" class="inp_text_reg" name="user[user_fio]" id="fio"  value="'.$user_fio.'">
	<label>Ваш пол</label>';
	$cnt = 1;
	foreach(array('Не имеет значения','Мужской','Женский','Средний') as $name) {
	    if ($cnt == $user_gender) {
		echo '<input type="radio" name="user[user_gender]" value="'.$cnt.'" id="'.$cnt.'" class="radioinput" checked="checked" >';
	    } else {
		echo '<input type="radio" name="user[user_gender]" value="'.$cnt.'" id="'.$cnt.'" class="radioinput" >';
	    }
	    echo '<label for="'.$cnt.'" class="radiolab">'.$name.'</label><br>';
	    $cnt = $cnt + 1;
	}
	echo '
	<label for="addition">Дополнительно</label>
	<textarea maxlength="4096" name="user[description]" id="addition" class="area_text_reg">'.$user_description.'</textarea>
	<input type="hidden" name="MAX_FILE_SIZE" value="500000">
	<label for="image">Аватар:</label><input name="image" type="file">
	<div class="box_small_text">Разрешается загрузить картинку jpg, png или gif и размером не более 500КБ.</div>
	<div class="line2"><div></div></div>
	<div class="box_small_text">Если вся информация верна - нажмите кнопку (достаточно одного раза):</div>';
	    if ($reg_mode == 1) {
		echo '<input type="submit" class="btn_reg" value="Зарегистрироваться">';
	    } else {
		echo '<input type="submit" class="btn_reg" value="Сохранить">';
	    }
	echo '
	</form>
	</div>
</div>
';
	} else if ($reg_mode == 2) {
	    echo '
<div class="box_pasport">
<div class="box_pasport_bg">
<h3>Регистрация пользователя завершена!</h3>
Теперь вы можете войти на форум под своим именем пользователя.
</div>
</div>
';
	} else if ($reg_mode == 4) {
	    echo '
<div class="box_pasport">
<div class="box_pasport_bg">
<h3>Настройки пользователя изменены!</h3>
Вы всегда можете изменить свои личные настройки.
</div>
</div>
';
	} else if ($show_groups != 0) {
	    $group_query = "SELECT id, grp, note FROM ForumGroups ORDER BY grp ASC ;";
	    foreach ($database->query($group_query) as $row) {
		$topics = 0;
		$updated = 0;
		$view_query = "SELECT COUNT(*) as topics, (SELECT MAX(time) FROM ForumPosts WHERE id_grp = {$row['id']}) as time FROM ForumTopics WHERE id_grp = {$row['id']} GROUP BY id_grp;";
		foreach ($database->query($view_query) as $row1) {
		    $topics = $row1['topics'];
		    $updated = $row1['time'];
		    if ($updated != "") {
			$updated = date('d.m.Y H:i', $updated);
		    }
		}
		print "<div class=\"box1\"><a href=\"?g={$row['id']}\" class=\"title\">{$row['grp']}</a> {$row['note']}<br><span class=\"white\">Тем: <span class=\"bold\">".$topics."</span>&nbsp;|&nbsp;Обновление: <span class=\"bold\">".$updated."</span></span></div>";
	    }
	} else if ($show_banlist != 0) {
	    $banned = 0;

	    echo '<table class="userstable">';

	    if (is_session('banlist')) {
		foreach($_SESSION['banlist'] as $ban_id_session) {
		    $bannick = "";
		    foreach($database->query("SELECT DISTINCT nick, id_user FROM ForumPosts WHERE id_session = \"$ban_id_session\"") as $row) {
			if ($row['id_user'] != 0) {
			    $bannick = format_user_nick($row['nick'], $row['id_user'], $row['nick'], $row['id_user'])." $bannick";
			} else {
			    $bannick = $row['nick']." $bannick";
			}
			$banned++;
		    }
		    if ($bannick == "") {
			$bannick = "Пользователь удален";
		    }
//echo "<!-- bannick $bannick -->";
		    $request = $_SERVER['REQUEST_URI'].'&unban='.$ban_id_session;
		    echo '<tr><td>'.$bannick.'</td><td class="tdu1"><a href="'.$request.'">'.
			 '<svg viewBox="0 0 20 20" width="16px">'.
			 '<title>Показать пользователя и его темы</title>'.
			 '<path fill="#CCCCCC" d="M.2 10a11 11 0 0 1 19.6 0A11 11 0 0 1 .2 10zm9.8 4a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm0-2a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/>'.
			 '</svg>'.
			 '</a></td></tr>';
		}
	    }

	    if ($banned == 0) {
		echo '<tr><td colspan="2">Пока тут никого нет...</td></tr>';
	    }

	    echo '</table>';

	} else if ($id_topic == 0) {
	    $posts = 0;
	    $pnext = "";
	    $pprev = "";
	    $base_query = "SELECT ForumTopics.nick AS nick, ForumTopics.id_user AS id_user, ForumTopics.view AS view,".
			  " ForumUsers.login AS login, ForumUsers.id AS id, COUNT(*) AS posts, ForumPosts.time AS time,".
			  " ForumTopics.topic AS topic, ForumPosts.id_topic AS id_topic, ForumPosts.nick AS last_nick,".
			  " ForumPosts.id_user AS last_id_user, ForumGroups.grp AS grp".
			  " FROM ForumPosts, ForumTopics, ForumUsers, ForumGroups".
			  " WHERE ForumPosts.id_topic = ForumTopics.id AND ForumGroups.id = ForumTopics.id_grp ";
	    $count_query = "SELECT COUNT(*)".
			   " FROM ForumTopics WHERE 1 ";

	    if (!$show_trash_topics && $id_grp == 0) {
	        $base_query   = "$base_query  AND ForumTopics.id_grp != $FORUM_TRASH_GID $search_opt";
		$count_query  = "$count_query AND ForumTopics.id_grp != $FORUM_TRASH_GID $search_opt";
	    }

	    if ($id_grp != 0) {
		$base_query  = "$base_query  AND ForumPosts.id_grp = $id_grp AND ForumUsers.id = ForumTopics.id_user";
		$count_query = "$count_query AND ForumTopics.id_grp = $id_grp";

//echo "<!-- 1count_query $count_query -->";

		$posts = $database->query($count_query)->fetchColumn();

		$numentry = ($page - 1) * $MAX_PAGE_ENTRIES;
		if ($numentry + $MAX_PAGE_ENTRIES < $posts) {
		    $pnext = "?g=".$id_grp."&p=".($page + 1);
		}
		if ($page > 1) {
		    $pprev = "?g=".$id_grp."&p=".($page - 1);
		}

		show_page_control('down', $page, ceil($posts / $MAX_PAGE_ENTRIES), $pprev, $pnext);

		$view_query = "$base_query GROUP BY id_topic ORDER BY ForumPosts.time DESC LIMIT $numentry,$MAX_PAGE_ENTRIES;";
	    } else {
		if ($show_mylist != 0) {
		    $view_query = "SELECT ForumTopics.nick AS nick, ForumTopics.id_user AS id_user, ForumTopics.view AS view,".
				    " ForumUsers.login AS login, ForumUsers.id AS id, COUNT(*) AS posts,".
				    " ForumPosts.time AS time, ForumTopics.topic AS topic, ForumPosts.id_topic AS id_topic,".
				    " ForumPosts.nick AS last_nick, ForumPosts.id_user AS last_id_user, ForumGroups.grp AS grp".
				    " FROM ForumPosts, ForumTopics, ForumUsers, ForumGroups, ForumUserLike".
				    " WHERE ForumPosts.id_topic = ForumTopics.id AND ForumGroups.id = ForumTopics.id_grp".
				    " AND ForumUsers.id = ForumTopics.id_user".
				    " AND ForumTopics.id = ForumUserLike.id_like AND ForumUserLike.id_user = $id_user AND ForumUserLike.type = 0 ".
				    " GROUP BY id_topic ORDER BY ForumPosts.time DESC;";
		} else {
		    $base_query  = "$base_query  AND ForumUsers.id = ForumTopics.id_user";
		}

		$ban_opts = "";
		if (is_session('banlist')) {
		    foreach($_SESSION['banlist'] as $ban_id_session) {
			if ($ban_opts == "") {
			    $ban_opts = "ForumTopics.id_session != \"$ban_id_session\"";
			} else {
			    $ban_opts = "$ban_opts AND ForumTopics.id_session != \"$ban_id_session\"";
			}
		    }
		    if ($ban_opts != "") {
			$ban_opts = "AND ($ban_opts OR ForumTopics.id_session IS NULL)";
		    }

		    $base_query = "$base_query $ban_opts";
		    $count_query = "$count_query $ban_opts";
		}

//echo "<!-- 2count_query $count_query -->";

		$posts = $database->query($count_query)->fetchColumn();

		$numentry = ($page - 1) * $MAX_PAGE_ENTRIES;
		if ($numentry + $MAX_PAGE_ENTRIES < $posts) {
		    $pnext = "?p=".($page + 1);
		}
		if ($page > 1) {
		    $pprev = "?p=".($page - 1);
		}

		show_page_control('down', $page, ceil($posts / $MAX_PAGE_ENTRIES), $pprev, $pnext);

		if ($show_mylist != 0) {
		    $view_query = "SELECT ForumTopics.nick AS nick, ForumTopics.id_user AS id_user, ForumTopics.view AS view,".
				    " ForumUsers.login AS login, ForumUsers.id AS id, COUNT(*) AS posts,".
				    " ForumPosts.time AS time, ForumTopics.topic AS topic, ForumPosts.id_topic AS id_topic,".
				    " ForumPosts.nick AS last_nick, ForumPosts.id_user AS last_id_user, ForumGroups.grp AS grp".
				    " FROM ForumPosts, ForumTopics, ForumUsers, ForumGroups, ForumUserLike".
				    " WHERE ForumPosts.id_topic = ForumTopics.id AND ForumGroups.id = ForumTopics.id_grp".
				    " AND ForumUsers.id = ForumTopics.id_user".
				    " AND ForumTopics.id = ForumUserLike.id_like AND ForumUserLike.id_user = $id_user AND ForumUserLike.type = 0 ".
				    " GROUP BY id_topic ORDER BY ForumPosts.time DESC;";
		} elseif ($show_hot == 0) {
		    $view_query = "$base_query GROUP BY id_topic ORDER BY ForumPosts.time DESC LIMIT $numentry,$MAX_PAGE_ENTRIES;";
		} else {
		    $view_query = "$base_query GROUP BY id_topic ORDER BY posts DESC LIMIT $numentry,$MAX_PAGE_ENTRIES;";
		}
	    }

	    echo '<table class="themes">';

	    foreach ($database->query($view_query) as $row) {
		$timestamp = date('H:i m/d', $row['time']);

		$name = format_user_nick($row['nick'], $row['id_user'], $row['login'], $row['id']);

		print("<tr>" .
		  "<td class='tdw1'>{$timestamp}</td>" .
		  "<td class='tdw3'><a href=\"?t={$row['id_topic']}\" title=\"{$row['grp']}\">{$row['topic']}</a> [{$row['view']}/{$row['posts']} - {$row['last_nick']}]");
		if (is_forum_admin()) {
		    $rmargs = "";
		    if ($id_grp != 0) {
			$rmargs = 'g='.$id_grp.'&p='.$page.'&dt='.$row['id_topic'];
		    } else if ($show_hot != 0) {
			$rmargs = 's=1&dt='.$row['id_topic'];
		    } else {
			$rmargs = 'dt='.$row['id_topic'];
		    }
		    echo '<a href="?'.$rmargs.'" class="remove">Удалить</a>';
		    if ($id_grp != $FORUM_TRASH_GID) {
			echo '<a href="?'.$rmargs.'&trash=1" class="remove">Мусор</a>';
		    }
		}
		print("</td>".
		  "<td class='tdw2'>".$name."</td>".
		  "</tr>"
		);

	    }
	    echo '</table>';

	    show_page_control('up', $page, ceil($posts / $MAX_PAGE_ENTRIES), $pprev, $pnext);
	} else {
	    $posts = 0;
	    $pnext = "";
	    $pprev = "";

	    $ban_opts = "";
	    if (is_session('banlist')) {
		foreach($_SESSION['banlist'] as $ban_id_session) {
		    if ($ban_opts == "") {
			$ban_opts = "ForumPosts.id_session != \"$ban_id_session\"";
		    } else {
			$ban_opts = "$ban_opts AND ForumPosts.id_session != \"$ban_id_session\"";
		    }
		}
		if ($ban_opts != "") {
		    $ban_opts = "AND ($ban_opts OR ForumPosts.id_session IS NULL)";
		}
	    }

//echo "<!-- ban_opts $ban_opts -->";

	    $posts = $database->query("SELECT COUNT(*) FROM ForumPosts WHERE id_topic = $id_topic $ban_opts;")->fetchColumn();

	    $numentry = ($page - 1) * $MAX_PAGE_ENTRIES;
	    if ($numentry + $MAX_PAGE_ENTRIES < $posts) {
		$pnext = "?t=".$id_topic."&p=".($page + 1);
	    }
	    if ($page > 1) {
		$pprev = "?t=".$id_topic."&p=".($page - 1);
	    }
	    show_page_control('down', $page, ceil($posts / $MAX_PAGE_ENTRIES), $pprev, $pnext);

	    $view_query = "UPDATE ForumTopics SET view = view + 1 WHERE id = $id_topic;";
	    $database->exec($view_query);

	    $view_query =	"SELECT ForumPosts.id AS id_post, ForumUsers.login AS login, ForumUsers.id AS id,".
				" ForumPosts.time AS time, ForumPosts.nick AS nick, ForumPosts.id_user AS id_user,".
				" ForumPosts.subj AS subj, ForumPosts.post AS post, ForumTopics.topic AS topic,".
				" ForumPosts.id_session AS id_session, ForumPosts.attachment AS attachment".
				" FROM ForumPosts, ForumTopics, ForumUsers".
				" WHERE ForumPosts.id_topic = ForumTopics.id AND ForumTopics.id = $id_topic".
				" AND ForumUsers.id = ForumPosts.id_user $ban_opts".
				" ORDER BY ForumPosts.time DESC LIMIT $numentry,$MAX_PAGE_ENTRIES;";

//echo "<!-- view_opt $view_query -->";

	    $msg_count = 0;

	    foreach ($database->query($view_query) as $row) {
		echo '<div class="text_box_1"><a id="post'.$msg_count.'"></a>
<div class="box_user">';
		$timestamp = date('d.m.Y (H:i)', $row['time']);
		$name = format_user_nick($row['nick'], $row['id_user'], $row['login'], $row['id']);

		$tmp_post = $row['post'];
		if (strlen($tmp_post) > 8192) {
		    $tmp_post = substr($tmp_post, 0, 8191);
		}

		$post_id_session = "";
		if ($row['id_session']) {
		    $post_id_session = $row['id_session'];
		}

		$post = linkify(convert_tiktok2(convert_tiktok(convert_vkv(convert_youtube($tmp_post)))), array("http", "https"), array("target" => "_blank"));

		$post = remove_iframes($post);

		$request = $_SERVER['REQUEST_URI'].'&ban='.$post_id_session;
		$banned_session = 0;
		$banned_text =	'<svg viewBox="0 0 20 20" width="16px">'.
				'<title>Скрыть пользователя и его темы</title>'.
				'<path fill="#CCCCCC" d="M12.81 4.36l-1.77 1.78a4 4 0 0 0-4.9 4.9l-2.76 2.75C2.06 12.79.96 11.49.2 10a11 11 0 0 1 12.6-5.64zm3.8 1.85c1.33 1 2.43 2.3 3.2 3.79a11 11 0 0 1-12.62 5.64l1.77-1.78a4 4 0 0 0 4.9-4.9l2.76-2.75zm-.25-3.99l1.42 1.42L3.64 17.78l-1.42-1.42L16.36 2.22z"/>'.
				'</svg>';

		echo $timestamp.' | <span class="name1">'.$name.'</span> -&gt;
<span class="white1">'.$row['subj'].'</span>';

		$id_session = md5(session_id());

		if ($id_session != $post_id_session) {
		    echo '<a class="ban" href="'.$request.'">'.$banned_text.'</a>';
		}

//		if (is_session('myuser_name')) {
		    if ($id_session == $post_id_session && time() - $row['time'] < 60 * 5 ) {
//			echo '<a class="ban" href="'.$_SERVER['REQUEST_URI'].'&editpost='.$row['id_post'].'">'.
			echo '<a class="ban" href="" onclick="post(\''.$_SERVER['REQUEST_URI'].'\',{\'editpost\':'.$row['id_post'].'}); return false;">'.
'<svg viewBox="0 0 20 20" width="16px">'.
'<title>Редактировать сообщение</title>'.
'<path fill="#CCCCCC" d="M2 4v14h14v-6l2-2v10H0V2h10L8 4H2zm10.3-.3l4 4L8 16H4v-4l8.3-8.3zm1.4-1.4L16 0l4 4-2.3 2.3-4-4z"/>'.
'</svg>'.
'</a>';
		    }
//		}

		if (is_forum_admin()) {
		    echo '<a href="?t='.$id_topic.'&dp='.$row['id_post'].'" class="remove">Удалить</a>';
		    echo '<a href="'.$_SERVER['REQUEST_URI'].'&sdel='.$post_id_session.'" class="remove">Удалить сессию</a>';
		}
		echo '</div>
</div>';
		if ($banned_session == 0) {
		echo '<div class="text_box_2">
<div id="message_'.$msg_count.'" class="text_box_2_mess">';

	    $attachment = $row['attachment'];
	    if ($attachment != "") {
		$image_ext = substr(strrchr($attachment, '.'), 1);
		if ($image_ext == 'jpg'  || $image_ext == 'jpeg'  || $image_ext == 'gif' || $image_ext == 'png' ||
		    $image_ext == 'webp') {
		    echo '<a href="'.$UPLOAD_DIR.'/'.$attachment.'" class="highslide" onclick="return hs.expand(this)">';
		    echo '<img src="'.$UPLOAD_DIR."/small-".$attachment.'" alt="" class="postimage"/>';
		    echo '</a>';
		} else {
		    echo '<video class="postvideo" width="420" height="315" controls>';
		    if ($image_ext == 'mp4' || $image_ext == 'mpg4' || $image_ext == 'mpeg4') {
			echo "<source src=\"$UPLOAD_DIR/$attachment\" type=\"video/mp4\">";
		    } else if ($image_ext == 'ogv') {
			echo "<source src=\"$UPLOAD_DIR/$attachment\" type=\"video/ogg\">";
		    } else if ($image_ext == 'webm') {
			echo "<source src=\"$UPLOAD_DIR/$attachment\" type=\"video/webm\">";
		    }
		    echo 'Your browser does not support the video tag.';
		    echo '</video>';
		}
	    } else {
		$post_img = "img".$row['id_post'].".jpg";
		if (file_exists($UPLOAD_DIR."/small-".$post_img)) {
		    echo '<a href="'.$UPLOAD_DIR.'/'.$post_img.'" class="highslide" onclick="return hs.expand(this)">';
		    echo '<img src="'.$UPLOAD_DIR."/small-".$post_img.'" alt="" class="postimage"/>';
		    echo '</a>';
		}
	    }

//$post_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ?
//    'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/'.$_SERVER['REQUEST_URI'].'#post'.$msg_count;

echo $post.'</div>
	<div class="answer_bar">
<!-- <a href="#ftop" class="up">Вверх</a> -->
<a href="#" onclick="reply(\''.$row['nick'].' ('.$timestamp.')\', \'message_'.$msg_count.'\');" class="reply">
<svg viewBox="0 0 20 20" width="16px">
<title>Ответить</title>
<path fill="#CCCCCC" d="M 15,3 V 5.99 A 4,4 0 0 1 11,10 H 8 V 5 l -6,6 6,6 v -5 h 3 A 6,6 0 0 0 17,6 V 3 Z"/>
</svg>
</a>
<a href="#" onclick="reply_cite(\''.$row['nick'].' ('.$timestamp.')\', \'message_'.$msg_count.'\');" class="reply">
<svg viewBox="0 0 20 20" width="16px">
<title>Цитировать</title>
<path fill="#CCCCCC" d="m 12,6 h 3 V 5.99 C 15.0055,8.2030432 13.21305,10.000007 11,10 H 8 V 5 l -6,6 6,6 v -5 h 3 c 3.313708,0 6,-2.6862915 6,-6 v 0 h 3 V 3 H 18 V 4 H 14 V 3 h -2 z"/>
</svg>
</a>
<a href="'.$_SERVER['REQUEST_URI'].'#post'.$msg_count.'" onclick="copyStringToClipboard(\'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'#post'.$msg_count.'\'); return false;" class="reply">
<svg viewBox="0 0 20 20" width="16px">
<title>Ссылка на это сообщение</title>
<path fill="#CCCCCC" d="M11 12h6v-1l-3-1V2l3-1V0H3v1l3 1v8l-3 1v1h6v7l1 1 1-1v-7z"/>
</svg>
</a>
	</div>
</div>';
		}
		$msg_count++;
	    }
	    show_page_control('up', $page, ceil($posts / $MAX_PAGE_ENTRIES), $pprev, $pnext);
	}

	show_menu($database);

	$posts = 0;
	$topics = 0;
	$users = 0;

	$posts  = $database->query("SELECT COUNT(*) FROM ForumPosts;")->fetchColumn();
	$topics = $database->query("SELECT COUNT(*) FROM ForumTopics;")->fetchColumn();
	$users  = $database->query("SELECT COUNT(*) FROM ForumUsers;")->fetchColumn();
	$users = $users - 1; // minus super Anonymous

	echo '
<div class="line1"></div>
<div class="block2">
	<div class="copy">Клонировано <a title="Made in Tomsk" href="https://github.com/antimozga/ftr" target="_blank">AntiMozga</a></div>
	Тем: '.$topics.'
	&nbsp;|&nbsp; Сообщений: '.$posts.'
	&nbsp;|&nbsp; Пользователей: '.$users.'
</div>';

	show_footer();

    }
    unset($database);
}
?>
