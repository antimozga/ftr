<?php

require_once('config.php');

session_start();

include('funcs.php');

$database = new PDO("sqlite:" . DBASEFILE);
if (!$database) {
    echo '<p>Ошибка базы данных.</p>';
} else {
    if (is_logged()) {
	    function make_href($href, $id, $name) {
		return '<a onclick="window.open(\'\',\'u\',\'scrollbars,width=620,height=350\');" target="u" href="'.$href.$id.'">'.$name.'</a>';
	    }
	    $pager_query = "SELECT COUNT(*) as total, ForumPager.id_from_user AS id_from_user, ForumUsers.login AS login FROM ForumPager,ForumUsers"
		." WHERE ForumPager.id_user = ".$_SESSION['myuser_id']." AND ForumUsers.id = ForumPager.id_from_user GROUP BY ForumPager.id_from_user;";
	    echo '<table class="userstable"><tr><th>&nbsp;</th><th colspan=2 align="left">Пользователь</th><th>Сообщений</th></tr>';
	    foreach ($database->query($pager_query) as $row) {
		$pn = $database->query('SELECT COUNT(*) FROM ForumPager WHERE id_user = '.$_SESSION['myuser_id'].' AND id_from_user = '.$row['id_from_user'].' AND new = 1;')->fetchColumn();
		echo '<tr><td></td><td>'.format_user_nick($row['login'], $row['id_from_user'], $row['login'], $row['id_from_user']).'</td><td>'.$pn.'</span>&nbsp;|&nbsp;'.$row['total'].'</td><td><a href="#" onclick="load_modal(\'pagerchat.php/?new='.$row['id_from_user'].'\',620,350); return false;">Читать</a></td></tr>';
	    }
	    echo '</table>';
    } else {
	echo '<p>Доступ запрещен.</p>';
    }
}
?>
