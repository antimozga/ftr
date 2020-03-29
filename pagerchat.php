<?php

require_once('config.php');

session_start();

include('funcs.php');

function get_user($database, $id) {
    $query = "SELECT login, last_login FROM ForumUsers WHERE id = $id;";
    foreach ($database->query($query) as $row) {
	$login = $row['login'];
	$last_login = $row['last_login'];
    }
    return array("login" => $login, "time" => $last_login);
}

$database = new PDO("sqlite:" . DBASEFILE);
if (!$database) {
    echo '<p>Ошибка базы данных.</p>';
} else if (check_login()) {
    $myuser_id = $_SESSION['myuser_id'];
    if (is_defined('new')) {
	$to_id = $_REQUEST['new'];
	$to_id = ($to_id * 10) / 10;

	if (is_defined("event")) {
	    $cmd = $_REQUEST["event"];
	    if ($cmd == "forumpagercreatemess") {
		$tim = time();
		$post = convert_string($_REQUEST["pagermess"]["content"]);
		if ($post != "") {
		    $user_query = "INSERT INTO ForumPager (id_user, id_from_user, new, time, post) ".
				  "VALUES($to_id, $myuser_id, 1, $tim, '$post');";
		    $database->exec($user_query);
		}
		exit;
	    }
	}

	$to_user = get_user($database, $to_id);

echo '<div class="modal-content-window pagerchat_window">
    <div class="head">
    <div class="user_info">';

    $avatar = $UPLOAD_DIR."/small-id".$to_id.".jpg";
    if (file_exists($avatar)) {
	echo '<img src="'.$avatar.'" class="user_info_img" alt="">';
    }

echo '<h3>'.format_user_nick($to_user['login'], $to_id, $to_user['login'], $to_id).'</h3>
        <span class="user_info_date">был на FTR: '.date('d.m.Y H:i', $to_user['time']).'</span>
    </div>
    <div class="dialog_brn_box"><div>

    </div></div>
</div>';

echo '
<div class="dialog_answer_box">
    <form action="pagerchat.php/?new='.$to_id.'" method="post" class="form_dialog" onsubmit="pager_post_submit(event, this)">
    <input type="hidden" name="event" value="forumpagercreatemess"/>
    <textarea maxlength="4096" class="area_dialog_text" name="pagermess[content]" id="dialog_mess" autofocus></textarea>
    <input type="submit" class="btn_dialog" value="Отправить">
    </form>
</div>
';

echo '<div class="autorefresh refreshnow" src="pagerchathist.php/?to='.$to_id.'"></div>';
echo '</div>';
    }
} else {
    echo '<p>Доступ запрещен.</p>';
}
?>
