<?php

require_once('config.php');

session_start();

include('funcs.php');

$database = new PDO("sqlite:" . DBASEFILE);
if (!$database) {
    echo '<p>Ошибка базы данных.</p>';
} else {
    if (is_logged()) {
	echo '<div class="autorefresh refreshnow" src="showpager-refr.php"></div>';
    } else {
	echo '<p>Доступ запрещен.</p>';
    }
}
?>
