<?php

require_once('config.php');

session_start();

include('funcs.php');

echo '
<div class="vert-center">
	<form action="">
		<table><tr>
		<td class="tdw1"><input type="text" name="search" onfocus="if(this.value == \'Поиск по темам...\') { this.value = \'\'; }" value="Поиск по темам..."/></td>
		<td class="tdw2"><input class="btn_group_sel" type="submit" value="&nbsp;"/></td>
		</tr></table>
	</form>
</div>';

?>
