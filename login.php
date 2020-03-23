<?php

require_once('config.php');

session_start();

include('funcs.php');

echo '
<div class="modal-content-window" style="display:table;">
	<form action="" method="post" style="display:table-cell; vertical-align:middle; text-align:center;">
	    <div style="font-size: 200%;font-weight: bold;padding-bottom: 20px;">Вход</div>
	    <input type="hidden" name="event" value="login"/>
	    <div class="form_box_text">
		<input type="text" autocomplete="username" style="width:40%;" name="user[name]" onfocus="if(this.value == \'Имя\') { this.value = \'\'; }" value="Имя"/>
		<input type="password" autocomplete="current-password" style="width:40%;" name="user[password]" onfocus="if(this.value == \'Пароль\') { this.value = \'\'; }" value="Пароль"/>
		<input class="btn_group_sel" type="submit" value="&nbsp;" />
	    </div>
	    <div style="padding-top: 10px;"><a href="?reg=1">Регистрация</a></div>
	</form>
</div>';

?>
