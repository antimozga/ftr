<?php
require_once ('config.php');

session_start();

include ('funcs.php');
?>

<div class="modal-content-window" style="display: table;">
	<form action=""
		style="display: table-cell; vertical-align: middle; text-align: center;">
		<input style="width: 80%;" type="text" name="search"
			onfocus="if(this.value == 'Поиск по темам...') { this.value = ''; }"
			value="Поиск по темам..." /> <input class="btn_group_sel"
			type="submit" value="&nbsp;" />
	</form>
</div>
