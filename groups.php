<?php
require_once('config.php');

session_start();

include('funcs.php');
include('header.php');
include('footer.php');


/* Открываем базу данных */
$database = new PDO("sqlite:" . DBASEFILE);
if (!$database) {
    print("<b>Ошибка базы данных.</b>");
} else if (is_forum_admin()) {
    show_header("Редактор групп");
    echo 
'<p><h3>База данных</h3></p>
<table>
<tr bgcolor="#dddddd">
<td>Группа</td>
<td>Примечание</td>
<td>id</td>
<td></td>
<td></td>
</tr>
';
    /* Режим редактирования выключен */
    $edit = "";

    {
	/* По умолчанию запрос на вывод всех записей из базы данных с сортировкой по фамилии по алфавиту */
	$view_query = "SELECT * FROM ForumGroups ORDER BY grp ASC;";

	/* Проверяем есть ли переданная команда */
	$cmd = $_REQUEST["cmd"];
	
	/* Команда удаления */
	if ($cmd == "delete") {
	    /* Сохраняем в переменной переданный идентификатор удаляемой записи */
	    $id = $_REQUEST["id"];
	    if ($id) {
		/* Выполняем запрос на удаление записи соответствующей принятому идентификатору */
		$database->exec("DELETE FROM ForumGroups WHERE id LIKE '$id';");
		print "<p>Запись была удалена из базы данных.</p>";
	    }
	}
	
	/* Команды добавления, сохранения или правки */
	if (($cmd == "Добавить") || ($cmd == "Сохранить") || ($cmd == "Искать") || ($cmd == "edit")) {
	    /* Сохраняем в переменных переданные данные о записи */
	    $id = $_REQUEST["id"];
	    $grp = $_REQUEST["grp"];
	    $note = $_REQUEST["note"];
	    /* Команда добавления добавляет новую запись в базу данных */
	    if ($cmd == "Добавить") {
		/* Используем время как идентификатор записи */
		//$id = time();
		/* Формируем запрос */
		$query = "INSERT INTO ForumGroups (grp, note, id)" .
			"VALUES ('$grp', '$note', NULL);";
		/* Выполняем запрос */
		$database->exec($query);
		print "<p><b>Новая запись добавлена в базу!</b></p>";
	    }
	    /* Команда сохранения заменяет старую запись в базе новой исправленной */
	    if ($cmd == "Сохранить") {
		/* Формируем запрос */
		$query = "REPLACE INTO ForumGroups (grp, note, id)" .
			"VALUES ('$grp', '$note', '$id');";
		/* Выполняем запрос */
		$database->exec($query);
		print "<p><b>Запись исправлена!</b></p>";
	    }

	    if ($cmd == "Искать") {
		/* Составляем запрос на вывод - выбрать все записи совпадающие с заполненными полями в форме */
		$view_query = "SELECT * FROM ForumGroups WHERE ";
		$op = "";
		if ( $grp != "") {
		    $view_query = "$view_query grp LIKE '$grp'";
		}
		/* Добавляем в сформированный запрос сортировку по фамилии по алфавиту */
		$view_query = "$view_query ORDER BY grp ASC;";
		print "<p>Результаты поиска в базе (<a href=\"groups.php\">Показать всю</a>)</p>";
	    }

	    /* Для правки устанавливаем переменную, которая используется как флаг для заполнения полей формы ввода при редактировании записи */
	    if ($cmd == "edit") {
		$edit = "edit";
	    }
	}
	
	/* Выполняем запрос вывода записей в таблицу */
	foreach ($database->query($view_query) as $row) {
	    print("<tr>" .
	      "<td>{$row['grp']}</td>" .
	      "<td>{$row['note']}</td>".
	      "<td>{$row['id']}</td>".
	      "<td><a href=\"groups.php?cmd=edit&id={$row['id']}&grp={$row['grp']}&note={$row['note']}\">Правка</a></td>" .
	      "<td><a href=\"groups.php?cmd=delete&id={$row['id']}\">Удалить</a></td>" .
	      "</tr>"
	    );
	}
    }
?>
</table>
<p><h3><?php if ($edit) echo 'Правка записи'; else echo 'Ввод новых данных или поиск по базе' ?></h3></p>
<form action="groups.php" method="post">
<table>
<tr><td>Группа</td><td><input type="text" name="grp" <?php if ($edit) echo ' value="'.$grp.'"' ?>></td><tr>
<tr><td>Примечание</td><td><input type="text" name="note" <?php if ($edit) echo ' value="'.$note.'"' ?>></td><tr>
<tr><td></td><td><input type="hidden" name="id" <?php if ($edit) echo ' value="'.$id.'"' ?>></td><tr>
<tr><td><input type="submit" name="cmd" value=<?php if ($edit) echo '"Отменить"'; else echo '"Искать"' ?>></td></td><td><input type="submit" name="cmd" value=<?php if ($edit) echo '"Сохранить"'; else echo '"Добавить"' ?>></td><tr>
</table>
</form>
<a href="index.php">Вернуться на страницу форума</a>
</body>
</html>
<?php

    show_footer();
    /* Закрываем базу данных */
    unset($database);
}
?>
