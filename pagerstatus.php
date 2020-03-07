<?php

require_once('config.php');

session_start();

include('funcs.php');

$database = new PDO("sqlite:" . DBASEFILE);
if (!$database) {
    echo '<p>Ошибка базы данных.</p>';
} else {
    if (is_logged()) {
	$pt = $database->query('SELECT COUNT(*) FROM ForumPager WHERE id_user = '.$_SESSION['myuser_id'].';')->fetchColumn();
	$pn = $database->query('SELECT COUNT(*) FROM ForumPager WHERE id_user = '.$_SESSION['myuser_id'].' AND new = 1;')->fetchColumn();

	echo $pn.'&nbsp;|&nbsp;'.$pt;
    } else {
	echo '';
    }
}
?>
