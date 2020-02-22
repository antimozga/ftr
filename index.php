<?php

require_once('config.php');
require_once('config_user.php');

setcookie ('PHPSESSID', $_COOKIE['PHPSESSID'], time() + 60 * 30, '/');
session_start();

include('funcs.php');
include('header.php');
include('footer.php');
include('gismeteo.php');

function db_get_val($str, $val)
{
    global $database;
    foreach ($database->query($str) as $row) {
	return $row[$val];
    }
    return "";
}

function start_page($title) {
global $FORUM_RULES_LINK;
show_header($title);
echo 
'<div class="block_menu">
    <div class="menu">
	<div>';

$group_edit = "";
if(!is_session('myuser_name')) {
    echo '  <form action="" method="post"><input type="hidden" name="event" value="login"/>
	    <div class="form_box_text">
		<input type="text" style="width:10%;" name="user[name]" onfocus="if(this.value == \'Имя\') { this.value = \'\'; }" value="Имя"/>
		<input type="password" style="width:10%;" name="user[password]" onfocus="if(this.value == \'Пароль\') { this.value = \'\'; }" value="Пароль"/>
		<input class="btn_group_sel" type="submit" value="&nbsp;" />
	    </div>
	    </form>
	</div>
	<div class="sep"><div></div></div>
	<div><a href="?reg=1">Регистрация</a></div>';
} else {
    $pt = db_get_val('SELECT COUNT(*) AS total FROM ForumPager WHERE id_user = '.$_SESSION['myuser_id'].';', 'total');
    $pn = db_get_val('SELECT COUNT(*) AS total FROM ForumPager WHERE id_user = '.$_SESSION['myuser_id'].' AND new = 1;', 'total');
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
	<div><a href="?users">Пользователи</a></div>
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

    show_gismeteo();

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
    global $RECAPTCHA_SITE_KEY;
    $error = "";
    $name = "";
    $subj = "";
    $post = "";

    if (isset($_SESSION['user_temp_name'])) {
	$name = $_SESSION['user_temp_name'];
    }

    if (isset($_SESSION['user_temp_subj'])) {
	$subj = $_SESSION['user_temp_subj'];
    }

    if (isset($_SESSION['user_temp_post'])) {
	$post = $_SESSION['user_temp_post'];
	$error = "<div class=\"error1\">* Робот обнаружен? Попробуйте изменить текст или отправить его чуть позже...</div>";
    }

    if ($type == 'topic') {
	$h = 'Заголовок темы';
	$b = 'Добавить тему';
    } else {
	$h = 'Заголовок сообщения';
	$b = 'Добавить сообщение';
    }
echo '
<script src="https://www.google.com/recaptcha/api.js?render='.$RECAPTCHA_SITE_KEY.'"></script>
<script>
function formSubmit () {
    grecaptcha.ready(function () {
	grecaptcha.execute(\''.$RECAPTCHA_SITE_KEY.'\', { action: \'post\' }).then(function (token) {
	    var recaptchaResponse = document.getElementById(\'recaptchaResponse\');
	    recaptchaResponse.value = token;
	    document.getElementById(\'formMessage\').submit();
	});
    });

    return false;
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
			<textarea class="area_text" id="mess_text" name="message[content]" onFocus="javascript: textFocus = true;" onBlur="javascript: textFocus = false;">'.$post.'</textarea>
		</div>
		<div class="form_box_btn">
			<input class="btn_form" value="'.$b.'" type="submit">
		</div>
		<div class="format">
			<div id="web"></div>
			<span id="mess_emo"><a href="" onclick="doInsert(\'[b]\',\'[/b]\', true); return false;" class="for1" id="bold">Bold</a>&nbsp;-&nbsp;<a href="" onclick="doInsert(\'[i]\',\'[/i]\', false); return false;" class="for2">Italic</a>&nbsp;-&nbsp;<a href="" onclick="doInsert(\'[re]\',\'[/re]\', false); return false;" class="for3">Cite</a>&nbsp;-&nbsp;</span>
		</div>
		<input type="hidden" name="MAX_FILE_SIZE" value="500000">
		    <table class="form_box_image_btn">
		    <tr><td>
		    <input name="image" type="file">
		    </td></tr>
		    <tr><td>
		    <label for="image">Картинка JPG,PNG,GIF(макс. размер 500КБ)</label>
		    </td></tr>
		    </table>
	</div>
	<input type="hidden" name="recaptcha_response" id="recaptchaResponse">
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
	     "(id INTEGER PRIMARY KEY, time DATE, id_grp INTEGER, id_topic INTEGER, id_user INTEGER, nick NVARCHAR, subj NVARCHAR, post NVARCHAR);";
    $database->exec($query);

    $query = "CREATE TABLE IF NOT EXISTS ForumTopics " .
	     "(id INTEGER PRIMARY KEY, id_grp INTEGER, id_user INTEGER, nick VARCHAR, topic VARCHAR, view INTEGER DEFAULT 0);";
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

    {
	$show_groups = 0;
	$id_grp = 0;
	$id_topic = 0;
	$id_user = 0;
	$show_hot = 0;
	$page = 1;
	$topic = "ТОП ".$TOP_LIST." ГОРЯЧИХ ТЕМ";
	$search_opt = "";
	$reg_mode = 0;
	$show_search = 0;
	$show_users = 0;
	$show_pager = 0;
	$show_trash_topics = $SHOW_TRASH_TOPICS;
	$show_mylist = 0;

	if (isdefined("logout")) {
	    unset($_SESSION['myuser_name']);
	    unset($_SESSION['myuser_id']);
	    unset($_SESSION['user_temp_name']);
	}

	if (isset($_SESSION['myuser_id'])) {
	    $id_user = $_SESSION['myuser_id'];
	}

	unset($_SESSION['user_temp_subj']);
	unset($_SESSION['user_temp_post']);

	if (isdefined("event")) {
	    $cmd = $_REQUEST["event"];
	    if ($cmd == "login") {
		$myuser_name		= convert_string($_REQUEST["user"]["name"]);
		$myuser_password	= convert_string($_REQUEST["user"]["password"]);
		$myuser_id		= "";
		//echo "Login>>>".$myuser_name." ".$myuser_password;
		$name = "";
		$password = "";
		$user_query = "SELECT id, login, password FROM ForumUsers WHERE login = '$myuser_name';";
		foreach ($database->query($user_query) as $row) {
		    $name = $row['login'];
		    $password = $row['password'];
		    $myuser_id = $row['id'];
		}
		if (($myuser_name == $name) && ($myuser_password == $password) && ($myuser_id != 0)) {
		    //echo "Logged";
		    $_SESSION['myuser_name'] = $myuser_name;
		    $_SESSION['myuser_id'] = $myuser_id;

		    $tim = time();
		    $query = "UPDATE ForumUsers SET last_login = $tim WHERE id = $myuser_id;";
		    $database->exec($query);

		    unset($_SESSION['user_temp_name']);
		    header("location:index.php");
		} else {
		    //echo "fail";
		}
	    }
	}

	if (is_forum_admin()) {
	    if (isdefined("dp")) {
		$dp = $_REQUEST["dp"];
		$dp = ($dp * 10) / 10;
		$dt = db_get_val("SELECT id_topic FROM ForumPosts WHERE id = ".$dp, "id_topic");
		$query = "DELETE FROM ForumPosts WHERE id = ".$dp.";";
		$database->exec($query);
		/* remove topic if last post was removed */
		$query = "DELETE FROM ForumTopics WHERE id = ".$dt." AND (SELECT COUNT(*) FROM ForumPosts WHERE id_topic = ".$dt.") = 0;";
		$database->exec($query);
	    }
	    if (isdefined("dt")) {
		$dt = $_REQUEST["dt"];
		$dt = ($dt * 10) / 10;
		if (isdefined("trash")) {
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

	if (isdefined("p")) {
	    $page = $_REQUEST["p"];
	    if ($page < 1 ) {
		$page = 1;
	    }
	}

	if (isdefined("g")) {
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
	} else if (isdefined("t")) {
	    $id_topic = $_REQUEST["t"];
	    $id_topic = ($id_topic * 10) / 10;
	
	    if ($id_user != 0 && isdefined("like")) {
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
		if (db_get_val("SELECT id_user FROM ForumUserLike WHERE id_user = ".$id_user." AND id_like = ".$id_topic." AND type = 0;", 'id_user') == $id_user) {
		    $ctrlink = '<a style="float: right" href="?t='.$id_topic.'&like=0">-</a>';
		} else {
		    $ctrlink = '<a style="float: right" href="?t='.$id_topic.'&like=1">+</a>';
		}
	    }

	    start_page($topic);
	    show_banner();
	    show_menu($database);
	    show_nav_path("<a href=\"?g=".$id_group."\">".$group."</a> &nbsp;/&nbsp; <a href=\"?t=".$id_topic."\">".$topic."</a>", $ctrlink);
	} else if (isdefined("reg")) {
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
	} else if (isdefined("users")) {
	    $show_users = 1;
	    $show_users_string = convert_string($_REQUEST["users"]);

	    $topic = "ПОЛЬЗОВАТЕЛИ";

	    start_page($topic);
	    show_banner();
	    show_menu($database);
	    show_nav_path("Список пользователей");
	} else if (isdefined("pager")) {
	    $show_pager = 1;

	    $topic = "ПЕЙДЖЕР";

	    start_page($topic);
	    show_banner();
	    show_menu($database);
	    show_nav_path("Пейджер");
	} else if (isdefined("search")) {
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
	} else {
	    if (isdefined("s")) {
		$show_hot = 1;
		$topic = "ТОП ".$TOP_LIST." ОБЩЕНИЯ";
	    }
	    if (isdefined("m")) {
		$show_mylist = 1;
		$topic = "МОИ ИЗБРАННЫЕ ТЕМЫ";
	    }

	    start_page($topic);
	    show_banner();
	    show_menu($database);
	    show_nav_path($topic);
	}

	if (isdefined("event")) {
	    $cmd = $_REQUEST["event"];
	    if ($cmd == "forumcreatesubj") {
		$nick = convert_string($_REQUEST["message"]["author"]);
		$subj = convert_string($_REQUEST["message"]["caption"]);
		$post = convert_text($_REQUEST["message"]["content"]);

		if (isset($_POST['recaptcha_response'])) {
		    // Build POST request:
		    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
		    //    $recaptcha_secret = 'YOUR_RECAPTCHA_SECRET_KEY';
		    $recaptcha_response = $_POST['recaptcha_response'];

		    // Make and decode POST request:
		    $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $RECAPTCHA_SERV_KEY . '&response=' . $recaptcha_response);
		    $recaptcha = json_decode($recaptcha);

		    // Take action based on the score returned:
		    if ($recaptcha->score >= 0.5) {
			if ($nick == "") {
			    unset($_SESSION['user_temp_name']);
			} else {
			    $_SESSION['user_temp_name'] = $nick;
			}

			$tim = time();

			if ($nick == "" && $id_user != 0) {
			    $user_query = "SELECT login FROM ForumUsers WHERE id = '$id_user';";
			    foreach ($database->query($user_query) as $row) {
				$nick = $row['login'];
			    }
			}

			if ($nick == "") {
			    $nick = "Анонимно";
			}
			if ($id_topic != 0) {
			    if ($post != "") {
				$query = "INSERT INTO ForumPosts (id, time, id_grp, id_topic, id_user, nick, subj, post)" .
					 "VALUES (NULL, '$tim', (SELECT id_grp FROM ForumTopics WHERE id = '$id_topic'), $id_topic, $id_user, '$nick', '$subj', '$post');";
				$database->exec($query);
			    }
			} else {
			    if ($post != "" && $subj != "") {
				$query = "REPLACE INTO ForumTopics (id, id_grp, id_user, nick, topic, view)" .
					 "VALUES ((SELECT id FROM ForumTopics WHERE topic = '$subj'), $id_grp, coalesce((SELECT id_user FROM ForumTopics WHERE topic = '$subj'), $id_user), coalesce((SELECT nick FROM ForumTopics WHERE topic = '$subj'),'$nick'), '$subj', coalesce((SELECT view FROM ForumTopics WHERE topic = '$subj'), 0));";
				$database->exec($query);
				$query = "INSERT INTO ForumPosts (id, time, id_grp, id_topic, id_user, nick, subj, post)" .
					 "VALUES (NULL, '$tim', $id_grp, (SELECT id FROM ForumTopics WHERE topic = '$subj'), $id_user, '$nick', '$subj', '$post');";
				$database->exec($query);
			    }
			}

			$post_id = db_get_val("SELECT id FROM ForumPosts WHERE time = '".$tim."' AND post = '".$post."';", 'id');

			$image = $_FILES['image']['name'];
			$image_tmp = $_FILES['image']['tmp_name'];
			$image_ext = strtolower(substr(strrchr($image, '.'), 1));

			if ($image_ext == "jpg" || $image_ext == "jpeg" || $image_ext == "gif" || $image_ext == "png") {
			    $img_file = "img".$post_id.".jpg";
			    list($img_width, $img_height, $img_type, $img_attr)= getimagesize($image_tmp);
			    if (is_uploaded_file($image_tmp)) {
				if (($img_width > 800) || ($img_height > 600)) {
				    system("convert -resize 800x600 -quality 85 ".$image_tmp." ".$UPLOAD_DIR."/".$img_file);
				} else {
				    system("convert -quality 85 ".$image_tmp." ".$UPLOAD_DIR."/".$img_file);
				}
				system("convert -resize 80x60 -quality 85 ".$image_tmp." ".$UPLOAD_DIR."/small-".$img_file);
			    }
			}

			if ($image_tmp != "") {
			    unlink($image_tmp);
			}

			unset($_SESSION['user_temp_subj']);
			unset($_SESSION['user_temp_post']);
		    } else {
			// Not verified - show form error
			$_SESSION['user_temp_name'] = $nick;
			$_SESSION['user_temp_subj'] = $subj;
			$_SESSION['user_temp_post'] = $_REQUEST["message"]["content"];
			//echo "YOU ARE FUCKIN' ROBOT! " . $recaptcha->score;
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
			    system("pnmscale -xy 70 70 ".$tmpimg." | cjpeg -qual 75 >".$image_tmp);
			    copy($image_tmp, $UPLOAD_DIR."/small-".$img_file);
			    unlink($tmpimg);
			}
		    }
		}
		if ($image_tmp != "") {
		    unlink($image_tmp);
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
		$pn = db_get_val('SELECT COUNT(*) AS total FROM ForumPager WHERE id_user = '.$id_user.' AND id_from_user = '.$row['id_from_user'].' AND new = 1;', 'total');
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
    <td colspan="4">
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
</tr>';
	    if (mb_strlen($show_users_string, 'utf-8') > 1) {
		$view_query = "SELECT id, login, last_login, gender FROM ForumUsers WHERE login LIKE '".$show_users_string."%';";
	    } else {
		$view_query = "SELECT id, login, last_login, gender FROM ForumUsers WHERE login LIKE '".$show_users_string."%' OR login LIKE '".lower_ru($show_users_string)."%';";
	    }
	    $g = array(1 => 'Не имеет значения', 2 => 'Мужской', 3 => 'Женский', 4 => 'Средний');
	    foreach ($database->query($view_query) as $row) {
		if ($row['id'] == 0) {
		    continue;
		}
		echo '<tr>';
		echo '<th>'.format_user_nick($row['login'], $row['id'], $row['login'], $row['id']).'</th>';
		echo '<th>'.$g[$row['gender']].'</th>';
		$avatar = $UPLOAD_DIR."/small-id".$row['id'].".jpg";
		if (file_exists($avatar)) {
		    echo '<th>'.'ЕСТЬ'.'</th>';
		} else {
		    echo '<th>'.'НЕТ'.'</th>';
		}
		echo '<th>'.date('d.m.Y (H:i)', $row['last_login']).'</th>';
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
	<input type="text" class="inp_text_reg" name="user[user_name]" id="login" maxlength="20" value="'.$user_name.'">
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
	<input type="password" class="inp_text_reg" name="user[user_password]" id="password1" maxlength="100" value="'.$user_password.'">
	<label for="password2">* Подтверждение пароля </label>
	<input type="password" class="inp_text_reg" name="user[user_password_confirm]" id="password2" maxlength="100" value="'.$user_password.'">
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
	<textarea name="user[description]" id="addition" class="area_text_reg">'.$user_description.'</textarea>
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
	} else if ($id_topic == 0) {
	    $posts = 0;
	    $pnext = "";
	    $pprev = "";
	    $base_query = "SELECT ForumTopics.nick AS nick, ForumTopics.id_user AS id_user, ForumTopics.view AS view, ForumUsers.login AS login, ForumUsers.id AS id, COUNT(*) AS posts, ForumPosts.time AS time, ForumTopics.topic AS topic, ForumPosts.id_topic AS id_topic, ForumPosts.nick AS last_nick, ForumPosts.id_user AS last_id_user, ForumGroups.grp AS grp FROM ForumPosts, ForumTopics, ForumUsers, ForumGroups WHERE ForumPosts.id_topic = ForumTopics.id AND ForumGroups.id = ForumTopics.id_grp ";
	    if (!$show_trash_topics && $id_grp == 0) {
	        $base_query = $base_query."AND ForumTopics.id_grp != ".$FORUM_TRASH_GID." ";
	    }
	    if ($id_grp != 0) {
		$view_query = "SELECT COUNT(topic) as posts FROM ForumTopics WHERE id_grp = $id_grp;";
		foreach ($database->query($view_query) as $row) {
		    $posts = $row['posts'];
		}
		$numentry = ($page - 1) * $MAX_PAGE_ENTRIES;
		if ($numentry + $MAX_PAGE_ENTRIES < $posts) {
		    $pnext = "?g=".$id_grp."&p=".($page + 1);
		}
		if ($page > 1) {
		    $pprev = "?g=".$id_grp."&p=".($page - 1);
		}
		show_page_control('down', $page, ceil($posts / $MAX_PAGE_ENTRIES), $pprev, $pnext);

		$view_query = $base_query." AND ForumPosts.id_grp = $id_grp AND ForumUsers.id = ForumTopics.id_user ".$search_opt." GROUP BY id_topic ORDER BY ForumPosts.time DESC LIMIT $numentry,$MAX_PAGE_ENTRIES;";
	    } else {
		$view_query = "SELECT COUNT(topic) as posts FROM ForumTopics;";
		foreach ($database->query($view_query) as $row) {
		    $posts = $row['posts'];
		}
		$numentry = ($page - 1) * $TOP_LIST;
		if ($numentry + $TOP_LIST < $posts) {
		    $pnext = "?p=".($page + 1);
		}
		if ($page > 1) {
		    $pprev = "?p=".($page - 1);
		}
		show_page_control('down', $page, ceil($posts / $TOP_LIST), $pprev, $pnext);

		if ($show_hot == 0) {
		    $view_query = $base_query." AND ForumUsers.id = ForumTopics.id_user ".$search_opt." GROUP BY id_topic ORDER BY ForumPosts.time DESC LIMIT $numentry,$TOP_LIST;";
		} else {
		    $view_query = $base_query." AND ForumUsers.id = ForumTopics.id_user ".$search_opt." GROUP BY id_topic ORDER BY posts DESC LIMIT $numentry,$TOP_LIST;";
		}
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
		}
	    }

//	    $count_query = "SELECT COUNT(ForumTopics.topic) as posts ".substr($view_query, strpos($view_query, "FROM"));
//echo '- [ '.$count_query.' ] -';

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
//	    if ($id_grp != 0) {
		show_page_control('up', $page, ceil($posts / $MAX_PAGE_ENTRIES), $pprev, $pnext);
//	    }
	} else {
	    $posts = 0;
	    $pnext = "";
	    $pprev = "";
	    $view_query = "SELECT COUNT(post) as posts FROM ForumPosts WHERE id_topic = $id_topic;";
	    foreach ($database->query($view_query) as $row) {
		$posts = $row['posts'];
	    }
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

	    $view_query = "SELECT ForumPosts.id AS id_post, ForumUsers.login AS login, ForumUsers.id AS id, ForumPosts.time AS time, ForumPosts.nick AS nick, ForumPosts.id_user AS id_user, ForumPosts.subj AS subj, ForumPosts.post AS post, ForumTopics.topic AS topic FROM ForumPosts, ForumTopics, ForumUsers WHERE ForumPosts.id_topic = ForumTopics.id AND ForumTopics.id = $id_topic AND ForumUsers.id = ForumPosts.id_user ORDER BY ForumPosts.time DESC LIMIT $numentry,$MAX_PAGE_ENTRIES;";

	    $msg_count = 0;

	    foreach ($database->query($view_query) as $row) {
		echo '<div class="text_box_1">
<div class="box_user">';
		$timestamp = date('d.m.Y (H:i)', $row['time']);
		$name = format_user_nick($row['nick'], $row['id_user'], $row['login'], $row['id']);

		$post = linkify(convert_youtube($row['post']), array("http", "https"), array("target" => "_blank"));

		echo $timestamp.' | <span class="name1">'.$name.'</span> -&gt;
<span class="white1">'.$row['subj'].'</span>';
		if (is_forum_admin()) {
		    echo '<a href="?t='.$id_topic.'&dp='.$row['id_post'].'" class="remove">Удалить</a>';
		}
		echo '</div>
</div>
<div class="text_box_2">
<div id="message_'.$msg_count.'" class="text_box_2_mess">';
	    $post_img = "img".$row['id_post'].".jpg";
	    if (file_exists($UPLOAD_DIR."/small-".$post_img)) {
		echo '<a href="'.$UPLOAD_DIR.'/'.$post_img.'" class="highslide" onclick="return hs.expand(this,{wrapperClassName: \'borderless floating-caption\', dimmingOpacity: 0.75, align: \'center\'})">';
		echo '<img src="'.$UPLOAD_DIR."/small-".$post_img.'" alt="" class="postimage"/>';
		echo '</a>';
	    }
echo $post.'</div>
<!-- <a href="#ftop" class="up">Вверх</a> -->
<a href="#" onclick="reply(\''.$row['nick'].' ('.$timestamp.')\', \'message_'.$msg_count.'\');" class="answer">Ответить</a>
</div>';
		$msg_count++;
	    }
	    show_page_control('up', $page, ceil($posts / $MAX_PAGE_ENTRIES), $pprev, $pnext);
	}


	show_menu($database);

	$posts = 0;
	$topics = 0;
	$users = 0;

	$view_query = "SELECT COUNT(post) as posts FROM ForumPosts;";
	foreach ($database->query($view_query) as $row) {
	    $posts = $row['posts'];
	}
	$view_query = "SELECT COUNT(topic) as topics FROM ForumTopics;";
	foreach ($database->query($view_query) as $row) {
	    $topics = $row['topics'];
	}
	$view_query = "SELECT COUNT(login) as users FROM ForumUsers;";
	foreach ($database->query($view_query) as $row) {
	    $users = $row['users'];
	}
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
