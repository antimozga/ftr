<?php
function show_header($title) {
    global $FORUM_TITLE;
    global $RECAPTCHA_SITE_KEY;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="ru-RU" xml:lang="ru">
<head>
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Expires" content="0" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="Cache-control" content="no-cache" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="Томские форумы. Общение на любые темы." />
<meta name="keywords" content="Общение,Форумы,Форум,ФТР,foruma.vtomske.net,vtomske.net,чат,фото,видео,цензура,тонет" />
<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
<!-- <link href="style/style.css" type="text/css" rel="stylesheet" /> -->
<style>
<?php
include_once ("style/style.css");
include_once ("style/highslide.css");
?>
</style>
<title><?php echo $FORUM_TITLE; ?> - <?php echo $title; ?></title>
<!-- <script type="text/javascript" src="js/highslide.packed.js"></script> -->
<!-- <script type="text/javascript" src="js/scripts.js"></script> -->
<script>
<?php
include_once ("js/highslide.packed.js");
include_once ("js/scripts.js");
?>

hs.registerOverlay({
	html: '<div class="closebutton" onclick="return hs.close(this)" title="Close"></div>',
	position: 'top right',
	fade: 2 // fading the semi-transparent overlay looks bad in IE
});


hs.graphicsDir = 'graphics/';
hs.wrapperClassName = 'borderless';

</script>
</head>
<body>
<div class="modal">
    <div class="modal-window">
        <span class="close-button">&times;</span>
	<span id="modal-content"></span>
    </div>
</div>

<?php
}
?>
