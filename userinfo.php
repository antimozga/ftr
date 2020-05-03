<?php
require_once ('config.php');

session_start();

include ('funcs.php');

function show_user_info($name, $email, $fio, $gender, $description, $last_login, $id_to)
{
    global $UPLOAD_DIR;
    $g = array(
        1 => 'Не имеет значения',
        2 => 'Мужской',
        3 => 'Женский',
        4 => 'Средний'
    );
    $b = array(
        1 => 'Был',
        2 => 'Был',
        3 => 'Была',
        4 => 'Было'
    );
    ?>
<div class="pasport1">
<?php

    $avatar = $UPLOAD_DIR . "/small-id" . $id_to;
    $img_ext = '';
    if (file_exists($avatar.'.jpg')) {
        $img_ext = '.jpg';
    } else if (file_exists($avatar.'.png')) {
        $img_ext = '.png';
    } else if (file_exists($avatar.'.gif')) {
        $img_ext = '.gif';
    }

    if ($img_ext != '') {
        ?>
	<img src="<?php echo $avatar.$img_ext; ?>" class="user_info_img" alt="">
	<div class="clear"></div>
<?php
    }
    ?>
	<div class="pasport_info_box">
		<span>На форуме:</span>
<?php echo $name; ?>
	</div>
	<div class="line3"></div>
	<div class="pasport_info_box">
		<span>В реале:</span>
<?php echo $fio; ?>
	</div>
	<div class="line3"></div>
	<div class="pasport_info_box">
		<span>Пол:</span>
<?php echo $g[$gender]; ?>
	</div>
	<div class="line3"></div>
	<div class="pasport_info_box">
		<span>Почта:</span> <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a>
	</div>
	<div class="line3"></div>
	<div class="pasport_info_box">
		<span>
<?php echo $b[$gender]; ?> на форуме:</span>
<?php echo $last_login; ?>
</div>
<?php
    if (isset($_SESSION['myuser_id'])) {
        ?>
	<div class="pasport_info_box_write">
		<a href="#"
			onclick="load_modal('pagerchat.php/?new=<?php echo $id_to; ?>'); return false;">Написать
			сообщение</a>
	</div>
<?php
    }
    ?>
</div>
<div class="pasport2">
	<div class="pasport_text_full">
<?php echo $description; ?>
	</div>
</div>
<?php
}

$database = new PDO("sqlite:" . DBASEFILE);
if (! $database) {
    ?>
<b>Ошибка базы данных.</b>
<?php
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
                $b = array(
                    1 => 'Не был',
                    2 => 'Не был',
                    3 => 'Не была',
                    4 => 'Не было'
                );
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
