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
<link href="style/style.css" type="text/css" rel="stylesheet" />
<title><?php echo $FORUM_TITLE; ?> - <?php echo $title; ?></title>
<script type="text/javascript" src="js/scripts.js"></script>
<script type="text/javascript" src="js/highslide.js"></script>
</head>
<body>

<?php
}
?>
