<?php
require_once ('config.php');

session_start();

include ('funcs.php');

$database = new PDO("sqlite:" . DBASEFILE);
if (! $database) {
    echo '<p>Ошибка базы данных.</p>';
} else {
    check_login();

    if (is_defined('unban')) {
        $session_id = addslashes($_REQUEST['unban']);
        if (is_logged()) {
            $database->exec("DELETE FROM ForumBlackLists WHERE id_user={$_SESSION['myuser_id']} AND id_session='$session_id'");
            
            $_SESSION['reloadpage'] = 1;
        } else if (is_session('banlist')) {
            $arr = $_SESSION['banlist'];
            $pos = array_search($session_id, $arr);
            unset($arr[$pos]);
            $arr = array_values($arr);
            $_SESSION['banlist'] = $arr;

            $_SESSION['reloadpage'] = 1;
        }
    }

    if (is_session('reloadpage')) {
        ?>
<span id="modal-content-args" reloadPageOnClose></span>
<?php
    }

    $banned = 0;
    ?>
<div class="modal-content-window banlist_window">
	<table class="userstable">
    
<?php
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
            $bannick = "";
            foreach ($database->query("SELECT DISTINCT nick, id_user FROM ForumPosts WHERE id_session = '$ban_id_session'") as $row) {
                if ($row['id_user'] != 0) {
                    $bannick = format_user_nick($row['nick'], $row['id_user'], $row['nick'], $row['id_user']) . " $bannick";
                } else {
                    $bannick = $row['nick'] . " $bannick";
                }
                $banned ++;
            }
            if ($bannick == "") {
                $bannick = "Пользователь удален";
            }
            ?>
		<tr>
			<td><?php echo $bannick; ?></td>
			<td class="tdu1">
				<a href="#"	onclick="update_modal('showbanlist.php?unban=<?php echo $ban_id_session; ?>'); return false;">
					<svg viewBox="0 0 20 20" width="16px" class="svg_button">
						<title>Показать пользователя и его темы</title>
						<path d="M.2 10a11 11 0 0 1 19.6 0A11 11 0 0 1 .2 10zm9.8 4a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm0-2a2 2 0 1 1 0-4 2 2 0 0 1 0 4z" />
		 			</svg>
				</a>
			</td>
		</tr>
<?php
        }
    }

    if ($banned == 0) {
        ?>
		<tr>
			<td colspan="2">Пока тут никого нет...</td>
		</tr>
<?php
    }
    ?>
	</table>
</div>
<?php
}
?>
