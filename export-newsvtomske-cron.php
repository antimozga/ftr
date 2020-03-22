<?php

#
# /etc/crontab
#
# */3 *   * * *   www-data php /var/www/html/export-newsvtomske-cron.php
#

function is_cli()
{
    if( defined('STDIN')){
	return true;
    }

    if (empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0) {
	return true;
    }

    return false;
}

if (!is_cli()) {
    exit;
}

require_once('config.php');

if (!isset($FORUM_NEWSVTOMSKE_GID)) {
    exit;
}

$parsedate = new DateTime('NOW');

$database = new PDO("sqlite:" . DBASEFILE);
//$database = new PDO("sqlite:fftc.sqlite");

if (!$database) {
    printf("DataBase error\n");
    exit;
}

$query = "CREATE TABLE IF NOT EXISTS NewsVTomske ".
	 "(id INTEGER PRIMARY KEY,".
	 " time INTEGER,".
	 " link NVARCHAR,".
	 " id_topic INTEGER)";
$database->exec($query);

/*
$sth = $database->prepare("SELECT * FROM NewsVTomske ORDER BY id DESC LIMIT 1");
$sth->execute();
$row = $sth->fetch();

if ($row) {
    $last_access = $row['time'];

    $now = time();
    if ($now - $last_access < 60) {
	$delay = 60 - ($now - $last_access);
	printf("delay $delay for 1 minute period\n");
	sleep($delay);
    }
}
 */

sleep(mt_rand(0, 89));

$options  = array('http' => array('user_agent' => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36\r\n"));
$content = file_get_contents('https://news.vtomske.ru/c/tomsk', false, stream_context_create($options));
if ($content === false) {
    printf("Cannot get weather data\n");
    exit;
}

//echo $content;

function findClass($dom, $tag, $class) {
    $stack = array();
    $elements = $dom->getElementsByTagName($tag);

    foreach ($elements as $element) {
	if (strpos($element->getAttribute('class'), $class) !== false) {
	    array_push($stack, $element);
	}
    }

    return (count($stack) > 0) ? $stack : false;
}

function stripSpaces($str) {
    return preg_replace('/\s+/', '', $str);
}

function strToNum($str) {
    return str_replace(array('−', ','), array('-', '.'), $str);
}

function addPost($post) {
    global $database;
    global $parsedate;
    global $FORUM_NEWSVTOMSKE_GID;

    $postdate = clone $parsedate;
    $date = '';

    $href = $post->getAttribute('href');
    $class_title = findClass($post, 'div', 'title');
    if ($class_title !== false) {
	$txt_date = $class_title[0]->firstChild->textContent;
	if (strpos($txt_date, 'Сегодня') !== false) {
	} else if (strpos($txt_date, 'Вчера') !== false) {
	    date_modify($postdate, '-1 day');
	} else {
	    echo "error [$txt date]\n";
	    return false;
	}
	$date = $postdate->format('Y-m-d').'T'.trim(substr($txt_date, strpos($txt_date, ',') + 1)).':00+07:00';
    } else {
	echo "error no date\n";
	return false;
    }

    $tim = strtotime($date);

    $check_tim = $database->query("SELECT time FROM NewsVTomske WHERE link='$href'")->fetchColumn();
    if ($check_tim != "") {
	printf("already added\n");
	return false;
    }

    $options  = array('http' => array('user_agent' => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36\r\n"));
    $content = file_get_contents('https://news.vtomske.ru'.$href, false, stream_context_create($options));
    if ($content === false) {
	printf("Cannot get news data\n");
	return false;
    }

    $dom = new DomDocument();
    $dom->loadHTML($content);

    $title = '';
    $author = '';
    $text = 'Оригинал новости https://news.vtomske.ru'.$href.'<br/><br/>';

    $class_material = findClass($dom, 'div', 'material material-news');
    if ($class_material !== false) {
	$title_class = findClass($class_material[0], 'h1', 'material-title');
	if ($title_class !== false) {
	    $title = $title_class[0]->textContent;

	    $class_author = findClass($class_material[0], 'div', 'author');
	    if ($class_author !== false) {
		$author = $class_author[0]->textContent;

		if (strpos($author, 'На правах рекламы') !== false) {
		    return false;
		}

		if (strpos($author, 'Партнерский материал') !== false) {
		    return false;
		}

		$class_text = findClass($class_material[0], 'div', 'full-text');
		if ($class_text !== false) {
		    $class_pars = $class_text[0]->getElementsByTagName('p');
		    foreach($class_pars as $par) {
			$text = $text.trim($par->textContent).'<br/><br/>';
		    }

		    $id_session = md5($author);

//		    $author = $author.'&#x1f916;';

		    $database->exec("INSERT INTO ForumTopics (id, id_grp, id_user, nick, topic, id_session)" .
				    " VALUES(NULL, $FORUM_NEWSVTOMSKE_GID, 0, '$author', '$title', '$id_session')");
		    $id_topic = $database->lastInsertId();

		    $database->exec("INSERT INTO ForumPosts (id, time, id_grp, id_topic, id_user, nick, subj, post, id_session)" .
				    " VALUES (NULL, $tim, $FORUM_NEWSVTOMSKE_GID, $id_topic, 0, '$author', '$title', '$text', '$id_session');");

		    $database->exec("INSERT INTO NewsVTomske (id, time, link, id_topic) ".
				    "VALUES(NULL, $tim, '$href', $id_topic)");
		    return true;
		}
	    }
	}
    }

    return false;
}

$dom = new DomDocument();
$dom->loadHTML($content);

$class = findClass($dom, 'div', 'content-singlecolumn article-list clever-columns');

if ($class !== false) {
    $small_posts = findClass($class[0], 'a', 'news-small');
    if ($small_posts !== false) {
	foreach(array_reverse($small_posts) as $post) {
	    addPost($post);
	}
    }

    $medium_post = findClass($class[0], 'a', 'news-medium');
    if ($medium_post !== false) {
	addPost($medium_post[0]);
    }
}

?>
