<?php

function is_cli()
{
    if (defined('STDIN')) {
        return true;
    }

    if (empty($_SERVER['REMOTE_ADDR']) and ! isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0) {
        return true;
    }

    return false;
}

if (! is_cli()) {
    exit();
}

require_once ('config.php');

$database = new PDO("sqlite:" . DBASEFILE);

if (! $database) {
    printf("DataBase error\n");
    exit();
}

function censor($filename) {
    // initialise the curl request
    $request = curl_init('http://62.171.146.145:8080/nsfw');

    // Create a CURLFile object
    $cfile = curl_file_create($filename);

    // Assign POST data
    $data = array('image' => $cfile);
    curl_setopt($request, CURLOPT_POST, 1);
    curl_setopt($request, CURLOPT_POSTFIELDS, $data);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);

    $ret = curl_exec($request);
    $datas = json_decode($ret);

    curl_close($request);

    if ($datas == null) {
	if ($ret == 'Unsupported image file') {
	    return 0;
	}
	return -1;
    }

    if (is_array($datas)) {
	switch($datas[0]->{'className'}) {
	    case 'Hentai':
	    case 'Porn':
	    case 'Sexy':
//		printf("%s: %s: %f\n", $filename, $datas[0]->{'className'}, $datas[0]->{'probability'});
		return 1;
	    default:
		//printf("%s: %s: %f\n", $filename, $datas[0]->{'className'}, $datas[0]->{'probability'});
		return 0;
	}
    }
}

foreach($database->query("SELECT id_post, idx, attachment FROM ForumPostAttachment WHERE censor=0") as $row) {
    if (file_exists($UPLOAD_DIR.'/'.$row['attachment'])) {
	$ret = censor($UPLOAD_DIR.'/'.$row['attachment']);
	if ($ret > 0) {
	    $database->exec("UPDATE ForumPostAttachment SET censor=-1 WHERE id_post={$row['id_post']} AND idx={$row['idx']}");
	} else if ($ret == 0) {
	    $database->exec("UPDATE ForumPostAttachment SET censor=1 WHERE id_post={$row['id_post']} AND idx={$row['idx']}");
	}
    }
}

?>
