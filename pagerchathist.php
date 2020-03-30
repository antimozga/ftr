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
    if (is_defined('to')) {
	$to_id = $_REQUEST['to'];
	$to_id = ($to_id * 10) / 10;

	$to_user = get_user($database, $to_id);

	$user_query = "SELECT id_user, id_from_user, ForumPager.new AS new, "
	    ."ForumPager.time AS time, ForumPager.subj AS subj, "
	    ."ForumPager.post AS post, ForumPager.encrypted AS encrypted "
	    ."FROM ForumUsers, ForumPager WHERE ForumUsers.id = $to_id AND "
	    ."((ForumPager.id_from_user = $to_id AND ForumPager.id_user = $myuser_id) OR "
	    ." (ForumPager.id_from_user = $myuser_id AND ForumPager.id_user = $to_id)) ORDER BY ForumPager.time DESC;";
	foreach ($database->query($user_query) as $row) {
	    $user = get_user($database, $row['id_from_user']);
	    $n = "";
	    if ($row['id_from_user'] == $to_id && $row['new'] == 1) {
		$n = " (Новое)";
	    }

	    $login = $user['login'];

	    $encrypted = $row['encrypted'];
	    if (!is_numeric($encrypted)) {
		$encrypted = 0;
	    }

	    if ($encrypted != 0) {
		$post = stripslashes($row['post']);
		$login = $login.'&#x1f512;';
	    } else {
		$post = $row['post'];
	    }



	    echo '
<div class="dialog_box_text">
 <div class="text_box_1_dialog">
  <div class="box_user">
    <span class="name">'.$login.'</span> -&gt; <span>'.date('d.m.Y H:i', $row['time']).$n.'</span>
  </div>
 </div>
 <div class="text_box_2_dialog" encrypted="'.$encrypted.'">
'.$post.'
 </div>
</div>';
	}
	$user_query = "UPDATE ForumPager SET new = 0 WHERE new = 1 AND "
	    ."(id_from_user = $to_id AND id_user = $myuser_id);";
	$database->exec($user_query);
    }
} else {
    echo '<p>Доступ запрещен.</p>';
}
?>
