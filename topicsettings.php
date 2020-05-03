<?php
require_once ('config.php');

session_start();

include ('funcs.php');

function exitstatus($msg)
{
    $myObj = [
        'status' => $msg
    ];

    echo json_encode($myObj);
    exit();
}

function getCheckboxVal($name1, $name2)
{
    if (isset($_POST[$name1][$name2]) && $_POST[$name1][$name2] == '1') {
        return 1;
    } else {
        return 0;
    }
}

$database = new PDO("sqlite:" . DBASEFILE);
if (! $database) {
    exitstatus('Ошибка базы данных');
} else {
    if (is_defined('id_topic')) {
        $id_topic = $_REQUEST['id_topic'];
        $id_topic = ($id_topic * 10) / 10;

        $id_topic_owner = $database->query("SELECT id_user FROM ForumTopics WHERE id=$id_topic")->fetchColumn();

        if (is_defined('event')) {
            if ($id_topic_owner != $_SESSION['myuser_id']) {
                exitstatus('Доступ запрещен');
            }

            $cmd = $_REQUEST['event'];
            if ($cmd == 'topicsettings') {
                if (check_login()) {
                    $en_private = getCheckboxVal('topic', 'private');
                    $en_noanon = getCheckboxVal('topic', 'noanon');
                    $en_readonly = getCheckboxVal('topic', 'readonly');

                    $database->exec("UPDATE ForumTopics SET private=$en_private, readonly=$en_readonly WHERE id=$id_topic");
                    if ($en_noanon == 1) {
                        $database->exec("INSERT INTO ForumTopicUsers(id_topic, id_user, id_session, readonly) VALUES($id_topic, 0, '', 1)");
                    } else {
                        $database->exec("DELETE FROM ForumTopicUsers WHERE id_topic=$id_topic AND id_user=0 AND id_session=''");
                    }

                    exitstatus('ok');
                }
                exitstatus('Доступ запрещен');
            } else if ($cmd == 'unban') {
                if (check_login()) {
                    $id_user = $_REQUEST['id_user'];
                    $id_user = ($id_user * 10) / 10;
                    $id_session = addslashes($_REQUEST['id_session']);
                    if ($id_user == 0) {
                        $database->exec("DELETE FROM ForumTopicUsers WHERE id_topic=$id_topic AND id_user=0 AND id_session='$id_session'");
                    } else {
                        $database->exec("DELETE FROM ForumTopicUsers WHERE id_topic=$id_topic AND id_user=$id_user");
                    }

                    exitstatus('ok');
                }
                exitstatus('Доступ запрещен');
            } else if ($cmd == 'add') {
                if (check_login()) {
                    $id_user = $_REQUEST['id_user'];
                    $id_user = ($id_user * 10) / 10;
                    $id_session = addslashes($_REQUEST['id_session']);
                    if ($id_user != 0) {
                        $id_session = '';
                    }

                    $database->exec("DELETE FROM ForumTopicUsers WHERE id_topic=$id_topic AND id_user=$id_user AND (id_user!=0 OR (id_user=0 AND id_session='$id_session'))");
                    $database->exec("INSERT INTO ForumTopicUsers(id_topic, id_user, id_session, readonly) VALUES($id_topic, $id_user, '$id_session', 0)");

                    exitstatus('ok');
                }
                exitstatus('Доступ запрещен');
            }
        } else if (is_defined('invite')) {
            if ($id_topic_owner != $_SESSION['myuser_id']) {
                ?>
<h1>Доступ запрещен</h1>
<?php
                exit();
            }
            if (check_login()) {
                $invite_id_user = $_REQUEST['invite'];
                $invite_id_user = ($invite_id_user * 10) / 10;
                if (is_defined('id_session')) {
                    $invite_id_session = addslashes($_REQUEST['id_session']);
                } else {
                    $invite_id_session = '';
                }

                $invite_login = $database->query("SELECT login FROM ForumUsers WHERE id=$invite_id_user")->fetchColumn();
                $topic = $database->query("SELECT topic FROM ForumTopics WHERE id=$id_topic")->fetchColumn();

                if ($invite_id_user == 0) {
                    $anon_session = " ($invite_id_session) ";
                } else {
                    $anon_session = '';
                }
                ?>
<div class="refreshnow" src="topicsettings.js"></div>
<div class="modal-content-window topicsettings_window">
	<h1>Запрос от пользователя</h1>
<?php
                if ($database->query("SELECT id_topic FROM ForumTopicUsers WHERE id_user=$invite_id_user AND id_topic=$id_topic AND id_session='$invite_id_session'")->fetchColumn() == $id_topic) {
                    ?>		    
Пользователь <?php echo format_user_nick($invite_login, $invite_id_user, $invite_login, $invite_id_user).$anon_session; ?>
 уже добавлен в список доступа темы <a
		href="?t=<?php echo $id_topic; ?>" target="_blank"><?php echo $topic; ?></a>.
 Удалить пользователя можно в настройках темы.
<?php
                } else {
                    ?>
Пользователь <?php echo format_user_nick($invite_login, $invite_id_user, $invite_login, $invite_id_user).$anon_session; ?>
 просит доступ к теме <a href="?t=<?php echo $id_topic; ?>"
		target="_blank"><?php echo $topic; ?></a><br>
	<br>
	<button type="button" id="add_submit"
		onclick="return AddSubmit(<?php echo $id_topic;?>, <?php echo $invite_id_user; ?>, '<?php echo $invite_id_session; ?>')">Разрешить</button>
	<span class="error1" id="add_settings_error"></span>
	<span id="add_submit_process" hidden>
		<svg width="19px" height="19px"	viewBox="0 0 50 50">
			<path fill="#33CCFF"
				d="M25,5A20.14,20.14,0,0,1,45,22.88a2.51,2.51,0,0,0,2.49,2.26h0A2.52,2.52,0,0,0,50,22.33a25.14,25.14,0,0,0-50,0,2.52,2.52,0,0,0,2.5,2.81h0A2.51,2.51,0,0,0,5,22.88,20.14,20.14,0,0,1,25,5Z">
				<animateTransform attributeName="transform" type="rotate" from="0 25 25"
					to="360 25 25" dur="0.5s" repeatCount="indefinite" />
			</path>
		</svg>
	</span>
	<br>
	<br>Если вы не хотите разрешать доступ в тему, просто закройте это окно.
<?php
                }
                ?>		
</div>
<?php
            } else {
                ?>
<h1>Доступ запрещен</h1>
<?php
            }
            exit();
        }

        if ($id_topic_owner != $_SESSION['myuser_id']) {
            ?>
<h1>Доступ запрещен</h1>
<?php
            exit();
        }
        ?>
<div class="refreshnow" src="topicsettings.js"></div>
<div class="modal-content-window topicsettings_window">
<?php
        $anon_ro = $database->query("SELECT readonly FROM ForumTopicUsers WHERE id_topic=$id_topic AND id_user=0 AND id_session=''")->fetchColumn();

        if (! is_defined('show_users')) {
            $sth = $database->prepare("SELECT private,readonly FROM ForumTopics WHERE id=$id_topic AND id_user={$_SESSION['myuser_id']}");
            $sth->execute();
            $row = $sth->fetch();

            $en_private = ($row['private'] == 1) ? 'checked' : '';
            $en_noanon = ($anon_ro == 1) ? 'checked' : '';
            $en_readonly = ($row['readonly'] == 1) ? 'checked' : '';
            ?>
	<h1>Настройки темы</h1>
	<form action="" id="topic_settings_form"
		onsubmit="return TopicSettingsSubmit(<?php echo $id_topic; ?>)">
		<input type="hidden" name="event" value="topicsettings">
		<input type="checkbox" id="ts_check_private" name="topic[private]" value="1"
			<?php echo $en_private; ?>>
		<label for="ts_check_private">Закрытая (вход не для всех)</label><br>
		<input type="checkbox" id="ts_check_noanon" name="topic[noanon]" value="1"
			<?php echo $en_noanon; ?>>
		<label for="ts_check_noanon">Без анонимных сообщений</label><br>
		<input type="checkbox" id="ts_check_readonly" name="topic[readonly]" value="1" <?php echo $en_readonly; ?>>
		<label for="ts_check_readonly">Только свои сообщения</label><br>
		<input type="submit" id="ts_submit" value="Сохранить" style="float: right;">
		<span class="error1" id="topic_settings_error"></span>
		<span id="ts_submit_process" hidden>
			<svg width="19px" height="19px" viewBox="0 0 50 50">
				<path fill="#33CCFF"
					d="M25,5A20.14,20.14,0,0,1,45,22.88a2.51,2.51,0,0,0,2.49,2.26h0A2.52,2.52,0,0,0,50,22.33a25.14,25.14,0,0,0-50,0,2.52,2.52,0,0,0,2.5,2.81h0A2.51,2.51,0,0,0,5,22.88,20.14,20.14,0,0,1,25,5Z">
					<animateTransform attributeName="transform" type="rotate" from="0 25 25"
						to="360 25 25" dur="0.5s" repeatCount="indefinite" />
				</path>
			</svg>
		</span>
	</form>
<?php
        } else {
            // $is_private = $row['private'];
            $cnt = 0;
            ?>
	<h1>Участники</h1>
	<table class="userstable">
		<tr>
			<th>Пользователь</th>
			<th>Чтец</th>
			<th></th>
		</tr>
<?php
            foreach ($database->query("SELECT ForumTopicUsers.id_user AS id_user, ForumTopicUsers.id_session AS id_session," . " ForumTopicUsers.readonly AS readonly, ForumUsers.login AS login" . " FROM ForumTopicUsers,ForumUsers" . " WHERE ForumTopicUsers.id_topic=$id_topic AND ForumUsers.id=ForumTopicUsers.id_user") as $row) {
                if ($anon_ro == 1 && $row['id_user'] == 0 && ($row['id_session'] == '' || $row['readonly'] == 1)) {
                    continue;
                }

                $button = '<a id="unban_submit' . $cnt . '" href="" onclick="return UnbanSubmit(' . $id_topic . ',' . $row['id_user'] . ',\'' . $row['id_session'] . '\',' . $cnt . ')"><svg viewBox="0 0 20 20" width="16px" class="svg_button">
<title>Удалить</title>
<path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm1.41-1.41A8 8 0 1 0 15.66 4.34 8 8 0 0 0 4.34 15.66zm9.9-8.49L11.41 10l2.83 2.83-1.41 1.41L10 11.41l-2.83 2.83-1.41-1.41L8.59 10 5.76 7.17l1.41-1.41L10 8.59l2.83-2.83 1.41 1.41z"/>
</svg></a>';

                ?>
		<tr>
<?php
                if ($row['id_user'] == 0) {
                    ?>
			<td><?php echo $row['login']; ?> <?php echo $row['id_session']; ?></td>
<?php
                } else {
                    ?>		 
			<td><?php echo format_user_nick($row['login'], $row['id_user'], $row['login'], $row['id_user']); ?></td>
<?php
                }
                if ($row['readonly'] == 1) {
                    ?>		    
			<td>Да</td>
<?php
                } else {
                    ?>		    
			<td>Нет</td>
<?php
                }
                ?>		
			<td><?php echo $button; ?><span
				id="unban_submit_process<?php echo $cnt; ?>" hidden></span></td>
		</tr>
<?php
                $cnt ++;
            }
            ?>	 
	</table>
<?php
        }
        ?>
</div>
<?php
    } else if (is_defined('id_post')) {
        $id_post = $_REQUEST['id_post'];
        $id_post = ($id_post * 10) / 10;
        if (is_defined('event')) {
            $cmd = $_REQUEST['event'];
            if ($cmd == 'ban') {
                if (check_login()) {
                    $sth = $database->prepare("SELECT ForumTopics.private AS private, ForumPosts.id_topic AS id_topic," . " ForumPosts.id_user AS id_user, ForumPosts.id_session AS id_session" . " FROM ForumTopics, ForumPosts" . " WHERE ForumPosts.id=$id_post AND ForumTopics.id=ForumPosts.id_topic" . " AND ForumTopics.id_user={$_SESSION['myuser_id']}");
                    $sth->execute();
                    $row = $sth->fetch();
                    if ($row != null) {
                        if ($row['private']) {
                            if ($row['id_user'] == 0) {
                                $database->exec("DELETE FROM ForumTopicUsers WHERE id_topic={$row['id_topic']} AND id_user=0 AND id_session='{$row['id_session']}'");
                            } else {
                                $database->exec("DELETE FROM ForumTopicUsers WHERE id_topic={$row['id_topic']} AND id_user={$row['id_user']}");
                            }
                        } else {
                            if ($row['id_user'] == 0) {
                                $database->exec("INSERT INTO ForumTopicUsers(id_topic, id_user, id_session, readonly) VALUES({$row['id_topic']}, 0, '{$row['id_session']}', 1)");
                            } else {
                                $database->exec("INSERT INTO ForumTopicUsers(id_topic, id_user, id_session, readonly) VALUES({$row['id_topic']}, {$row['id_user']}, '', 1)");
                            }
                        }

                        exitstatus('ok');
                    }
                }
            }
        }
        exitstatus('Доступ запрещен');
    } else {
?>
<h1>Доступ запрещен</h1>
<?php	
	exit();
    }
}

?>
