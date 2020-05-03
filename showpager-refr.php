<?php
require_once ('config.php');

session_start();

include ('funcs.php');

$database = new PDO("sqlite:" . DBASEFILE);
if (! $database) {
    ?>
<p>Ошибка базы данных.</p>
<?php
} else {
    if (check_login()) {
        $pager_query = "SELECT COUNT(*) as total, ForumPager.id_from_user AS id_from_user, ForumUsers.login AS login FROM ForumPager,ForumUsers" . " WHERE ForumPager.id_user = " . $_SESSION['myuser_id'] . " AND ForumUsers.id = ForumPager.id_from_user" . " GROUP BY ForumPager.id_from_user ORDER BY ForumUsers.login;";
        ?>
<table class="pagertable">
	<tr>
		<th>Пользователь</th>
		<th colspan=2>Сообщений</th>
	</tr>
<?php
        foreach ($database->query($pager_query) as $row) {
            $pn = $database->query('SELECT COUNT(*) FROM ForumPager WHERE id_user = ' . $_SESSION['myuser_id'] . ' AND id_from_user = ' . $row['id_from_user'] . ' AND new = 1;')->fetchColumn();
            ?>
	<tr>
		<td><?php echo format_user_nick($row['login'], $row['id_from_user'], $row['login'], $row['id_from_user']); ?></td>
		<td class="td2"><?php echo $pn; ?>&nbsp;|&nbsp;<?php echo $row['total']; ?></td>
		<td class="td3"><a href="#"
			onclick="load_modal('pagerchat.php/?new=<?php echo $row['id_from_user']; ?>'); return false;">Показать</a></td>
	</tr>
<?php
        }
        ?>
</table>
<?php
    } else {
        ?>
<p>Доступ запрещен.</p>
<?php
    }
}
?>
