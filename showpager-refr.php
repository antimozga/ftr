<?php

require_once('config.php');

session_start();

include('funcs.php');

$database = new PDO("sqlite:" . DBASEFILE);
if (!$database) {
    echo '<p>Ошибка базы данных.</p>';
} else {
    if (is_logged()) {
	echo '<div class="pagerchat_window">';
	    $pager_query = "SELECT COUNT(*) as total, ForumPager.id_from_user AS id_from_user, ForumUsers.login AS login FROM ForumPager,ForumUsers".
		" WHERE ForumPager.id_user = ".$_SESSION['myuser_id']." AND ForumUsers.id = ForumPager.id_from_user".
		" GROUP BY ForumPager.id_from_user;";
	    echo '<table class="pagertable"><tr><th>Пользователь</th><th colspan=2>Сообщений</th></tr>';
	    foreach ($database->query($pager_query) as $row) {
		$pn = $database->query('SELECT COUNT(*) FROM ForumPager WHERE id_user = '.$_SESSION['myuser_id'].' AND id_from_user = '.$row['id_from_user'].' AND new = 1;')->fetchColumn();
		echo '<tr><td>'.format_user_nick($row['login'], $row['id_from_user'], $row['login'], $row['id_from_user']).'</td><td class="td2">'.$pn.'</span>&nbsp;|&nbsp;'.$row['total'].'</td><td class="td3"><a href="#" onclick="load_modal(\'pagerchat.php/?new='.$row['id_from_user'].'\'); return false;">Показать</a></td></tr>';
	    }
	    echo '</table>';
	echo '</div>';
    } else {
	echo '<p>Доступ запрещен.</p>';
    }
}
?>
