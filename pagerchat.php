<?php
require_once ('config.php');

session_start();

include ('funcs.php');

function get_user($database, $id)
{
    $query = "SELECT login, last_login, pubkey FROM ForumUsers WHERE id = $id;";
    foreach ($database->query($query) as $row) {
        $login = $row['login'];
        $last_login = $row['last_login'];
        $pubkey = $row['pubkey'];
    }
    return array(
        "login" => $login,
        "time" => $last_login,
        "pubkey" => $pubkey
    );
}

$database = new PDO("sqlite:" . DBASEFILE);
if (! $database) {
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
                $encrypted = $_REQUEST["pagermess"]["encrypted"];
                $encrypted = ($encrypted * 10) / 10;
                if ($encrypted != 0) {
                    $post = addslashes($_REQUEST["pagermess"]["content"]);
                } else {
                    $post = convert_text($_REQUEST["pagermess"]["content"]);
                }
                if ($post != "") {
                    $user_query = "INSERT INTO ForumPager (id_user, id_from_user, new, time, post, encrypted) " . "VALUES($to_id, $myuser_id, 1, $tim, '$post', $encrypted);";
                    $database->exec($user_query);
                }
                exit();
            }
        }

        $to_user = get_user($database, $to_id);
        ?>
<div class="modal-content-window pagerchat_window"
	onscroll="pagerHistoryScroll(this)">
	<div class="head">
		<div class="user_info">
<?php
        $avatar = $UPLOAD_DIR . "/small-id" . $to_id . ".jpg";
        if (file_exists($avatar)) {
            echo '<img src="' . $avatar . '" class="user_info_img" alt="">';
        }

        // $encrypted_chat = '';
        $encrypt_send = '';

        if ($to_user['pubkey'] != '') {
            if ($_SESSION['myuser_pubkey'] != '') {
                // $encrypted_chat = '<svg viewBox="0 0 20 20" width="16px" class="svg_icon">
                // <title>Переписка защищена шифрованием</title>
                // <path d="M4 8V6a6 6 0 1 1 12 0v2h1a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-8c0-1.1.9-2 2-2h1zm5 6.73V17h2v-2.27a2 2 0 1 0-2 0zM7 6v2h6V6a3 3 0 0 0-6 0z"/>
                // </svg>';
                $encrypt_send = 'onclick="return pgpSendMessage();"';
            } else {
                // $encrypted_chat = '<a href="?reg=3#pager"><svg viewBox="0 0 20 20" width="16px" class="svg_button">
                // <title>Настройте ключ для защищенной переписки</title>
                // <path d="M4 8V6a6 6 0 1 1 12 0h-3v2h4a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-8c0-1.1.9-2 2-2h1zm5 6.73V17h2v-2.27a2 2 0 1 0-2 0zM7 6v2h6V6a3 3 0 0 0-6 0z"/>
                // </svg></a>';
            }
        }
        ?>
		<h3><?php echo format_user_nick($to_user['login'], $to_id, $to_user['login'], $to_id); ?><span
					id="encrypted_status"></span>
			</h3>
			<span class="user_info_date">был в сети: <?php echo date('d.m.Y H:i', $to_user['time']); ?></span>
		</div>
<?php
        if ($to_user['pubkey'] != '') {
            ?>
	<div class="sender_key_block">
			<label for="sender_key_show">Показать</label><input type="radio"
				id="sender_key_show" name="group"> <label for="sender_key_hide">Скрыть</label><input
				type="radio" id="sender_key_hide" name="group"> открытый ключ
			<textarea readonly id="pubkey2" class="sender_key"><?php echo $to_user['pubkey']; ?></textarea>
		</div>
		<textarea readonly id="active_pubkey" style="display: none;"><?php echo $_SESSION['myuser_pubkey']; ?></textarea>
		<textarea readonly id="current_pubkey" style="display: none;"></textarea>
<?php
        }
        ?>
	<div class="dialog_brn_box">
			<div></div>
		</div>
	</div>

	<div class="dialog_answer_box">
		<form action="pagerchat.php/?new=<?php echo $to_id; ?>" method="post"
			class="form_dialog" id="pager_message_form_default"
			onsubmit="return pager_post_submit(event, this);">
			<input type="hidden" name="event" value="forumpagercreatemess" />
			<textarea maxlength="4096" class="area_dialog_text"
				name="pagermess[content]" id="dialog_mess" autofocus></textarea>
			<input type="hidden" name="pagermess[encrypted]" value="0"> <input
				type="submit" class="btn_dialog" value="Отправить"
				<?php echo $encrypt_send; ?>>
		</form>
		<form action="pagerchat.php/?new=<?php echo $to_id; ?>" method="post"
			class="form_dialog" id="pager_message_form" style="display: none;">
			<input type="hidden" name="event" value="forumpagercreatemess" />
			<textarea maxlength="4096" class="area_dialog_text"
				name="pagermess[content]" id="dialog_mess2" autofocus></textarea>
			<input type="hidden" name="pagermess[encrypted]" value="1">
		</form>
	</div>

	<div class="refreshnow" src="pagerchat.js" preexec="pagerHistoryReset"
		exec="pagerHistoryLoad"></div>
	<div class="autorefresh refreshnow" exec="pagerHistoryUpdate"></div>

	<div id="pager_history"
		src="pagerchathist.php/?to=<?php echo $to_id; ?>&count=15"></div>
</div>

<?php
    }
} else {
    ?>
<p>Доступ запрещен.</p>
<?php
}
?>
