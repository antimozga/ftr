<?php

#
# url.rewrite-once = ( "^/(.*)" => "/redirect-newsvtomske.php/$1" )
#

require_once('config.php');

if (!isset($FORUM_NEWSVTOMSKE_GID)) {
    header("Location: https://foruma.vtomske.net");
    exit;
}

$database = new PDO("sqlite:" . DBASEFILE);

if (!$database) {
    header("Location: https://foruma.vtomske.net/?g=$FORUM_NEWSVTOMSKE_GID");
    exit;
}

$link = substr($_SERVER[REQUEST_URI], strpos($_SERVER[REQUEST_URI], '/news/'));
$id_topic = $database->query("SELECT id_topic FROM NewsVTomske WHERE link='$link'")->fetchColumn();

if ($id_topic != "") {
    header("Location: https://foruma.vtomske.net/?t=$id_topic");
    exit;
} else {
?>

<html>
<body>
<p>Новость будет готова в течении нескольних минут...</p>
<script>
function fun() {
    setTimeout(function(){window.location = '<?php echo "https://foruma.vtomske.net/?g=$FORUM_NEWSVTOMSKE_GID"; ?>';}, 3000);
}
fun();
</script>
</body>
</html>

<?php
}

?>
