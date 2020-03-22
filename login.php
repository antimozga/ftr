<?php

require_once('config.php');

session_start();

include('funcs.php');

echo '
<div style="display:table; width:100%; height:100%;">
	<form action="" method="post" style="display:table-cell; vertical-align:middle; text-align:center;">
	    <input type="hidden" name="event" value="login"/>
	    <div class="form_box_text">
		<input type="text" autocomplete="username" style="width:40%;" name="user[name]" onfocus="if(this.value == \'Имя\') { this.value = \'\'; }" value="Имя"/>
		<input type="password" autocomplete="current-password" style="width:40%;" name="user[password]" onfocus="if(this.value == \'Пароль\') { this.value = \'\'; }" value="Пароль"/>
		<input class="btn_group_sel" type="submit" value="&nbsp;" />
	    </div>
	</form>
</div>';

?>
