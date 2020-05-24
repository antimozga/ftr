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
    $request = curl_init('https://video.vtomske.net/nsfw');

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

$query = $database->query("SELECT id_post, idx, attachment FROM ForumPostAttachment WHERE censor=0");
$results = $query->fetchAll(PDO::FETCH_OBJ);
$query = NULL;

//print_r($results);

foreach($results as $row) {
    printf("Processing %s\n", $row->attachment);

    if (file_exists($UPLOAD_DIR.'/'.$row->attachment)) {
	$image_ext = strtolower(substr(strrchr($row->attachment, '.'), 1));
	if ($image_ext == 'jpg' || $image_ext == 'jpeg' || $image_ext == 'gif' || $image_ext == 'png' || $image_ext == 'webp') {
	    system("convert $UPLOAD_DIR/small-{$row->attachment} -delete 1--1 /tmp/small-{$row->attachment}.jpg");
//
// convert 597_1000.jpg -delete 1--1 597_1000_out.jpg
//
	    $ret = censor("/tmp/small-{$row->attachment}.jpg");
	    if ($ret > 0) {
		$database->exec("UPDATE ForumPostAttachment SET censor=-1 WHERE id_post={$row->id_post} AND idx={$row->idx}");
	    } else if ($ret == 0) {
		$database->exec("UPDATE ForumPostAttachment SET censor=1 WHERE id_post={$row->id_post} AND idx={$row->idx}");
	    }

	    unlink("/tmp/small-{$row->attachment}.jpg");
	} else {
	    $database->exec("UPDATE ForumPostAttachment SET censor=1 WHERE id_post={$row->id_post} AND idx={$row->idx}");
	}
    }
}

?>
