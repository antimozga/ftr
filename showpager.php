<?php
require_once ('config.php');

session_start();

include ('funcs.php');

$database = new PDO("sqlite:" . DBASEFILE);
if (! $database) {
    echo '<p>Ошибка базы данных.</p>';
} else {
    if (check_login()) {
        ?>
<div
	class="modal-content-window pagerchat_window autorefresh refreshnow"
	src="showpager-refr.php"></div>
<?php
    } else {
        ?>
<p>Доступ запрещен.</p>
<?php
    }
}
?>
