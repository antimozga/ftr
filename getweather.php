<?php
$text = file_get_contents('http://pogodavtomske.ru/informer/getKod.php?c=geks&tp=250_100_js&l=29430&t=1582398990894');
$text = substr($text, 0, strpos($text, 'try'));
echo iconv("CP1251", "UTF-8", $text);
?>
