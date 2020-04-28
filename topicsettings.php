<?php

require_once('config.php');

session_start();

include('funcs.php');

function getCheckboxVal($name1, $name2)
{
    if (isset($_POST[$name1][$name2]) && $_POST[$name1][$name2] == '1') {
	return 1;
    } else {
	return 0;
    }
}

$database = new PDO("sqlite:" . DBASEFILE);
if (!$database) {
    echo '<p>Ошибка базы данных.</p>';
} else {
    if (is_defined('t')) {
	$id_topic = addslashes($_REQUEST['t']);
	if (check_login()) {
	    if (is_defined("event")) {
		$cmd = $_REQUEST["event"];
		if ($cmd == "topicsettings") {
		    $en_private  = getCheckboxVal('topic', 'private');
		    $en_noanon   = getCheckboxVal('topic', 'noanon');
		    $en_readonly = getCheckboxVal('topic', 'readonly');

		    $database->exec("UPDATE ForumTopics SET private=$en_private, readonly=$en_readonly WHERE id=$id_topic AND id_user={$_SESSION['myuser_id']}");
		    if ($en_noanon == 1) {
			$database->exec("REPLACE INTO ForumTopicUsers(id_topic, id_user, id_session, readonly) VALUES($id_topic, 0, '', 1)");
		    } else {
			$database->exec("DELETE FROM ForumTopicUsers WHERE id_topic=$id_topic AND id_user=0 AND id_session=''");
		    }

		    $myObj = [
			'status' => 'ok',
		    ];

		    echo json_encode($myObj);

		    exit();
		}
	    }

	    echo '<div class="refreshnow" src="topicsettings.js"></div>';

	    $sth = $database->prepare("SELECT private,readonly FROM ForumTopics WHERE id=$id_topic AND id_user={$_SESSION['myuser_id']}");
	    $sth->execute();
	    $row = $sth->fetch();
	    $anon_ro = $database->query("SELECT readonly FROM ForumTopicUsers WHERE id_topic=$id_topic AND id_user=0")->fetchColumn();

	    $en_private = ($row['private'] == 1) ? 'checked' : '';
	    $en_noanon = ($anon_ro == 1) ? 'checked' : '';
	    $en_readonly = ($row['readonly'] == 1) ? 'checked' : '';

	    echo '
<div class="modal-content-window" style="display:table;">
<h1>Настройки темы</h1>

<form action="" id="topic_settings_form" onsubmit="return TopicSettingsSubmit('.$id_topic.')">
  <input type="hidden" name="event" value="topicsettings">
  <input type="checkbox" id="ts_check_private" name="topic[private]" value="1" '.$en_private.'>
  <label for="ts_check_private">Закрытая (вход не для всех)</label><br>
  <input type="checkbox" id="ts_check_noanon" name="topic[noanon]" value="1" '.$en_noanon.'>
  <label for="ts_check_noanon">Без анонимных сообщений</label><br>
  <input type="checkbox" id="ts_check_readonly" name="topic[readonly]" value="1" '.$en_readonly.'>
  <label for="ts_check_readonly">Только свои сообщения</label><br>
  <input type="submit" id="ts_submit" value="Сохранить" style="float: right;">
<span class="error1" id="topic_settings_error"></span><span id="ts_submit_process" hidden>
<svg width="19px" height="19px" viewBox="0 0 50 50">
<path fill="#33CCFF" d="M25,5A20.14,20.14,0,0,1,45,22.88a2.51,2.51,0,0,0,2.49,2.26h0A2.52,2.52,0,0,0,50,22.33a25.14,25.14,0,0,0-50,0,2.52,2.52,0,0,0,2.5,2.81h0A2.51,2.51,0,0,0,5,22.88,20.14,20.14,0,0,1,25,5Z">
<animateTransform attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.5s" repeatCount="indefinite"/>
</path>
</svg>
</span>
</form>

</div>';
	}
    } else {
	echo '<p>Доступ запрещен.</p>';
    }
}

?>
