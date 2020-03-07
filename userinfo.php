<?php

require_once('config.php');

session_start();

include('funcs.php');

function show_user_info($name, $email, $fio, $gender, $description, $last_login, $id_to)
{
    $g = array(1 => 'Не имеет значения', 2 => 'Мужской', 3 => 'Женский', 4 => 'Средний');
    $b = array(1 => 'Был', 2 => 'Был', 3 => 'Была', 4 => 'Было');
    echo ' <div class="pasport1">';

    global $UPLOAD_DIR;

    $avatar = $UPLOAD_DIR."/small-id".$id_to.".jpg";
    if (file_exists($avatar)) {
	echo '<img src="'.$avatar.'" class="user_info_img" alt="">
<div class="clear"></div>
';
    }

    echo '<div class="pasport_info_box">
<span>На форуме:</span>
'.$name.'
</div>
<div class="line3"></div>
<div class="pasport_info_box">
<span>В реале:</span>
'.$fio.'
</div>
<div class="line3"></div>
<div class="pasport_info_box">
<span>Пол:</span>
'.$g[$gender].'
</div>
<div class="line3"></div>
<div class="pasport_info_box">
<span>Почта:</span>
<a href="mailto:'.$email.'">'.$email.'</a>
</div>
<div class="line3"></div>
<div class="pasport_info_box">
<span>
'.$b[$gender].' на форуме:</span>
'.$last_login.'
</div>';
    if (isset($_SESSION['myuser_id'])) {
	echo '<div class="pasport_info_box_write">
<a href="#" onclick="load_modal(\'pagerchat.php/?new='.$id_to.'\'); return false;">Написать сообщение</a>
</div>
';
    }
echo '</div>
<div class="pasport2">
<div class="pasport_text_full">
'.$description.'
</div>
</div>
';
}

$database = new PDO("sqlite:" . DBASEFILE);
if (!$database) {
    print("<b>Ошибка базы данных.</b>");
} else {
    if (is_defined('id')) {
	$id = $_REQUEST['id'];
	$id = ($id * 10) / 10;
	$user_query = "SELECT login, email, fio, gender, description, last_login FROM ForumUsers WHERE id = $id;";
	foreach ($database->query($user_query) as $row) {
	    $name = $row['login'];
	    $email = $row['email'];
	    $fio = $row['fio'];
	    $gender = $row['gender'];
	    $description = $row['description'];
	    $last_login = $row['last_login'];
	}

	$email = 'скрыт от спамеров';

	if ($name != "") {
	    if ($last_login == 0) {
		$b = array(1 => 'Не был', 2 => 'Не был', 3 => 'Не была', 4 => 'Не было');
		$last_login = $b[$gender];
	    } else {
		$last_login = date('d.m.Y H:i', $last_login);
	    }
	    show_user_info($name, $email, $fio, $gender, $description, $last_login, $id);
	}
    }

    unset($database);
}
?>
