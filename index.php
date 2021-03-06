<?php

function microtime_float()
{
    list ($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
}

session_start();

setcookie('PHPSESSID', session_id(), time() + 60 * 60 * 24 * 7, '/');

if (isset($_SESSION['ajsner'])) {
    if ($_SESSION['ajsner'] > 100) {
        header($_SERVER["SERVER_PROTOCOL"] . " 503 Service Temporarily Unavailable", true, 503);
        $retryAfterSeconds = 240;
        header('Retry-After: ' . $retryAfterSeconds);
?>
<h1>503 Service Temporarily Unavailable</h1>
<?php
        exit();
    }
}

$time_now = microtime_float();

$time_diff = 0;

if (isset($_SESSION['lkasdas'])) {
    $time_diff = $time_now - $_SESSION['lkasdas'];
}

// error_log("access to ".$_SERVER['HTTP_HOST']." ".$_SERVER['REQUEST_URI']." ".$_COOKIE['PHPSESSID']." ".$_SESSION['lkasdas']." ".$time_diff." ".$_SESSION['ajsner']);
// error_log("time diff ".$time_diff);

$_SESSION['lkasdas'] = $time_now;

if ($time_diff < 1) {
    if (! isset($_SESSION['ajsner'])) {
        $_SESSION['ajsner'] = 1;
    } else {
        $_SESSION['ajsner'] ++;
    }

    if ($_SESSION['ajsner'] > 1) {
        header($_SERVER["SERVER_PROTOCOL"] . " 503 Service Temporarily Unavailable", true, 503);
        $retryAfterSeconds = 240;
        header('Retry-After: ' . $retryAfterSeconds);
?>
<h1>503 Service Temporarily Unavailable</h1>
<?php
        exit();
    }
} else {
    $_SESSION['ajsner'] = 0;
}

$debug = false;

require_once ('config.php');
require_once ('config_user.php');

include ('funcs.php');
include ('automoderator.php');
include ('header.php');
include ('footer.php');

function start_page($title)
{
    global $FORUM_RULES_LINK;
    global $database;

    show_header($title);
    ?>
<div class="block_menu">
	<div class="menu" id="mobMenu">
    
<?php
    if (! is_logged()) {
        ?>        
		<a href="" onclick="load_modal('login.php'); return false;">Вход</a>
		<div class="sep">
			<div></div>
		</div>
		<a href="?reg=1">Регистрация</a>
<?php
    } else {
        ?>	
		<a href="?reg=3" class="session_ctl"><?php echo $_SESSION['myuser_name']; ?></a>
		<div class="sep">
			<div></div>
		</div>
		<a href="#" id="pagerlink"
			onclick="load_modal('showpager.php'); return false;">Пейджер (<span
			class="autorefresh refreshnow" src="pagerstatus.php"></span>)
		</a>
<?php
    }
    ?>
    	<div class="sep">
			<div></div>
		</div>
		<a href="?users">Пользователи</a><a href="#"
			onclick="load_modal('showbanlist.php'); return false;">(Скрытые)</a>
		<div class="sep">
			<div></div>
		</div>
		<a href="<?php echo $FORUM_RULES_LINK; ?>">Правила</a>
<?php
    if (is_logged()) {
        $logout_uri = $_SERVER['REQUEST_URI'];
        if ($logout_uri === '/') {
            $logout_uri = '?logout';
        } else {
            $logout_uri = $logout_uri . '&logout';
        }
        ?>
		<div class="sep">
			<div></div>
		</div>
		<a href="<?php echo $logout_uri; ?>" class="session_ctl">Выход</a>
<?php
    }

    if (is_forum_admin()) {
        ?>        
		<div class="sep">
			<div></div>
		</div>
		<a href="groups.php">Редактор групп тем</a>
<?php
    }
    ?>
    <a href="javascript:void(0);" class="mobicon"
			onclick="mobileMenu('mobMenu','menu')">&#9776;</a>
	</div>
</div>
<?php
}

function show_menu($database)
{
    global $FORUM_PURGATORIUM_GID;
    ?>
<div class="block_menu_m">
	<form class="group_sel" action="">
		<select onchange="document.location='?g='+this.value;" name="g">
			<option value="0" selected="selected">Группы тем:</option>
			<option value="0">-----------------------</option>
<?php
    $group_query = "SELECT * FROM ForumGroups ORDER BY grp ASC;";
    foreach ($database->query($group_query) as $row) {
        if (isset($FORUM_PURGATORIUM_GID) && is_hardcore_on()) {
            if ($row['id'] == $FORUM_PURGATORIUM_GID) {
                continue;
            }
        }
        ?>
			<option value="<?php echo $row['id']; ?>"><?php echo $row['grp']; ?></option>
<?php
    }
    ?>
	</select>
	</form>
	<div class="menu">
		<div>
			<a href="./?g=0">Группы<span class="view-desk"> тем</span></a>
		</div>
		<div class="sep">
			<div></div>
		</div>
		<div>
			<a href="./">Горячее</a>
		</div>
		<div class="sep">
			<div></div>
		</div>
		<div>
			<a href="./?s=1">Топ<span class="view-desk"> общения</span></a>
		</div>
<?php
    if (is_logged()) {
        ?>       
		<div class="sep">
			<div></div>
		</div>
		<div>
			<a href="./?m=1">Избранное</a>
		</div>
		<div class="sep">
			<div></div>
		</div>
		<div>
			<a href="./?o=1">Моё</a>
		</div>
<?php
    }
    ?>    
		<div>
			<a style="float: right;" href="#"
				onclick="load_modal('searchtopic.php'); return false;">
				<svg viewBox="0 0 20 20" width="16px">
					<title>Поиск по темам</title>
					<path
						d="M12.9 14.32a8 8 0 1 1 1.41-1.41l5.35 5.33-1.42 1.42-5.33-5.34zM8 14A6 6 0 1 0 8 2a6 6 0 0 0 0 12z" />
				</svg>
			</a>
		</div>
	</div>
</div>
<?php
}

function show_banner()
{
?>
<div class="block1">
	<div class="logo">
		<a href="./"><img src="images/ftrclogo.png"></a>
	</div>
<!--
	<div class="logo2">
		<img height="228px" src="images/novigod.png">
	</div>
-->
	<div class="autorefresh refreshnow" src="weather-gismeteo-informer.php"></div>
	<div class="autorefresh refreshnow" src="exchangerate-ecb.php"></div>
</div>
<?php
}

function show_nav_path($topic, $ctrlink = "")
{
    global $FORUM_NAME;
    ?>
<div class="navigation">
	<div class="box_path">
		<table>
			<tr>
				<td class="tdw1">:: <a href="./"><?php echo $FORUM_NAME; ?></a> &nbsp;/&nbsp; <?php echo $topic; ?></td>
<?php
    if ($ctrlink != "") {
        ?>
				<td class="tdw2"><?php echo $ctrlink; ?></td>
<?php
    }
    ?>    
    		</tr>
		</table>
	</div>
</div>
<?php
}

function show_postbox($type, $id_session)
{
    global $database;
    global $debug;
    global $RECAPTCHA_SITE_KEY;

    $name = "";
    $subj = "";
    $post = "";
    $edit_info = "";

    if (is_defined('editpost')) {
        $edit_post_id = $_REQUEST['editpost'];
        $edit_post_id = ($edit_post_id * 10) / 10;

        $sth = $database->prepare("SELECT id, time, nick, subj, post, modtime" . " FROM ForumPosts" . " WHERE id_session=\"$id_session\" AND id=$edit_post_id");
        $sth->execute();
        $row = $sth->fetch();
        if ($row['id'] != "") {
            $name = $row['nick'];
            $subj = $row['subj'];
            $post = reconvert_text($row['post']);
            $edit_info = '<input type="hidden" name="edit_post_info" value="' . $edit_post_id . '">';
        }
    } else {
        if (isset($_SESSION['user_temp_name'])) {
            $name = $_SESSION['user_temp_name'];
        }
    }

    if ($type == 'topic') {
        $h = 'Заголовок темы';
        $b = 'Создать';
    } else {
        $h = 'Заголовок сообщения';

        if (is_defined('editpost')) {
            $b = 'Исправить';
        } else {
            $b = 'Отправить';
        }
    }
    ?>
<script	src="https://www.google.com/recaptcha/api.js?render=<?php echo $RECAPTCHA_SITE_KEY; ?>"></script>
<script>
function formSubmit () {
    document.getElementById('mess_submit').hidden = true;
    document.getElementById('mess_submit_process').hidden = false;
<?php
    if ($debug) {
        ?>
    	formSubmit2('', 'formMessage');
<?php
    } else {
        ?>
    	grecaptcha.ready(function () {
			grecaptcha.execute('<?php echo $RECAPTCHA_SITE_KEY; ?>', { action: 'post' }).then(function (token) {
	    		var recaptchaResponse = document.getElementById('recaptchaResponse');
	    		recaptchaResponse.value = token;
	    		formSubmit2('', 'formMessage');
			});
    	});
<?php
    }
    ?>
    return false;
}
</script>
<div class="line1"></div>
<div>
	<form action="" method="post" class="form_mess" name="formMessage"
		id="formMessage" enctype="multipart/form-data"
		onsubmit="return formSubmit()">
		<input type="hidden" name="event" value="forumcreatesubj">
		<div class="form_box">
			<div class="form_box_mess_info">
				<div class="form_box_name">
					<label for="name" class="l_inp_text_name">Ваше имя:</label>
					<input class="inp_text_name" id="name" maxlength="25"
						name="message[author]" value="<?php echo $name; ?>" type="text">
				</div>
				<div class="form_box_title">
					<label for="heading" class="l_inp_text_name"><?php echo $h; ?>:</label>
					<input class="inp_text_name" id="heading" maxlength="100"
						name="message[caption]" value="<?php echo $subj; ?>" type="text">
				</div>
			</div>
			<div class="form_box_mess">
				<textarea maxlength="16384" class="area_text" id="mess_text"
					name="message[content]" onFocus="javascript: textFocus = true;"
					onBlur="javascript: textFocus = false;"><?php echo $post; ?></textarea>
			</div>
			<div>
				<div class="form_box_btn">
					<input class="btn_form" value="<?php echo $b; ?>" type="submit" id="mess_submit">
					<span id="mess_submit_process" hidden>
						<svg width="19px" height="19px" viewBox="0 0 50 50">
							<path fill="#33CCFF" d="M25,5A20.14,20.14,0,0,1,45,22.88a2.51,2.51,0,0,0,2.49,2.26h0A2.52,2.52,0,0,0,50,22.33a25.14,25.14,0,0,0-50,0,2.52,2.52,0,0,0,2.5,2.81h0A2.51,2.51,0,0,0,5,22.88,20.14,20.14,0,0,1,25,5Z">
								<animateTransform attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.5s" repeatCount="indefinite" />
							</path>
						</svg>
					</span>
				</div>
				<div class="format">
					<div id="web"></div>
					<span id="mess_emo"> &nbsp;-&nbsp; <a href="" onclick="return doInsert('[re]','[/re]', false)" class="for3">
					<span class="view-desk">Цитата</span><span class="view-mob">Ц</span></a>
					&nbsp;-&nbsp;
					<a href="" onclick="return doInsert('[b]', '[/b]',  true)" class="for1" id="bold">
					<span class="view-desk">Жирный</span><span class="view-mob">Ж</span></a>
					&nbsp;-&nbsp;
					<a href="" onclick="return doInsert('[i]', '[/i]',  false)" class="for2">
					<span class="view-desk">Курсив</span><span class="view-mob">К</span></a></span>
<!--
					&nbsp;-&nbsp;
					<a href="" onclick="return doInsert('[s]', '[/s]',  false)" class="for4">
					<span class="view-desk">Зачерк</span><span class="view-mob">З</span></a></span>
					&nbsp;-&nbsp;
					<a href="" onclick="return doInsert('[u]', '[/u]',  false)" class="for5">
					<span class="view-desk">Подчерк</span><span class="view-mob">П</span></a></span>
-->
				</div>
				<input type="hidden" name="MAX_FILE_SIZE" value="6291456">
				<div class="form_box_upload">
					<div>
						<svg viewBox="0 0 20 20" width="16px" class="svg_button" onclick="reControl('recontrol')">
							<title>Меню записи аудио</title>
							<path d="M9 18v-1.06A8 8 0 0 1 2 9h2a6 6 0 1 0 12 0h2a8 8 0 0 1-7 7.94V18h3v2H6v-2h3zM6 4a4 4 0 1 1 8 0v5a4 4 0 1 1-8 0V4z" />
						</svg>
						&nbsp;
						<svg viewBox="0 0 20 20" width="16px" class="svg_button" onclick="document.getElementById('attachFile').click()">
							<title>Прикрепить картинку, аудио или видео</title>
							<path d="M15 3H7a7 7 0 1 0 0 14h8v-2H7A5 5 0 0 1 7 5h8a3 3 0 0 1 0 6H7a1 1 0 0 1 0-2h8V7H7a3 3 0 1 0 0 6h8a5 5 0 0 0 0-10z" />
						</svg>
						<span id="attachedFile"></span>
						<input name="image" type="file" style="display: none;" id="attachFile">
						<script>
document.getElementById('attachFile').onchange = function () {
    document.getElementById('attachedFile').innerHTML = this.value.replace(/^.*[\\\/]/, '');
/*  alert(\'Selected file: \' + this.value.replace(/^.*[\\\/]/, \'\')); */
};
						</script>
					</div>
					<div>
						<label class="upload_file" for="image">JPG,PNG,GIF,WEBP/OGA,MP4A/MP4,OGV,WEBM (макс. размер 6МБ)</label>
					</div>
					<div id="recontrol" hidden>
						<button id="action_recstart" onclick="myRecorderStart(updateRecord, updateUpload); return false;">
							<svg viewBox="0 0 20 20" width="16px" class="svg_icon_black">
								<title>Начать запись аудио</title>
								<path d="M9 18v-1.06A8 8 0 0 1 2 9h2a6 6 0 1 0 12 0h2a8 8 0 0 1-7 7.94V18h3v2H6v-2h3zM6 4a4 4 0 1 1 8 0v5a4 4 0 1 1-8 0V4z" />
							</svg>
						</button>
						<button id="action_recstop" onclick="myRecorderStop(); return false;" hidden>
							<svg viewBox="0 0 20 20" width="16px" class="svg_icon_red">
								<title>Остановить запись аудио</title>
									<path d="M9 18v-1.06A8 8 0 0 1 2 9h2a6 6 0 1 0 12 0h2a8 8 0 0 1-7 7.94V18h3v2H6v-2h3zM6 4a4 4 0 1 1 8 0v5a4 4 0 1 1-8 0V4z" />
							</svg>
						</button>
						<button id="action_recplay" onclick="myRecorderPlay(); return false;" hidden>
							<span id="action_playstart">
								<svg viewBox="0 0 20 20" width="16px" class="svg_icon_black">
									<title>Воспроизвести запись аудио</title>
									<path d="M4 4l12 6-12 6z" />
								</svg>
							</span>
							<span id="action_playstop" hidden>
								<svg viewBox="0 0 20 20" width="16px" class="svg_icon_red">
									<title>Воспроизвести запись аудио</title>
									<path d="M4 4l12 6-12 6z" />
								</svg>
							</span>
						</button>
					</div>
					<script>
function updateRecord(maxt, curt) {
//    console.log(\'time \' + maxt + \' \' + curt);
    let secs = maxt / 1000 - curt;
    document.getElementById('attachedFile').innerHTML = secs + " с.";
}

function updateUpload(url) {
//    console.log(\'url \' + url);
    document.getElementById('attachedFile').innerHTML = url.replace(/^.*[\\\/]/, '');
}
					</script>
				</div>
			</div>
		</div>
		<input type="hidden" name="recaptcha_response" id="recaptchaResponse">
<?php echo $edit_info; ?>
	</form>
	<span class="error1" id="mess_post_error"></span>
</div>
<script type="text/javascript">
<!--var 
fombj = document.getElementById("formMessage");
//-->

    (() => {
      new EmojiPicker(document.getElementById('mess_text'), document.getElementById('mess_emo'))
    })()
</script>
<div class="line1"></div>
<?php
}

function show_page_control($type, $page, $pages, $pageprev, $pagenext, $id_topic = 0, $id_grp = 0)
{
    ?>
<div class="paging">
	<form class="paging_sel" action="" method="get">
<?php
    if ($id_topic != 0) {
        ?>        
		<input type="hidden" name="t" value="<?php echo $id_topic; ?>">
<?php
    }
    if ($id_grp != 0) {
        ?>        
		<input type="hidden" name="g" value="<?php echo $id_grp; ?>">
<?php
    }
    ?>    
    	<b>Страница: </b>&nbsp;
    	<select class="pagsel" name="p">
<?php
    if ($pages > 0) {
        $cnt = 1;
        $total = $pages;
        while ($total > 0) {
            if ($page == $cnt) {
                ?>
			<option selected value="<?php echo $cnt; ?>"><?php
            } else {
                ?>
			<option value="<?php echo $cnt; ?>"><?php
            }
            echo $total;
            ?></option>
<?php
            $cnt = $cnt + 1;
            $total = $total - 1;
        }
    }
    ?>
		</select>
		<input class="btn_paging_sel" value="&nbsp;" type="submit">
	&nbsp;из <?php echo $pages; ?>
    </form>
<?php
    if ($pagenext != "") {
        ?>
	<span class="prev">
		<a href="<?php echo $pagenext; ?>"><span class="no-mob-view">Назад </span>»</a>
	</span>
<?php
    } else {
        ?>        
	<span class="prev"><span class="no-mob-view">Назад </span>»</span>
<?php
    }
    if ($pageprev != "") {
        ?>
	<span class="next">
		<a href="<?php echo $pageprev; ?>">«<span class="no-mob-view"> Вперед</span></a>
	</span>
<?php
    } else {
        ?>
	<span class="next">«<span class="no-mob-view"> Вперед</span></span>
<?php
    }
    if ($type == 'down') {
        ?>
	<a name="ftop"></a><span class="up_down"> <a href="#bottom">Вниз</a></span>
<?php
    } else {
        ?>
	<a name="bottom"></a><span class="up_down"> <a href="#ftop">Вверх</a></span>
<?php
    }
    ?>
</div>
<?php
}

$database = new PDO("sqlite:" . DBASEFILE);

if (!$database) {
    print("<b>Ошибка базы данных.</b>");
} else {
    $query = "CREATE TABLE IF NOT EXISTS ForumPosts " .
	     "(id INTEGER PRIMARY KEY, time DATE, id_grp INTEGER, id_topic INTEGER, id_user INTEGER, nick NVARCHAR, subj NVARCHAR, post NVARCHAR, id_session NVARCHAR, attachment NVARCHAR, modtime INTEGER, hidden INTEGER DEFAULT 0);";
    $database->exec($query);

    $query = "CREATE TABLE IF NOT EXISTS ForumTopics " .
	     "(id INTEGER PRIMARY KEY, id_grp INTEGER, id_user INTEGER, nick VARCHAR, topic VARCHAR, view INTEGER DEFAULT 0, id_session NVARCHAR, purgatory INTEGER DEFAULT 0, private INTEGER DEFAULT 0, readonly INTEGER DEFAULT 0);";
    $database->exec($query);

    $query = "CREATE TABLE IF NOT EXISTS ForumGroups " .
	     "(id INTEGER PRIMARY KEY, grp VARCHAR, note VARCHAR);";
    $database->exec($query);

    $query = "CREATE TABLE IF NOT EXISTS ForumUserLike " .
	     "(id INTEGER PRIMARY KEY, id_user INTEGER, id_like INTEGER, type INTEGER);";
    $database->exec($query);

    $query = "CREATE TABLE IF NOT EXISTS ForumUsers " .
	     "(id INTEGER PRIMARY KEY, login VARCHAR, password VARCHAR, email VARCHAR,".
         " fio VARCHAR, gender INTEGER, description VARCHAR, time INTEGER, last_login INTEGER,".
         " pubkey NVARCHAR, topics_rate INTEGER DEFAULT 0, status INTEGER DEFAULT 0);";
    $database->exec($query);

    $query = "REPLACE INTO ForumUsers (id, login) VALUES (0, 'Анонимно');";
    $database->exec($query);

    $query = "CREATE TABLE IF NOT EXISTS ForumPager " .
	     "(id INTEGER PRIMARY KEY, id_user INTEGER,  id_from_user INTEGER, new INTEGER, time INTEGER, subj VARCHAR, post VARCHAR, encrypted INTEGER);";
    $database->exec($query);

    $query = "CREATE TABLE IF NOT EXISTS ForumTopicUsers " .
	     "(id_topic INTEGER, id_user INTEGER, id_session NVARCHAR, readonly INTEGER DEFAULT 0);";
    $database->exec($query);

    $query = "CREATE TABLE IF NOT EXISTS ForumBlackLists ".
        "(id_user INTEGER NOT NULL, id_session NVARCHAR NOT NULL, UNIQUE(id_user, id_session));";
    $database->exec($query);

    $query = "CREATE TABLE IF NOT EXISTS ForumPostAttachment ".
        "(id_post INTEGER NOT NULL, idx INTEGER NOT NULL, attachment NVARCHAR NOT NULL, censor INTEGER DEFAULT 0, hash NVARCHAR, UNIQUE(id_post, idx));";
    $database->exec($query);

    unset($_SESSION['reloadpage']);

    check_login();

    $id_session = md5(session_id());

	$ctrlink = "";
	$show_groups = 0;
	$id_grp = 0;
	$id_topic = 0;
	$id_user = 0;
	$id_topic_owner = 0;
	$id_topic_owner_login = '';
	$topic_private = 0;
	$topic_private_access = 0;
	$topic_readonly = 0;

	$show_hot = 0;
	$page = 1;
	$topic = "ТОП ".$MAX_PAGE_ENTRIES." ГОРЯЧИХ ТЕМ";
	$search_opt = "";
	$reg_mode = 0;
	$show_search = 0;
	$show_users = 0;
	$show_trash_topics = $SHOW_TRASH_TOPICS;
	$show_mylist = 0;
	$show_mytopics = 0;
	$show_banlist = 0;

	$user_name = '';
	$user_name_warning = '';
	$user_password_warning = '';
	$user_email_warning = '';
	$user_email = '';
	$user_fio = '';
	$user_gender = '';
	$user_description = '';
	$user_pubkey = '';

	if (isset($_SESSION['myuser_id'])) {
	    $id_user = $_SESSION['myuser_id'];
	}
	
	if (is_defined('ban')) {
	    $session_id = addslashes($_REQUEST['ban']);
	    if ($session_id != "") {
	        if (is_logged()) {
	            $database->exec("INSERT INTO ForumBlackLists(id_user, id_session) VALUES($id_user, '$session_id')");
	        } else {
        		if (is_session('banlist')) {
        		    array_push($_SESSION['banlist'], $session_id);
        		} else {
        		    $_SESSION['banlist'] = array($session_id);
        		}
	        }
	    }
	    $uri = $_SERVER['REQUEST_URI'];
	    $uri = substr($uri, 0, strpos($uri, '&ban'));
	    header("Location: $uri", true, 301);
	    exit();
	}

	if (is_defined('hardcore')) {
	    if ($_REQUEST['hardcore'] == 1) {
	        $_SESSION['hardcore'] = 1;
	    } else {
	        unset($_SESSION['hardcore']);
	    }
	    redirect_without('hardcore');
	}

	if (is_defined("logout")) {
	    unset($_SESSION['myuser_name']);
	    unset($_SESSION['myuser_password']);
	    unset($_SESSION['myuser_id']);
	    unset($_SESSION['user_temp_name']);
	    unset($_SESSION['myuser_pubkey']);

	    redirect_without('logout');
	}

	if (is_defined("event")) {
	    $cmd = $_REQUEST["event"];
	    if ($cmd == "login") {
    		$myuser_name		= convert_string($_REQUEST["user"]["name"]);
    		$myuser_password	= convert_string($_REQUEST["user"]["password"]);
    
    		user_login($myuser_name, $myuser_password);
	    }
	}

	if (is_forum_admin()) {
	    if (is_defined('image_censor')) {
		$image_censor = $_REQUEST['image_censor'];
		if (is_defined('image_name')) {
		    $image_name = addslashes($_REQUEST['image_name']);

		    if (is_numeric($image_censor)) {
			if ($image_censor == 0) {
			    $image_censor = 1;
			} else {
			    $image_censor = -1;
			}
			$database->exec("UPDATE ForumPostAttachment SET censor=$image_censor WHERE attachment='$image_name'");
			$database->exec("UPDATE ForumPostAttachment SET censor=$image_censor WHERE hash=(SELECT hash FROM ForumPostAttachment WHERE attachment='$image_name')");
		    }

		    redirect_without('image_censor');
		}
	    }

	    if (is_defined('disableuser')) {
            $id_user = $_REQUEST['disableuser'];
            $id_user = ($id_user * 10) / 10;
            $database->exec("UPDATE ForumUsers SET status=1 WHERE id=$id_user");

	        redirect_without('disableuser');
	    }
	    
	    if (is_defined('enableuser')) {
	        $id_user = $_REQUEST['enableuser'];
	        $id_user = ($id_user * 10) / 10;
	        $database->exec("UPDATE ForumUsers SET status=0 WHERE id=$id_user");
	        
	        redirect_without('enableuser');
	    }

	    if (is_defined('purgatory')) {
	        $id_public = $_REQUEST['purgatory'];
	        $id_public = ($id_public * 10) / 10;
	        if ($id_public > 0) {
//	            $database->exec("UPDATE ForumUsers".
//	                " SET topics_rate = topics_rate - 1".
//	                " WHERE id = (SELECT id_user FROM ForumTopics WHERE id = $id_public AND id_user != 0 AND topics_rate >= 0)");
	            $database->exec("UPDATE ForumTopics SET purgatory = 1 WHERE id = $id_public;");
	        }
	        redirect_without('purgatory');
	    }

        if (is_defined('sdel')) {
            $session_id = addslashes($_REQUEST['sdel']);
            if ($session_id != "") {
                $database->exec("UPDATE ForumUsers ".
                    "SET topics_rate=topics_rate-(SELECT count(id_user) FROM ForumTopics WHERE id_session='$session_id' AND id_user!=0)".
                    " WHERE id IN (SELECT id_user FROM ForumTopics WHERE id_session='$session_id' AND id_user!=0)");
                $database->exec("DELETE FROM ForumPosts".
                    " WHERE id_topic IN (SELECT id FROM ForumTopics WHERE id_session = '$session_id')");
                $database->exec("DELETE FROM ForumTopics WHERE id_session = '$session_id'");
            }
            $uri = $_SERVER['REQUEST_URI'];
            $uri = substr($uri, 0, strpos($uri, '&sdel'));
            header("Location: $uri", true, 301);
            exit();
        }

        if (is_defined('shide')) {
            $id_topic = addslashes($_REQUEST['topic']);
            $session_id = addslashes($_REQUEST['shide']);
            $time = addslashes($_REQUEST['time']);
            $period = addslashes($_REQUEST['period']);

            if ($session_id != "" && is_numeric($time) && is_numeric($period) && is_numeric($id_topic) && $id_topic > 0) {
		$mintime = $time - $period * 60;
                $database->exec("UPDATE ForumPosts ".
                                "SET hidden=1 ".
                                "WHERE id_session='$session_id' AND id_topic=$id_topic AND time <= $time AND time > $mintime");
            }
            redirect_without('shide');
        }

        if (is_defined('shideall')) {
            $id_topic = addslashes($_REQUEST['topic']);
            $session_id = addslashes($_REQUEST['shideall']);
            $time = addslashes($_REQUEST['time']);
            $period = addslashes($_REQUEST['period']);

            if ($session_id != "" && is_numeric($time) && is_numeric($period) && is_numeric($id_topic) && $id_topic > 0) {
		$mintime = $time - $period * 60;
                $database->exec("UPDATE ForumPosts ".
                                "SET hidden=1 ".
                                "WHERE id_session='$session_id' AND time <= $time AND time > $mintime");
            }
            redirect_without('shideall');
        }

        if (is_defined("dp")) {
            $dp = $_REQUEST["dp"];
            $dp = ($dp * 10) / 10;
            $dt = $database->query("SELECT id_topic FROM ForumPosts WHERE id=$dp")->fetchColumn();
            $database->exec("DELETE FROM ForumPosts WHERE id=$dp");
            $database->exec("DELETE FROM ForumPostAttachment WHERE id_post=$dp");
            /* remove topic if last post was removed */
            $query = "DELETE FROM ForumTopics".
                " WHERE id=$dt AND (SELECT COUNT(*) FROM ForumPosts WHERE id_topic=$dt) = 0;";
            $database->exec($query);
        }
        if (is_defined("dt")) {
            $dt = $_REQUEST["dt"];
            $dt = ($dt * 10) / 10;
            if (is_defined("trash")) {
                $query = "UPDATE ForumPosts SET id_grp = $FORUM_TRASH_GID WHERE id_topic = $dt;";
                $database->exec($query);
                $query = "UPDATE ForumTopics SET id_grp = $FORUM_TRASH_GID, purgatory = 0 WHERE id = $dt;";
                $database->exec($query);
            } else {
                $database->exec("UPDATE ForumUsers".
                    " SET topics_rate=topics_rate-1".
                    " WHERE id=(SELECT id_user FROM ForumTopics WHERE id=$dt AND id_user!=0)");
                $query = "DELETE FROM ForumPosts WHERE id_topic = $dt;";
                $database->exec($query);
                $query = "DELETE FROM ForumTopics WHERE id = $dt;";
                $database->exec($query);
            }
        }
    }
    
    if (is_defined("p")) {
        $page = $_REQUEST["p"];
        if ($page < 1) {
            $page = 1;
        }
    }

	if (is_defined("g")) {
        $topic = "ГРУППЫ ТЕМ";
        $id_grp = $_REQUEST["g"];
        $id_grp = ($id_grp * 10) / 10;

        if (is_forum_admin()) {
            if (is_defined("public")) {
                $id_public = $_REQUEST['public'];
                $id_public = ($id_public * 10) / 10;
                if ($id_public > 0) {
                    $database->exec("UPDATE ForumUsers".
                        " SET topics_rate = topics_rate + 1".
                        " WHERE id = (SELECT id_user FROM ForumTopics WHERE id = $id_public AND id_user != 0 AND topics_rate < 0)");
                    $database->exec("UPDATE ForumTopics SET purgatory = 0 WHERE id = $id_public;");
                }
                redirect_without("public");
            }
        }

        if ($id_grp != 0) {
            $topic = $database->query("SELECT grp FROM ForumGroups WHERE id = $id_grp")->fetchColumn();
        } else {
            $topic = "ГРУППЫ ТЕМ";
            $show_groups = 1;
        }

        $nav_path = "<a href=\"?g=" . $id_grp . "\">" . $topic . "</a>";
	} else if (is_defined("topic") || is_defined("t")) {
        if (is_defined("topic")) {
            $id_topic = $_REQUEST["topic"];
        } else {
            $id_topic = $_REQUEST["t"];
        }

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

        $purgatory = 0;
        $topic_query = "SELECT ForumTopics.id_grp AS id_grp, ForumTopics.topic AS topic, ForumTopics.id AS id_topic,".
            " ForumTopics.purgatory AS purgatory, ForumTopics.id_user AS id_user, ForumGroups.grp AS grp,".
            " ForumTopics.private AS private, ForumTopics.readonly AS readonly, ForumUsers.login AS login".
            " FROM ForumTopics, ForumGroups, ForumUsers".
            " WHERE ForumTopics.id = $id_topic AND ForumTopics.id_grp = ForumGroups.id".
            " AND ForumUsers.id = ForumTopics.id_user";
        foreach ($database->query($topic_query) as $row) {
            $topic = $row['topic'];
            $id_topic = $row['id_topic'];
            $group = $row['grp'];
            $id_group = $row['id_grp'];
            $purgatory = $row['purgatory'];
            $id_topic_owner = $row['id_user'];
            $topic_owner_login = $row['login'];
            $topic_private = $row['private'];
            $topic_readonly = $row['readonly'];
        }

        if (is_defined("hide")) {
            if ($id_topic_owner != 0 && $id_topic_owner == $id_user) {
                $id_post = $_REQUEST['hide'];
                if (is_numeric($id_post)) {
                    $database->exec("UPDATE ForumPosts SET hidden=1 WHERE id=$id_post AND id_topic=$id_topic");
                }
            }
            redirect_without('hide');
        }

        if ($id_user != 0) {
            if ($database->query("SELECT id_user FROM ForumUserLike WHERE id_user=$id_user AND id_like=$id_topic AND type=0")->fetchColumn() == $id_user) {
                $ctrlink = '<a style="float: right" href="?topic=' . $id_topic . '&like=0">-</a>';
            } else {
                $ctrlink = '<a style="float: right" href="?topic=' . $id_topic . '&like=1">+</a>';
            }
        }

        $nav_path = "<a href=\"?g=$id_group\">$group</a> &nbsp;/&nbsp; <a href=\"?topic=$id_topic\">$topic</a>";
        if ($purgatory != 0 && is_hardcore_on() == 0) {
            $nav_path = $nav_path . "&nbsp[<a href=\"?g=$FORUM_PURGATORIUM_GID\">Чистилище</a>]";
        }
        unset($purgatory);
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

        $nav_path = $topic;
	} else if (is_defined("users")) {
	    $show_users = 1;
	    $show_users_string = convert_string($_REQUEST["users"]);

	    $topic = "ПОЛЬЗОВАТЕЛИ";

	    $nav_path = "Список пользователей";
	} else if (is_defined("search")) {
        $s = convert_string($_REQUEST["search"]);

        if ($s == "") {
            $show_search = 1;

            $topic = "ПОИСК";
        } else {
            $search_opt = " AND ForumTopics.topic LIKE '%" . $s . "%'";
        }

        $nav_path = "Поиск";
	} else {
        if (is_defined("s")) {
            $show_hot = 1;
            $topic = "ТОП " . $MAX_PAGE_ENTRIES . " ОБЩЕНИЯ";
        }
        if (is_defined("m")) {
            $show_mylist = 1;
            $topic = "МОИ ИЗБРАННЫЕ ТЕМЫ";
        }
        if (is_defined("o")) {
            $show_mytopics = 1;
            $topic = "МОИ ТЕМЫ";
        }

        $nav_path = $topic;
	}

	if (is_defined("event")) {
        $cmd = $_REQUEST["event"];
        if ($cmd == "forumcreatesubj") {
            if ($id_topic != 0 && $topic_readonly && $id_topic_owner != $id_user) {
                $myObj = [
                    'error' => "Тема закрыта для сообщений...",
                    'url' => "?topic=$id_topic"
                ];

                echo json_encode($myObj);

                exit();
            }

            $sth = $database->prepare("SELECT readonly, id_session".
                " FROM ForumTopicUsers".
                " WHERE id_topic=$id_topic AND id_user=$id_user AND (id_session='' OR id_session='$id_session')");
            $sth->execute();
            $row = $sth->fetch();

            if ($row != null) {
                if ($row['readonly'] == 1) {
                    if ($id_user != 0 || ($id_user == 0 and $row['id_session'] != '')) {
                        $myObj = [
                            'error' => "Тема закрыта для ваших сообщений.",
                            'url' => "?topic=$id_topic"
                        ];
                    } else {
                        $myObj = [
                            'error' => "Тема закрыта для анонимных сообщений.",
                            'url' => "?topic=$id_topic"
                        ];
                    }

                    echo json_encode($myObj);

                    exit();
                }
            }

            $nick = convert_string($_REQUEST["message"]["author"]);
            $subj = convert_string($_REQUEST["message"]["caption"]);
            $post = convert_text($_REQUEST["message"]["content"]);
            $post_id = "";
            $topic_id = "";
            $uri = '';
            $post_error_message = '';

            if (mb_strlen($post) > 16384) {
                $post = mb_substr($post, 0, 16383);
            }

            if (isset($_POST['recaptcha_response']) || $debug) {
                // Build POST request:
                $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
                // $recaptcha_secret = 'YOUR_RECAPTCHA_SECRET_KEY';
                $recaptcha_response = $_POST['recaptcha_response'];

                // Make and decode POST request:
                $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $RECAPTCHA_SERV_KEY . '&response=' . $recaptcha_response);
                $recaptcha = json_decode($recaptcha);

                // Take action based on the score returned:
                // echo "<!-- recaptcha $recaptcha->success $recaptcha->score -->";

                if ($recaptcha->score >= 0.5 || $debug) {
                    if ($nick == "") {
                        unset($_SESSION['user_temp_name']);
                    } else if (! isset($_POST['edit_post_info'])) {
                        $_SESSION['user_temp_name'] = $nick;
                    }

                    $tim = time();

                    $purgatory = 1;

                    if ($nick == "" && $id_user != 0) {
                        $nick = $_SESSION['myuser_name']; // $database->query("SELECT login FROM ForumUsers WHERE id = '$id_user'")->fetchColumn();
                    }

                    if ($nick == "") {
                        $nick = "Анонимно";
                    }

                    if ($id_user == 0 || $nick != $_SESSION['myuser_name']) {
                        $is_registered_user = $database->query("SELECT id FROM ForumUsers WHERE login='$nick'")->fetchColumn();
                        if (is_numeric($is_registered_user)) {
                            if ($is_registered_user > 0) {
                                $myObj = [
                                    'error' => 'Выбранное вами имя имеет зарегистрированного владельца. Войдите, если оно ваше.',
                                    'url' => "?topic=$id_topic"
                                ];

                                echo json_encode($myObj);

                                exit();
                            }
                        }
                    }

                    $mymoder = new AutoModerator();
                    if ($mymoder->moderated($subj . ' ' . $nick)) {
                        $purgatory = 0;

                        if ($id_user != 0 && $nick === $_SESSION['myuser_name']) {
                            $topics_rate = $database->query("SELECT topics_rate FROM ForumUsers WHERE id=$id_user")->fetchColumn();
                            if ($topics_rate < 0) {
                                $purgatory = 1;
                            }
                        }
                    }

                    if ($id_topic != 0) {
                        if ($post != "") {
                            if (isset($_POST['edit_post_info'])) {
                                $edit_post_id = $_POST['edit_post_info'];
                                $edit_post_id = ($edit_post_id * 10) / 10;

                                $sth = $database->prepare("SELECT id, id_topic, time, nick, subj, post, modtime".
                                    " FROM ForumPosts".
                                    " WHERE id_session='$id_session' AND id=$edit_post_id");
                                $sth->execute();
                                $row = $sth->fetch();
                                if ($row['id'] != "") {
                                    $modtime = time();

                                    $database->exec("UPDATE ForumPosts SET id_user='$id_user', nick='$nick', subj='$subj', post='$post', modtime=$modtime  WHERE id = $edit_post_id");
                                    $post_id = $row['id'];

                                    if ($subj != "") {
                                        $database->exec("UPDATE ForumTopics SET topic='$subj', nick='$nick', id_user='$id_user', purgatory=$purgatory".
                                            " WHERE id=(SELECT id_topic FROM ForumPosts WHERE id=(SELECT id FROM ForumPosts WHERE id_topic='{$row['id_topic']}' AND id_session='$id_session' ORDER BY id ASC LIMIT 1) AND id=(SELECT id FROM ForumPosts WHERE id_topic='{$row['id_topic']}' ORDER BY id ASC LIMIT 1) AND id='$post_id')");
                                    }
                                }
                            } else {
                                $query = "INSERT INTO ForumPosts (id, time, id_grp, id_topic, id_user, nick, subj, post, id_session)".
                                    " VALUES (NULL, '$tim', (SELECT id_grp FROM ForumTopics WHERE id = '$id_topic'), $id_topic, $id_user, '$nick', '$subj', '$post', '$id_session');";
                                $database->exec($query);
                                $post_id = $database->lastInsertId();
                            }
                        }
                    } else {
                        if ($post != "" && $subj != "") {
                            if ($database->query("SELECT id FROM ForumTopics WHERE topic = '$subj'")->fetchColumn() == "") {
                                $query = "REPLACE INTO ForumTopics (id, id_grp, id_user, nick, topic, view, id_session, purgatory)".
                                    " VALUES ((SELECT id FROM ForumTopics WHERE topic = '$subj'), $id_grp,".
                                    " coalesce((SELECT id_user FROM ForumTopics WHERE topic = '$subj'), $id_user),".
                                    " coalesce((SELECT nick FROM ForumTopics WHERE topic = '$subj'),'$nick'), '$subj',".
                                    " coalesce((SELECT view FROM ForumTopics WHERE topic = '$subj'), 0),".
                                    " coalesce((SELECT id_session FROM ForumTopics WHERE topic = '$subj'), '$id_session'),".
                                    " $purgatory)";
                                $database->exec($query);
                                $topic_id = $database->lastInsertId();
                                $query = "INSERT INTO ForumPosts (id, time, id_grp, id_topic, id_user, nick, subj, post, id_session)".
                                    " VALUES (NULL, '$tim', $id_grp, (SELECT id FROM ForumTopics WHERE topic = '$subj'), $id_user, '$nick', '$subj', '$post', '$id_session')";
                                $database->exec($query);
                                $post_id = $database->lastInsertId();
                            } else {
                                $post_error_message = "Тема с таким заголовком уже существует!";
                            }
                        } else {
                            if ($subj == "") {
                                $post_error_message = "Заголовок темы не может быть пустым!";
                            }
                        }
                    }

                    if ($post == "") {
                        $post_error_message = "Сообщение не может быть пустым!";
                    } else {
                        $image = $_FILES['image']['name'];
                        $image_tmp = $_FILES['image']['tmp_name'];
                        $image_ext = strtolower(substr(strrchr($image, '.'), 1));

                        if ($post != "" && $post_id != "") {
                            if ($image_ext == 'jpg' || $image_ext == 'jpeg' || $image_ext == 'gif' || $image_ext == 'png' || $image_ext == 'webp' ||
                                $image_ext == 'oga' || $image_ext == 'mp4a' || $image_ext == 'm4a' ||
                                $image_ext == 'mp4' || $image_ext == 'mpg4' || $image_ext == 'mpeg4' || $image_ext == 'ogv' || $image_ext == 'webm') {

                                $img_file = "att-$post_id.$image_ext";

                                if ($image_ext == 'jpg' || $image_ext == 'jpeg' || $image_ext == 'gif' || $image_ext == 'png' || $image_ext == 'webp') {
                                    system('convert -resize 420x315\> -quality 85 ' . $image_tmp . ' ' . $UPLOAD_DIR . '/small-' . $img_file);
                                }

                                move_uploaded_file($image_tmp, "$UPLOAD_DIR/$img_file");

                                $image_hash = md5_file("$UPLOAD_DIR/$img_file");

                                $image_censor = $database->query("SELECT censor FROM ForumPostAttachment WHERE hash='$image_hash' ORDER BY censor ASC LIMIT 1")->fetchColumn();
                                 if ($image_censor === FALSE) {
                                     $database->exec("INSERT OR REPLACE INTO ForumPostAttachment (id_post, idx, attachment, hash) VALUES($post_id, 0, '$img_file', '$image_hash')");
                                 } else {
                                     $database->exec("INSERT OR REPLACE INTO ForumPostAttachment (id_post, idx, attachment, censor, hash) VALUES($post_id, 0, '$img_file', $image_censor, '$image_hash')");
                                 }
                            }
                        }

                        if ($id_topic != 0) {
                            $uri = $_SERVER['REQUEST_URI'];
                        } else {
                            $uri = "?topic=$topic_id";
                        }
                        // header("Location: $uri", true, 301);
                        // exit();
                    }
                } else {
                    // Not verified - show form error
                    $post_error_message = "Робот обнаружен? Попробуйте изменить текст или отправить его чуть позже.";
                }
            } else {
                $post_error_message = "Робот обнаружен? Данные рекапчи не найдены.";
            }

            $myObj = [
                'error' => $post_error_message,
                'url' => $uri
            ];

            echo json_encode($myObj);

            exit();
        } else if ($cmd == "createuser" || $cmd == "updateuser") {
            $user_name = convert_string($_REQUEST["user"]["user_name"]);
            $user_password = convert_string($_REQUEST["user"]["user_password"]);
            $user_password_confirm = convert_string($_REQUEST["user"]["user_password_confirm"]);
            $user_email = convert_string($_REQUEST["user"]["user_email"]);
            $user_fio = convert_string($_REQUEST["user"]["user_fio"]);
            $user_gender = convert_string($_REQUEST["user"]["user_gender"]);
            $user_description = convert_text($_REQUEST["user"]["description"]);
            $user_pubkey = addslashes($_REQUEST["user"]["pubkey"]);
            if ($user_name == "") {
                $user_name_warning = '<div class="error">Имя пользователя не может быть пустым.</div>';
            } else if ($user_password == "" && $cmd != "updateuser") {
                $user_password_warning = '<div class="error">Пароль не может быть пустым.</div>';
                $user_password = "";
                $user_password_confirm = "";
            } else if ($user_password != $user_password_confirm) {
                $user_password_warning = '<div class="error">Пароль и его подтверждение не совпадают.</div>';
                $user_password = "";
                $user_password_confirm = "";
            } else if ($user_email == "") {
                $user_email_warning = '<div class="error">E-mail не может быть пустым.</div>';
            } else if ($cmd == "updateuser") {
                if ($user_password != "") {
                    $user_password = md5($user_password);
                } else {
                    $user_password = $_SESSION['myuser_password'];
                }
                $query = "UPDATE ForumUsers SET login = '$user_name', password = '$user_password', email = '$user_email', fio = '$user_fio', gender = '$user_gender', description = '$user_description', pubkey = '$user_pubkey' WHERE id = $id_user;";
                $database->exec($query);
                $reg_mode = 4;
            } else {
                $user_password = md5($user_password);
                $user_name_fake_lat = convert_fake_string($user_name, 0);
                $user_name_fake_cyr = convert_fake_string($user_name, 1);

                $login = $database->query("SELECT login FROM ForumUsers WHERE login LIKE '$user_name' OR login LIKE '$user_name_fake_lat' OR login LIKE '$user_name_fake_cyr' LIMIT 1")->fetchColumn();
                if ($login == "") {
                    $tim = time();
                    $query = "REPLACE INTO ForumUsers (id, login, password, email, fio, gender, description, time, last_login)".
                        " VALUES (NULL, '$user_name', '$user_password', '$user_email', '$user_fio', '$user_gender', '$user_description', $tim, 0)";
                    $database->exec($query);
                    $reg_mode = 2;
                } else {
                    $user_name_warning = '<div class="error">Имя пользователя уже занято.</div>';
                    $user_name = "";
                }
            }

            if ($user_name_warning == '') {
                $image = $_FILES['image']['name'];
                $image_tmp = $_FILES['image']['tmp_name'];
                $image_ext = strtolower(substr(strrchr($image, '.'), 1));
                if ($reg_mode == 2 || $reg_mode == 4) {
                    if ($image_ext == "jpg" || $image_ext == "jpeg" || $image_ext == "gif" || $image_ext == "png") {
                        $image_id = $database->query("SELECT id FROM ForumUsers WHERE login LIKE '$user_name'")->fetchColumn();

                        // FIXME: add image to user record
                        $img_file = 'id' . $image_id;
                        if (file_exists($UPLOAD_DIR . '/' . $img_file . '.jpg')) {
                            unlink($UPLOAD_DIR . '/' . $img_file . '.jpg');
                            unlink($UPLOAD_DIR . '/small-' . $img_file . '.jpg');
                        } else if (file_exists($UPLOAD_DIR . '/' . $img_file . '.png')) {
                            unlink($UPLOAD_DIR . '/' . $img_file . '.png');
                            unlink($UPLOAD_DIR . '/small-' . $img_file . '.png');
                        } else if (file_exists($UPLOAD_DIR . '/' . $img_file . '.gif')) {
                            unlink($UPLOAD_DIR . '/' . $img_file . '.gif');
                            unlink($UPLOAD_DIR . '/small-' . $img_file . '.gif');
                        }

                        $img_file = 'id' . $image_id . '.' . $image_ext;
                        if (is_uploaded_file($image_tmp)) {
                            system('convert -resize 640x640\> -quality 85 ' . $image_tmp . ' ' . $UPLOAD_DIR . '/' . $img_file);
                            system('convert -resize 70x70\>   -quality 85 ' . $image_tmp . ' ' . $UPLOAD_DIR . '/small-' . $img_file);
                        }
                    }
                }
            }
        }
    }

	start_page($topic);
	
	if (is_logged()) {
?>
	<script>const userName="<?php echo $_SESSION['myuser_name']; ?>";</script>
<?php
	}
	
	show_banner();
	show_menu($database);
	show_nav_path($nav_path, $ctrlink);

	if ($id_grp != 0) {
        if (!(isset($FORUM_PURGATORIUM_GID) && $id_grp == $FORUM_PURGATORIUM_GID) &&
            !(isset($FORUM_NEWSVTOMSKE_GID) && $id_grp == $FORUM_NEWSVTOMSKE_GID) &&
            !(isset($FORUM_TRASH_GID) && $id_grp == $FORUM_TRASH_GID)) {
            show_postbox('topic', $id_session);
        }
	} else if ($id_topic != 0) {
        if ($topic_private) {
            if ($id_topic_owner === $id_user) {
                $topic_private_access = 1;
            } else if (is_numeric($database->query("SELECT readonly FROM ForumTopicUsers".
                " WHERE id_topic=$id_topic AND id_user=$id_user AND (id_user!=0 OR (id_user=0 AND id_session='$id_session'))")->fetchColumn())) {
                $topic_private_access = 1;
            }
        }

        if ($topic_private == 0 || ($topic_private && $topic_private_access)) {
            show_postbox('post', $id_session);
        }
	}

	if ($show_search == 1) {
?>
<div class="block1">
	<form action="">
		<input type="text" name="search" onfocus="if(this.value == 'Поиск по темам...') { this.value = ''; }" value="Поиск по темам..."/>
		<input class="btn_group_sel" type="submit" value="&nbsp;"/>
	</form>
</div>
<?php
	} else if ($show_users == 1) {

        function lower_ru($str)
        {
            return mb_strtolower($str, 'utf-8');
        }

        function mb_str_split_compat($string)
        {
            return preg_split('/(?<!^)(?!$)/u', $string);
        }

        function show_letters_links($str)
        {
            foreach (mb_str_split_compat($str) as $letter) {
                ?>
<a href="?users=<?php echo $letter; ?>"><?php echo $letter; ?></a>
<?php
            }
        }
        ?>
<div class="box_alfavit">
<?php
	    $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	    show_letters_links($str);
?>
	&nbsp;&nbsp;
<?php
	    $str = "АБВГДЕЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ";
	    show_letters_links($str);
?>
</div>
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
<tr>
	<th>На форуме</th>
	<th>Пол</th>
	<th>Фото</th>
	<th>Время посещения</th>
	<th class="no-mob-view">Дата регистрации</th>
</tr>
<?php
	    if (mb_strlen($show_users_string, 'utf-8') > 1) {
            $view_query = "SELECT id, login, last_login, time, gender, topics_rate, status FROM ForumUsers WHERE login LIKE '".$show_users_string."%';";
	    } else {
            $view_query = "SELECT id, login, last_login, time, gender, topics_rate, status FROM ForumUsers WHERE login LIKE '".$show_users_string."%' OR login LIKE '".lower_ru($show_users_string)."%';";
	    }

	    $g = array(0 => 'Не указан', 1 => 'Не имеет значения', 2 => 'Мужской', 3 => 'Женский', 4 => 'Средний');

	    foreach ($database->query($view_query) as $row) {
    		if ($row['id'] == 0) {
    		    continue;
    		}
    
    		$gender_id = 0;
    		if (is_numeric($row['gender'])) {
    		    $gender_id = $row['gender'];
    		}
?>
<tr>
	<td><?php echo format_user_nick($row['login'], $row['id'], $row['login'], $row['id']); ?>
<?php
            if (is_forum_admin()) {
?>
		<sup><?php echo $row['topics_rate']?></sup>
<?php
                if ($row['status'] == 1) {
                    ?>
		<a href="?users&enableuser=<?php echo $row['id']?>" class="remove">Включить</a>
<?php
                } else {
?>
		<a href="?users&disableuser=<?php echo $row['id']?>" class="remove">Отключить</a>
<?php
                }
            }
?>
	</td>
	<td class="tdw1"><?php echo $g[$gender_id]; ?></td>
<?php
            $avatar = $UPLOAD_DIR."/small-id".$row['id'].".jpg";
            if (file_exists($avatar)) {
?>
	<td class="tdw2">ЕСТЬ</td>
<?php
            } else {
?>
	<td class="tdw2">НЕТ</td>
<?php
            }
?>
	<td class="tdu3"><?php echo date('d.m.Y (H:i)', $row['last_login']); ?></td>
	<td class="tdu3 no-mob-view"><?php echo date('d.m.Y (H:i)', $row['time']); ?></td>
</tr>
<?php
	    }
?>
</tbody>
</table>
<?php
	} else if ($reg_mode == 1 || $reg_mode == 3) {
?>
<div class="box_pasport">
	<div class="box_pasport_bg">
<?php
	    $user_password = "";

	    if ($reg_mode == 1) {
?>
		<h3>Регистрация пользователя</h3>
		<h4>Для регистрации на Форуме Вам необходимо заполнить форму. Поля, обязательные для заполнения, обозначены значком (*).</h4>
		<form action="" method="post" class="form_reg" name="registration" enctype="multipart/form-data">
			<input type="hidden" name="event" value="createuser">
			<label for="login">* Имя пользователя (login) <?php echo $user_name_warning; ?></label>
			<input type="text" autocomplete="username" class="inp_text_reg" name="user[user_name]" id="login" maxlength="20" value="<?php echo $user_name; ?>">
			<div class="box_small_text">Если выбранное Вами имя уже зарегистрировано, Вы сможете просто ввести другое имя, при этом остальные заполненные поля будут сохранены.</div>
<?php
	    } else {
    		$sth = $database->prepare("SELECT login, email, fio, gender, description, last_login FROM ForumUsers WHERE id = $id_user");
    		$sth->execute();
    		$row = $sth->fetch();
    		if ($row != null) {
    		    $user_name = $row['login'];
    		    $user_password = '';
    		    $user_email = $row['email'];
    		    $user_fio = $row['fio'];
    		    $user_gender = $row['gender'];
    		    $user_description = reconvert_text($row['description']);
    		}
?>
		<h3>Настройки пользователя</h3>
		<h4>Изменение личных настроек пользователя.</h4>
		<form action="" method="post" class="form_reg" name="registration" enctype="multipart/form-data" id="regeditform">
			<input type="hidden" name="event" value="updateuser">
			<label for="login">* Имя пользователя </label>
			<input type="text" class="inp_text_reg" name="user[user_name]" id="login" maxlength="20" value="<?php echo $user_name; ?>" readonly="readonly">
			<div class="box_small_text">Вы не можете изменить имя пользователя.</div>
<?php
	    }
?>
			<div class="line2"><div></div></div>
			<label for="password1">* Пароль <?php echo $user_password_warning; ?></label>
			<input type="password" autocomplete="new-password" class="inp_text_reg" name="user[user_password]" id="password1" maxlength="100" value="<?php echo $user_password; ?>">
			<label for="password2">* Подтверждение пароля </label>
			<input type="password" autocomplete="new-password" class="inp_text_reg" name="user[user_password_confirm]" id="password2" maxlength="100" value="<?php echo $user_password; ?>">
			<div class="box_small_text">При наборе пароля допускаются любые буквы (как русские, так и латинские) и символы. Пароль регистрозависим (советуем перед набором глянуть на Caps Lock)</div>
			<div class="line2"><div></div></div>
			<label for="email">* Ваш e-mail <?php echo $user_email_warning; ?></label>
			<input type="text" class="inp_text_reg" name="user[user_email]" id="email"  value="<?php echo $user_email; ?>">
			<div class="line2"><div></div></div>
			<label for="fio">Имя Фамилия Отчество</label>
			<input type="text" class="inp_text_reg" name="user[user_fio]" id="fio"  value="<?php echo $user_fio; ?>">
			<label>Ваш пол</label>
<?php
	   $cnt = 1;
	   foreach(array('Не имеет значения','Мужской','Женский','Средний') as $name) {
	       if ($cnt == $user_gender) {
?>
			<input type="radio" name="user[user_gender]" value="<?php echo $cnt; ?>" id="<?php echo $cnt; ?>" class="radioinput" checked="checked" >
<?php
            } else {
?>
			<input type="radio" name="user[user_gender]" value="<?php echo $cnt; ?>" id="<?php echo $cnt; ?>" class="radioinput" >
<?php
            }
?>
			<label for="<?php echo $cnt; ?>" class="radiolab"><?php echo $name; ?></label><br>
<?php
            $cnt = $cnt + 1;
        }
?>
			<label for="addition">Дополнительно</label>
			<textarea maxlength="4096" name="user[description]" id="addition" class="area_text_reg"><?php echo $user_description; ?></textarea>
			<input type="hidden" name="MAX_FILE_SIZE" value="500000">
			<label for="image">Аватар:</label><input name="image" type="file">
			<div class="box_small_text">Разрешается загрузить картинку jpg, png или gif и размером не более 500КБ.</div>
			<textarea name="user[pubkey]" id="pubkey" style="display:none;"></textarea>
			<div class="line2"><div></div></div>
			<div class="box_small_text">Если вся информация верна - нажмите кнопку (достаточно одного раза):</div>
<?php
	    if ($reg_mode == 1) {
?>
			<input type="submit" class="btn_reg" value="Зарегистрироваться">
<?php
	    } else {
?>
			<input type="submit" class="btn_reg" value="Сохранить">
<?php
	    }
?>
		</form>
<?php
        if ($reg_mode == 3) {
?>
		<script src="js/pgphelp.js"></script>
		<a name="pager"></a>
		<h3>Настройка пейджера</h3>
		<div><span class="warning" id="pgpregwarn"></span></div>
		<div><span class="error" id="pgpregerror"></span></div>
		<h4>Закрытый ключ шифрования хранится на компьютере или мобильном устройстве пользователя, зашифрованные сообщения могут быть прочитаны только получателем или отправителем.</h4>
		<label for="privkey">Закрытый PGP ключ<span class="error" id="lbprivkey"></span></label>
		<textarea maxlength="4096" id="privkey" class="area_text_reg" placeholder="Оставьте пустым для создания нового ключа (старые шифрованные сообщения будут утеряны) или вставьте старый ключ..."></textarea>
		<div class="box_small_text">Скопируйте и храните закрытый ключ в надежном месте, недоступном для посторонних</div>
		<label for="passphrase">Пароль закрытого ключа<span class="error" id="lbpassphrase"></span></label>
		<input type="text" class="inp_text_reg" id="passphrase" value="">
		<div class="box_small_text">Пароль старого ключа(если установлен) или задайте для нового (необязательно)</div>
		<label for="x_pubkey">Открытый PGP ключ</label>
		<textarea readonly id="x_pubkey" class="area_text_reg"></textarea>
		<div class="line2"><div></div></div>
		<div class="btn_reg_key">
<?php
            if ($_SESSION['myuser_pubkey'] !== "") {
?>
			<textarea readonly style="display:none;" id="active_pubkey"><?php echo $_SESSION['myuser_pubkey']; ?></textarea>
			<button type="button" class="btn_reg_left" onclick="return pgpRegResetKey();">Запретить</button>
<?php
            } else {
?>
			<textarea readonly style="display:none;" id="active_pubkey"></textarea>
			<button type="button" class="btn_reg_left" onclick="return pgpRegSetKey(1);">Разрешить</button>
<?php
            }
?>
			<span id="addremove_key_button"></span>
		</div>
<?php
        }
?>
	</div>
</div>
<?php
    } else if ($reg_mode == 2) {
?>
<div class="box_pasport">
	<div class="box_pasport_bg">
		<h3>Регистрация пользователя завершена!</h3>
		Теперь вы можете войти на форум под своим именем пользователя.
	</div>
</div>
<?php
    } else if ($reg_mode == 4) {
?>
<div class="box_pasport">
	<div class="box_pasport_bg">
		<h3>Настройки пользователя изменены!</h3>
		Вы всегда можете изменить свои личные настройки.
	</div>
</div>
<?php
	} else if ($show_groups != 0) {
	    if (isset($FORUM_PURGATORIUM_GID) && is_hardcore_on() == 0) {
	        $group_query_purgatory = 'purgatory=0 AND';
	        $group_query_purgatory1 = 'AND ForumTopics.purgatory=0';
	    } else {
	        $group_query_purgatory = '';
	        $group_query_purgatory1 = '';
	    }
	    $group_query = "SELECT id, grp, note FROM ForumGroups ORDER BY grp ASC ;";
	    foreach ($database->query($group_query) as $row) {
            $topics = 0;
            $updated = 0;
			$view_query =
"SELECT (SELECT COUNT(*) FROM ForumTopics WHERE $group_query_purgatory id_grp = {$row['id']}) as topics, ".
"(SELECT MAX(ForumPosts.time) FROM ForumPosts,ForumTopics WHERE ForumPosts.id_grp = {$row['id']} AND ForumPosts.id_topic = ForumTopics.id $group_query_purgatory1) as time;";
			if (isset($FORUM_PURGATORIUM_GID) && $row['id'] == $FORUM_PURGATORIUM_GID) {
                if (is_hardcore_on()) {
                    continue;
                } else {
                    $view_query =
"SELECT (SELECT COUNT(*) FROM ForumTopics WHERE purgatory!=0 AND id_grp != $FORUM_TRASH_GID AND id_grp != $FORUM_NEWSVTOMSKE_GID) as topics,".
" (SELECT MAX(ForumPosts.time) FROM ForumPosts,ForumTopics WHERE ForumPosts.id_topic = ForumTopics.id AND ForumTopics.purgatory!=0 AND ForumTopics.id_grp != $FORUM_TRASH_GID AND ForumTopics.id_grp != $FORUM_NEWSVTOMSKE_GID) as time;";
                }
            }
            foreach ($database->query($view_query) as $row1) {
                $topics = $row1['topics'];
                $updated = $row1['time'];
                if ($updated != "") {
                    $updated = date('d.m.Y H:i', $updated);
                }
            }
?>
<div class="box1">
	<a href="?g=<?php echo $row['id']; ?>" class="title"><?php echo $row['grp']; ?></a>
	<?php echo $row['note']; ?><br>
	<span class="white">
		Тем: <span class="bold"><?php echo $topics; ?></span>
		&nbsp;|&nbsp;
		Обновление: <span class="bold"><?php echo $updated; ?></span>
	</span>
</div>
<?php
        }
	} else if ($id_topic == 0) {
	    $posts = 0;
	    $pnext = "";
	    $pprev = "";
	    $having_query = "";

	    $base_query_hidden = '';
	    if (is_hardcore_on() == 0) {
	        $base_query_hidden = " AND ForumPosts.hidden = 0 ";
	    }
	    
	    $base_query = "SELECT ForumTopics.nick AS nick, ForumTopics.id_user AS id_user, ForumTopics.view AS view,".
			  " ForumUsers.login AS login, ForumUsers.id AS id, COUNT(*) AS posts, MAX(ForumPosts.time) AS time,".
			  " ForumTopics.topic AS topic, ForumPosts.id_topic AS id_topic, ForumPosts.nick AS last_nick,".
			  " ForumPosts.id_user AS last_id_user, ForumGroups.grp AS grp".
			  " FROM ForumPosts, ForumTopics, ForumUsers, ForumGroups".
			  " WHERE ForumPosts.id_topic = ForumTopics.id AND ForumGroups.id = ForumTopics.id_grp".
			  $base_query_hidden;
	    $count_query = "SELECT COUNT(*)".
			   " FROM ForumTopics,ForumUsers WHERE ForumTopics.id_user=ForumUsers.id ";

	    if ($id_grp == 0) {
            if (! $show_trash_topics) {
                $base_query = "$base_query  AND ForumTopics.id_grp != $FORUM_TRASH_GID";
                $count_query = "$count_query AND ForumTopics.id_grp != $FORUM_TRASH_GID";
            }

            if (isset($FORUM_NEWSVTOMSKE_GID)) {
                $having_query = " ForumTopics.id_grp != $FORUM_NEWSVTOMSKE_GID ";
                $having_query = " ($having_query OR (ForumTopics.id_grp=$FORUM_NEWSVTOMSKE_GID AND COUNT(*) > 1)) ";
                $count_query = "$count_query AND ForumTopics.id IN" . " (select id_topic from ForumPosts group by id_topic having id_grp!=$FORUM_NEWSVTOMSKE_GID or (id_grp=$FORUM_NEWSVTOMSKE_GID and count(*) > 1))";
            }

            if (isset($FORUM_PURGATORIUM_GID) && is_hardcore_on() == 0) {
                if ($having_query != "") {
                    $having_query = "$having_query AND";
                    $count_query = "$count_query AND";
                }
                if (isset($FORUM_NEWSVTOMSKE_GID)) {
                    $having_query = "$having_query (ForumTopics.id_grp = $FORUM_NEWSVTOMSKE_GID OR ForumTopics.purgatory = 0) ";
                    $count_query = "$count_query (ForumTopics.id_grp = $FORUM_NEWSVTOMSKE_GID OR ForumTopics.purgatory = 0) ";
                } else {
                    $having_query = "$having_query ForumTopics.purgatory = 0 ";
                    $count_query = "$count_query ForumTopics.purgatory = 0 ";
                }
            }

            $having_query = " HAVING $having_query";
            $base_query = "$base_query  $search_opt";
            $count_query = "$count_query $search_opt";
	    }

	    if ($id_grp != 0) {
	        if (isset($FORUM_PURGATORIUM_GID) && $id_grp === $FORUM_PURGATORIUM_GID && is_hardcore_on() == 0) {
                if (isset($FORUM_TRASH_GID)) {
                    $base_query = "$base_query AND ForumTopics.id_grp != $FORUM_TRASH_GID";
                    $count_query = "$count_query AND ForumTopics.id_grp != $FORUM_TRASH_GID";
                }

                if (isset($FORUM_NEWSVTOMSKE_GID)) {
                    $base_query = "$base_query AND ForumTopics.id_grp != $FORUM_NEWSVTOMSKE_GID";
                    $count_query = "$count_query AND ForumTopics.id_grp != $FORUM_NEWSVTOMSKE_GID";
                }

                $base_query = "$base_query  AND ForumUsers.id = ForumTopics.id_user AND ForumTopics.purgatory != 0";
                $count_query = "$count_query AND ForumTopics.purgatory != 0";
            } else {
                if (isset($FORUM_PURGATORIUM_GID) && is_hardcore_on() == 0) {
                    $base_query = "$base_query  AND ForumPosts.id_grp = $id_grp AND ForumUsers.id = ForumTopics.id_user " . "AND ForumTopics.purgatory = 0";
                    $count_query = "$count_query AND ForumTopics.id_grp = $id_grp " . "AND ForumTopics.purgatory = 0";
                } else {
                    $base_query = "$base_query  AND ForumPosts.id_grp = $id_grp AND ForumUsers.id = ForumTopics.id_user";
                    $count_query = "$count_query AND ForumTopics.id_grp = $id_grp";
                }
            }
            // echo "<!-- 1count_query $count_query -->";

            $posts = $database->query($count_query)->fetchColumn();

            $numentry = ($page - 1) * $MAX_PAGE_ENTRIES;
            if ($numentry + $MAX_PAGE_ENTRIES < $posts) {
                $pnext = "?g=" . $id_grp . "&p=" . ($page + 1);
            }
            if ($page > 1) {
                $pprev = "?g=" . $id_grp . "&p=" . ($page - 1);
            }

            show_page_control('down', $page, ceil($posts / $MAX_PAGE_ENTRIES), $pprev, $pnext, 0, $id_grp);

            $view_query = "$base_query GROUP BY id_topic ORDER BY ForumPosts.time DESC LIMIT $numentry,$MAX_PAGE_ENTRIES;";
        } else {
            if ($show_mylist) {
                $base_query = "$base_query AND ForumUsers.id = ForumTopics.id_user AND ForumTopics.id IN (SELECT id_like FROM ForumUserLike WHERE id_user = \"$id_user\")";
                $count_query = "$count_query AND ForumTopics.id IN (SELECT id_like FROM ForumUserLike WHERE id_user = \"$id_user\")";
            } else if ($show_mytopics) {
                $base_query = "$base_query AND ForumUsers.id = ForumTopics.id_user AND ForumTopics.id_user = $id_user";
                $count_query = "$count_query AND ForumTopics.id_user = $id_user";
            } else {
                $base_query = "$base_query  AND ForumUsers.id = ForumTopics.id_user";
            }

            $ban_opts = "";
            $ban_opts_count = "";
            if (is_logged()) {
                $banlist = $database->query("SELECT id_session FROM ForumBlackLists WHERE id_user={$_SESSION['myuser_id']}");
            } else if (is_session('banlist')) {
                $banlist = $_SESSION['banlist'];
            }
            if (isset($banlist)) {
                foreach ($banlist as $ban_id_session) {
                    if (is_array($ban_id_session)) {
                        $ban_id_session = $ban_id_session['id_session'];
                    }
                    $ban_opts = "$ban_opts AND ForumTopics.id_session!='$ban_id_session' AND ForumPosts.id_session!='$ban_id_session'";
                    $ban_opts_count = "$ban_opts_count AND ForumTopics.id_session!='$ban_id_session'";
                }

                $base_query = "$base_query $ban_opts";
                $count_query = "$count_query $ban_opts_count";
            }

            // echo "<!-- 2count_query $count_query -->";

            $posts = $database->query($count_query)->fetchColumn();

            $pprefix = '?';
            if ($show_hot) {
                $pprefix = '?s=1&';
            } else if ($show_mylist) {
                $pprefix = '?m=1&';
            } else if ($show_mytopics) {
                $pprefix = '?o=1&';
            }

            $numentry = ($page - 1) * $MAX_PAGE_ENTRIES;
            if ($numentry + $MAX_PAGE_ENTRIES < $posts) {
                $pnext = $pprefix . 'p=' . ($page + 1);
            }
            if ($page > 1) {
                $pprev = $pprefix . 'p=' . ($page - 1);
            }

            show_page_control('down', $page, ceil($posts / $MAX_PAGE_ENTRIES), $pprev, $pnext);

            if ($show_mylist != 0) {
                $view_query = "$base_query GROUP BY id_topic ORDER BY ForumPosts.time DESC LIMIT $numentry,$MAX_PAGE_ENTRIES;";
            } else if ($show_mytopics) {
                $view_query = "$base_query GROUP BY id_topic ORDER BY ForumPosts.time DESC LIMIT $numentry,$MAX_PAGE_ENTRIES;";
            } elseif ($show_hot == 0) {
                $view_query = "$base_query GROUP BY id_topic $having_query ORDER BY ForumPosts.time DESC LIMIT $numentry,$MAX_PAGE_ENTRIES;";
            } else {
                $view_query = "$base_query GROUP BY id_topic $having_query ORDER BY posts DESC LIMIT $numentry,$MAX_PAGE_ENTRIES;";
            }
        }
?>
<table class="themes">
<?php
//echo "<!-- 2view_query $view_query -->";

	    foreach ($database->query($view_query) as $row) {
            $timestamp = date('H:i d/m', $row['time']);

            $name = format_user_nick($row['nick'], $row['id_user'], $row['login'], $row['id']);

    		if ($id_user === $row['id_user']) {
    		    $topic_owner = 'owner';
    		} else {
    		    $topic_owner = '';
    		}
?>
	<tr>
		<td class="tdw1 <?php echo $topic_owner; ?>"><?php echo $timestamp; ?></td>
		<td class="tdw3 <?php echo $topic_owner; ?>">
			<a href="?topic=<?php echo $row['id_topic']; ?>" title="<?php echo $row['grp']; ?>"><?php echo $row['topic']; ?></a>
			&nbsp;[<?php echo $row['view']; ?>/<?php echo $row['posts']; ?> - <?php echo $row['last_nick']; ?>]
			<div class="topic_control_panel">
<?php
            if (is_forum_admin()) {
                $rmargs = "";
                if ($id_grp != 0) {
                    $rmargs = 'g=' . $id_grp . '&p=' . $page . '&dt=' . $row['id_topic'];
                } else if ($show_hot != 0) {
                    $rmargs = 's=1&dt=' . $row['id_topic'];
                } else {
                    $rmargs = 'dt=' . $row['id_topic'];
                }

                if (!isset($FORUM_TRASH_GID) || (isset($FORUM_TRASH_GID) && $id_grp == $FORUM_TRASH_GID)) {
?>
				<a href="?<?php echo $rmargs; ?>" class="remove">Удалить</a>
<?php
                }

                if (!isset($FORUM_TRASH_GID) || $id_grp != $FORUM_TRASH_GID) {
?>
				<a href="?<?php echo $rmargs; ?>&trash=1" class="remove">Мусор</a>
<?php
                    if (is_hardcore_on() == 0) {
                        if (isset($FORUM_PURGATORIUM_GID) && $id_grp == $FORUM_PURGATORIUM_GID) {
?>
				<a href="<?php echo $_SERVER['REQUEST_URI']; ?>&public=<?php echo $row['id_topic']; ?>" class="remove">Показать</a>
<?php
                        } else {
?>
				<a href="<?php echo get_href(); ?>purgatory=<?php echo $row['id_topic']; ?>" class="remove">Скрыть</a>
<?php
                        }
                    }
                }
            }
		
            if ($id_user === $row['id_user']) {
                if ($show_mytopics) {
?>
				<a href="" onclick="load_modal('topicsettings.php/?id_topic=<?php echo $row['id_topic']; ?>'); return false;" class="remove">
					<svg viewBox="0 0 20 20" width="16px" class="svg_button">
						<title>Настройка доступа к теме</title>
						<path d="M3.94 6.5L2.22 3.64l1.42-1.42L6.5 3.94c.52-.3 1.1-.54 1.7-.7L9 0h2l.8 3.24c.6.16 1.18.4 1.7.7l2.86-1.72 1.42 1.42-1.72 2.86c.3.52.54 1.1.7 1.7L20 9v2l-3.24.8c-.16.6-.4 1.18-.7 1.7l1.72 2.86-1.42 1.42-2.86-1.72c-.52.3-1.1.54-1.7.7L11 20H9l-.8-3.24c-.6-.16-1.18-.4-1.7-.7l-2.86 1.72-1.42-1.42 1.72-2.86c-.3-.52-.54-1.1-.7-1.7L0 11V9l3.24-.8c.16-.6.4-1.18.7-1.7zM10 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
					</svg>
				</a>
		        <a href="" onclick="load_modal('topicsettings.php/?id_topic=<?php echo $row['id_topic'];?>&show_users=1'); return false;" class="remove">
		        	<svg viewBox="0 0 20 20" width="16px" class="svg_button">
						<title>Участники темы</title>
						<path d="M7 8a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0 1c2.15 0 4.2.4 6.1 1.09L12 16h-1.25L10 20H4l-.75-4H2L.9 10.09A17.93 17.93 0 0 1 7 9zm8.31.17c1.32.18 2.59.48 3.8.92L18 16h-1.25L16 20h-3.96l.37-2h1.25l1.65-8.83zM13 0a4 4 0 1 1-1.33 7.76 5.96 5.96 0 0 0 0-7.52C12.1.1 12.53 0 13 0z"/>
					</svg>
				</a>
<?php
                }
            }
?>
			</div>
		</td>
		<td class="tdw2 <?php echo $topic_owner; ?>"><?php echo $name; ?></td>
	</tr>
<?php
        }
?>
</table>
<?php
	    show_page_control('up', $page, ceil($posts / $MAX_PAGE_ENTRIES), $pprev, $pnext, 0, $id_grp);
	} else if ($topic_private && $topic_private_access == 0) {
?>
<div class="block2">
	<h1>Закрытая тема</h1>
	Для доступа необходимо разрешение создателя темы.<br>
<?php
	    if ($id_user == 0) {
?>
	Чтобы запросить разрешение, скопируйте ссылку 
	<a href="invite://:<?php echo $id_session; ?>@<?php echo $id_topic; ?>" onclick="copyStringToClipboard('invite://:<?php echo $id_session; ?>@<?php echo $id_topic; ?>'); popup_copy('pop<?php echo $id_topic; ?>'); return false;">
		invite://:<?php echo $id_session; ?>@<?php echo $id_topic; ?>
	</a>
	<span class="popup">
		<span class="popuptext" id="pop<?php echo $id_topic; ?>"></span>
	</span>
	(нажмите на ссылку для копирования) и попросите зарегистрированного пользователя отправить ее в личное сообщение пользователю
	<?php echo format_user_nick($topic_owner_login, $id_topic_owner, $topic_owner_login, $id_topic_owner); ?>
	<br>Если ваш запрос будет одобрен, то вы сможете писать анонимно с текущей сессии используемого в данный момент браузера.
	Зарегистрированные пользователи не имеют подобных ограничений.
 <?php
	    } else {
?>
	Чтобы запросить разрешение, скопируйте ссылку 
	<a href="invite://<?php echo $id_user; ?>@<?php echo $id_topic; ?>" onclick="copyStringToClipboard('invite://<?php echo $id_user; ?>@<?php echo $id_topic; ?>'); popup_copy('pop<?php echo $id_topic; ?>'); return false;">
		invite://<?php echo $id_user; ?>@<?php echo $id_topic; ?>
	</a>
	<span class="popup">
		<span class="popuptext" id="pop<?php echo $id_topic; ?>"></span>
	</span>
	(нажмите на ссылку для копирования) и отправьте ее в личное сообщение пользователю
	<?php echo format_user_nick($topic_owner_login, $id_topic_owner, $topic_owner_login, $id_topic_owner); ?>
	(<a href="#" onclick="load_modal('pagerchat.php/?new=<?php echo $id_topic_owner; ?>'); return false;">Написать сообщение</a>)
<?php
	    }
?>
	<br>
	<br>Внимание! Разрешать или запрещать доступ в тему - личное право создателя темы.
</div>
<?php
	} else {
	    $posts = 0;
	    $pnext = "";
	    $pprev = "";

	    $ban_opts = "";
	    if (is_logged()) {
	        $banlist = $database->query("SELECT id_session FROM ForumBlackLists WHERE id_user={$_SESSION['myuser_id']}");
	    } else if (is_session('banlist')) {
	        $banlist = $_SESSION['banlist'];
	    }
        if (isset($banlist)) {
            foreach ($banlist as $ban_id_session) {
                if (is_array($ban_id_session)) {
                    $ban_id_session = $ban_id_session['id_session'];
                }
                
                $ban_opts = "$ban_opts AND ForumPosts.id_session!='$ban_id_session'";
            }
        }

        if (is_hardcore_on() == 0) {
            $query_hidden_opts = 'hidden = 0 AND';
        } else {
            $query_hidden_opts = '';
        }
        
//echo "<!-- ban_opts $ban_opts -->";
        
	    $posts = $database->query("SELECT COUNT(*) FROM ForumPosts WHERE $query_hidden_opts id_topic = $id_topic $ban_opts")->fetchColumn();

	    $first_posts = 0;
	    $post_id_req = "";
        if (is_defined('post') > 0) {
            $post_id_req = $_REQUEST['post'];
            if (is_numeric($post_id_req)) {
                $first_posts = $database->query("SELECT COUNT(*) FROM ForumPosts WHERE $query_hidden_opts id_topic = $id_topic AND id >= $post_id_req $ban_opts")->fetchColumn();

                if ($first_posts != "") {
                    $first_posts = $first_posts - 1;
                    $page = intdiv($first_posts, $MAX_PAGE_ENTRIES) + 1;
                    $page_offset = $first_posts % $MAX_PAGE_ENTRIES;
                    // $numentry = ($page - 1) * $MAX_PAGE_ENTRIES + $page_offset;
                    // echo '<!-- x post id req '.$post_id_req.' first posts '.$first_posts.' '.$page.' '.$page_offset.' -->';
                }
            } else {
                $post_id_req = "";
            }
        }

	    $numentry = ($page - 1) * $MAX_PAGE_ENTRIES;
        if ($numentry + $MAX_PAGE_ENTRIES < $posts) {
            $pnext = "?topic=" . $id_topic . "&p=" . ($page + 1);
        }
        if ($page > 1) {
            $pprev = "?topic=" . $id_topic . "&p=" . ($page - 1);
        }
        
	    show_page_control('down', $page, ceil($posts / $MAX_PAGE_ENTRIES), $pprev, $pnext, $id_topic);

	    $view_query = "UPDATE ForumTopics SET view = view + 1 WHERE id = $id_topic;";
	    $database->exec($view_query);

	    if (is_hardcore_on() == 0) {
	        $query_hidden_opts = 'AND ForumPosts.hidden = 0';
	    }
	    
	    $view_query =	"SELECT ForumPosts.id AS id_post, ForumUsers.login AS login, ForumUsers.id AS id,".
				" ForumPosts.time AS time, ForumPosts.nick AS nick, ForumPosts.id_user AS id_user,".
				" ForumPosts.subj AS subj, ForumPosts.post AS post, ForumTopics.topic AS topic,".
				" ForumPosts.id_session AS id_session, ForumPosts.attachment AS attachment".
				" FROM ForumPosts, ForumTopics, ForumUsers".
				" WHERE ForumPosts.id_topic = ForumTopics.id AND ForumTopics.id = $id_topic".
				" AND ForumUsers.id = ForumPosts.id_user $query_hidden_opts $ban_opts".
				" ORDER BY ForumPosts.time DESC LIMIT $numentry,$MAX_PAGE_ENTRIES;";

//echo "<!-- view_opt $view_query -->";

	    foreach ($database->query($view_query) as $row) {
?>
<div <?php echo ($row['id_post'] == $post_id_req)? 'class="shared_post"' : ''; ?>>
<div class="text_box_1"><a id="post<?php echo $row['id_post']; ?>"></a>
	<div class="box_user">
<?php
            $timestamp = date('d.m.Y (H:i)', $row['time']);
            $name = format_user_nick($row['nick'], $row['id_user'], $row['login'], $row['id']);

            if ($id_topic_owner != 0 && $id_topic_owner == $id_user && $id_topic_owner != $row['id_user']) {

                $is_readonly = $database->query("SELECT readonly FROM ForumTopicUsers WHERE id_topic=$id_topic AND id_user={$row['id_user']} AND (id_session='' OR id_session='{$row['id_session']}')")->fetchColumn();
                if ($topic_private && $is_readonly == null) {
                    $class_strike = 'strike';
                    $name_ban = '';
                } else if ($topic_private == 0 && $is_readonly == 1) {
                    $class_strike = 'strike';
                    $name_ban = '';
                } else {
                    $class_strike = '';

                    if ($topic_private) {
                        $name_ban_title = 'Закрыть тему для пользователя';
                    } else {
                        $name_ban_title = 'Запретить пользователю писать в теме';
                    }
                    $name_ban = '<a href="" onclick="return banSubmit(' . $row['id_post'] . ')" class="remove_left"><svg viewBox="0 0 20 20" width="16px" class="svg_button">
<title>' . $name_ban_title . '</title>
<path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm1.41-1.41A8 8 0 1 0 15.66 4.34 8 8 0 0 0 4.34 15.66zm9.9-8.49L11.41 10l2.83 2.83-1.41 1.41L10 11.41l-2.83 2.83-1.41-1.41L8.59 10 5.76 7.17l1.41-1.41L10 8.59l2.83-2.83 1.41 1.41z"/>
</svg></a>';
                }
            } else {
                $class_strike = '';
                $name_ban = '';
            }

            $tmp_post = $row['post'];
            if (mb_strlen($tmp_post) > 16384) {
                $tmp_post = mb_substr($tmp_post, 0, 16383);
            }

            $post_id_session = "";
            if ($row['id_session']) {
                $post_id_session = $row['id_session'];
            }

            $post = linkify(convert_tiktok2(convert_tiktok(convert_vkv(convert_youtube(convert_google_form2(convert_google_form($tmp_post)))))), array(
                "http",
                "https"
            ), array(
                "target" => "_blank"
            ));

            $post = remove_iframes($post);
            ?>
		<span class="ban_left"><?php echo $timestamp; ?>&nbsp;|&nbsp;
			<span class="name1 <?php echo $class_strike; ?>"><?php echo $name; ?></span>
		</span>
		<?php echo $name_ban; ?>&nbsp;-&gt;&nbsp;
		<span class="white1"><?php echo $row['subj']; ?></span>
<?php
            if ($id_session != $post_id_session) {
?>
		<a class="ban" href="<?php echo $_SERVER['REQUEST_URI']; ?>&ban=<?php echo $post_id_session; ?>">
			<svg viewBox="0 0 20 20" width="16px" class="svg_button">
				<title>Скрыть пользователя и его темы</title>
				<path d="M12.81 4.36l-1.77 1.78a4 4 0 0 0-4.9 4.9l-2.76 2.75C2.06 12.79.96 11.49.2 10a11 11 0 0 1 12.6-5.64zm3.8 1.85c1.33 1 2.43 2.3 3.2 3.79a11 11 0 0 1-12.62 5.64l1.77-1.78a4 4 0 0 0 4.9-4.9l2.76-2.75zm-.25-3.99l1.42 1.42L3.64 17.78l-1.42-1.42L16.36 2.22z"/>
			</svg>
		</a>
<?php
            }

            if ($id_topic_owner != 0 && $id_topic_owner == $id_user && $id_topic_owner != $row['id_user'] && is_hardcore_on() == 0) {
?>
		<a class="ban" href="<?php echo $_SERVER['REQUEST_URI']; ?>&hide=<?php echo $row['id_post']; ?>">
			<svg viewBox="0 0 20 20" width="16px" class="svg_button">
				<title>Удалить пост</title>
				<path d="M6 2l2-2h4l2 2h4v2H2V2h4zM3 6h14l-1 14H4L3 6zm5 2v10h1V8H8zm3 0v10h1V8h-1z"/>
			</svg>
		</a>
<?php
            }

		    if ($id_session == $post_id_session && time() - $row['time'] < 60 * 60 * 1) {
?>
		<a class="ban" href="" onclick="post('<?php echo $_SERVER['REQUEST_URI']; ?>',{'editpost':<?php echo $row['id_post']; ?>}); return false;">
			<svg viewBox="0 0 20 20" width="16px" class="svg_button">
				<title>Редактировать сообщение</title>
				<path d="M2 4v14h14v-6l2-2v10H0V2h10L8 4H2zm10.3-.3l4 4L8 16H4v-4l8.3-8.3zm1.4-1.4L16 0l4 4-2.3 2.3-4-4z"/>
			</svg>
		</a>
<?php
		    }

            if (is_forum_admin()) {
?>
		<a href="?topic=<?php echo $id_topic; ?>&dp=<?php echo $row['id_post']; ?>" class="remove">Удалить</a>
		<a href="<?php echo $_SERVER['REQUEST_URI']; ?>&sdel=<?php echo $post_id_session; ?>" class="remove">Удалить сессию</a>
		<a href="<?php echo $_SERVER['REQUEST_URI']; ?>&shide=<?php echo $post_id_session; ?>&time=<?php echo $row['time']; ?>&period=30" class="remove">Скрыть за последние 30 мин.</a>
		<a href="<?php echo $_SERVER['REQUEST_URI']; ?>&shideall=<?php echo $post_id_session; ?>&time=<?php echo $row['time']; ?>&period=30" class="remove">Скрыть все за последние 30 мин.</a>
<?php
            }
?>
	</div>
</div>
<div class="text_box_2">
	<div id="message_<?php echo $row['id_post']; ?>" class="text_box_2_mess"><?php
            //$attachment = $row['attachment'];
            $sth = $database->prepare("SELECT attachment, censor FROM ForumPostAttachment WHERE id_post={$row['id_post']}");
            $sth->execute();
            $post_data = $sth->fetch();
            if ($post_data !== FALSE) {
                $attachment = $post_data['attachment'];
                $censor = $post_data['censor'];

                if (isset($attachment)) {
                    $media_url=$UPLOAD_DIR;
                    if (!file_exists($UPLOAD_DIR.'/'.$attachment)) {
                        $media_url="https://video.vtomske.net/uploads";
                    }
                    $image_ext = substr(strrchr($attachment, '.'), 1);
                    if ($image_ext == 'jpg'  || $image_ext == 'jpeg'  || $image_ext == 'gif' || $image_ext == 'png' ||
                        $image_ext == 'webp') {
?><a href="<?php echo $media_url.'/'.$attachment; ?>" class="highslide" onclick="return hs.expand(this)"><div class="imagebox"><img src="<?php echo $media_url.'/small-'.$attachment; ?>" alt="" class="postimage<?php echo ($censor < 0 && !is_hardcore_on())?' censored':''; ?>"/><?php
    if (is_forum_admin()) {
	if ($censor < 0) {
	    ?><span onclick="window.location='<?php echo $_SERVER['REQUEST_URI']; ?>&image_censor=0&image_name=<?php echo $attachment; ?>'; return false;">Показать</span><?php
	} else {
	    ?><span onclick="window.location='<?php echo $_SERVER['REQUEST_URI']; ?>&image_censor=1&image_name=<?php echo $attachment; ?>'; return false;">Скрыть</span><?php
	}
    }
?></div></a><?php
                    } else if ($image_ext == 'oga' || $image_ext == 'mp4a' || $image_ext == 'm4a') {
?><audio class="postvideo" controls><source src="<?php echo $media_url.'/'.$attachment; ?>"></audio><?php
                    } else {
?><div class="videobox"><video class="postvideo<?php echo ($censor < 0 && !is_hardcore_on())?' censored':''; ?>" controls><?php
                        if ($image_ext == 'mp4' || $image_ext == 'mpg4' || $image_ext == 'mpeg4') {
?><source src="<?php echo $media_url.'/'.$attachment; ?>" type="video/mp4"><?php
                        } else if ($image_ext == 'ogv') {
?><source src="<?php echo $media_url.'/'.$attachment; ?>" type="video/ogg"><?php
                        } else if ($image_ext == 'webm') {
?><source src="<?php echo $media_url.'/'.$attachment; ?>" type="video/webm"><?php
                        }
?>Your browser does not support the video tag.</video><?php
    if (is_forum_admin()) {
	if ($censor < 0) {
	    ?><a href="#" class="videolink" onclick="window.location='<?php echo $_SERVER['REQUEST_URI']; ?>&image_censor=0&image_name=<?php echo $attachment; ?>'; return false;">Показать</a><?php
	} else {
	    ?><a href="#" class="videolink" onclick="window.location='<?php echo $_SERVER['REQUEST_URI']; ?>&image_censor=1&image_name=<?php echo $attachment; ?>'; return false;">Скрыть</a><?php
	}
    }?></div><?php
                    }
                }
            }
            echo $post;
?></div>
	<div class="answer_bar">
		<a href="#" onclick="reply('<?php echo $row['nick']; ?> (<?php echo $timestamp; ?>)', 'message_<?php echo $row['id_post']; ?>');" class="reply">
			<svg viewBox="0 0 20 20" width="16px" class="svg_button">
				<title>Ответить</title>
				<path d="M 15,3 V 5.99 A 4,4 0 0 1 11,10 H 8 V 5 l -6,6 6,6 v -5 h 3 A 6,6 0 0 0 17,6 V 3 Z"/>
			</svg>
		</a>
		<a href="#" onclick="reply_cite('<?php echo $row['nick']; ?> (<?php echo $timestamp; ?>)', 'message_<?php echo $row['id_post']; ?>');" class="reply">
			<svg viewBox="0 0 20 20" width="16px" class="svg_button">
				<title>Цитировать</title>
				<path d="m 12,6 h 3 V 5.99 C 15.0055,8.2030432 13.21305,10.000007 11,10 H 8 V 5 l -6,6 6,6 v -5 h 3 c 3.313708,0 6,-2.6862915 6,-6 v 0 h 3 V 3 H 18 V 4 H 14 V 3 h -2 z"/>
			</svg>
		</a>
		<a href="/?topic=<?php echo $id_topic; ?>&post=<?php echo $row['id_post']; ?>#post<?php echo $row['id_post']; ?>"
			onclick="copyStringToClipboard('https://<?php echo $_SERVER['HTTP_HOST'].'/?topic='.$id_topic.'&post='.$row['id_post'].'#post'.$row['id_post']; ?>'); popup_copy('pop<?php echo $row['id_post']; ?>'); return false;" class="reply">
			<svg viewBox="0 0 20 20" width="16px" class="svg_button">
				<title>Ссылка на это сообщение</title>
				<path d="M11 12h6v-1l-3-1V2l3-1V0H3v1l3 1v8l-3 1v1h6v7l1 1 1-1v-7z"/>
			</svg>
		</a>
		<span class="popup">
			<span class="popuptext" id="pop<?php echo $row['id_post']; ?>"></span>
		</span>
	</div>
</div>
</div>
<?php
        }
        show_page_control('up', $page, ceil($posts / $MAX_PAGE_ENTRIES), $pprev, $pnext, $id_topic);
    }

	show_menu($database);

	$posts = 0;
	$topics = 0;
	$users = 0;

	if (is_hardcore_on()) {
	    $posts  = $database->query("SELECT COUNT(*) FROM ForumPosts")->fetchColumn();
	} else {
	   $posts  = $database->query("SELECT COUNT(*) FROM ForumPosts WHERE hidden = 0")->fetchColumn();
	}
	$topics = $database->query("SELECT COUNT(*) FROM ForumTopics")->fetchColumn();
	$users  = $database->query("SELECT COUNT(*) FROM ForumUsers")->fetchColumn();
	$users = $users - 1; // minus super Anonymous
?>
<div class="line1"></div>
<div class="block2">
	<div class="copy">Клонировано <a title="Made in Tomsk" href="https://github.com/antimozga/ftr" target="_blank">AntiMozga</a></div>
	Тем: <?php echo $topics; ?>
	&nbsp;|&nbsp; Сообщений: <?php echo $posts; ?>
	&nbsp;|&nbsp; Пользователей: <?php echo $users; ?>
	&nbsp;|&nbsp; Хардкор <span class="switch"><?php echo (is_session('hardcore') && $_SESSION['hardcore'] == 1)?'<a style="color: green;" href="'.get_href().'hardcore=0">&#x2714;</a>':'<a href="'.get_href().'hardcore=1">&#x2718;</a>'; ?></span>
</div>
<?php
	show_footer();

    unset($database);
}
?>
