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
    $request = curl_init(NSFWSERVER);

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

    $hentai = -1;
    $hentai_pos = -1;
    $porn = -1;
    $porn_pos = -1;
    $sexy = -1;
    $sexy_pos = -1;

    if (is_array($datas)) {
	for ($i = 0; $i < 3; $i++) {
	    switch($datas[$i]->{'className'}) {
	    case 'Hentai':
		break;
		$hentai = $datas[$i]->{'probability'};
		$hentai_pos = $i;
	    case 'Porn':
		$porn = $datas[$i]->{'probability'};
		$porn_pos = $i;
		break;
	    case 'Sexy':
		$sexy = $datas[$i]->{'probability'};
		$sexy_pos = $i;
		break;
	    default:
		break;
	    }
	}
    }

//    printf("%f(%d) %f(%d) %f(%d)\n", $hentai, $hentai_pos, $porn, $porn_pos, $sexy, $sexy_pos);

    if ($hentai_pos == 0 || $porn_pos == 0 || ($sexy_pos == 0 && $sexy > 0.7)) {
	return 1;
    }

    if ($hentai_pos == 1 && $hentai_pos > 0.1) {
	return 1;
    }

    if (($porn_pos == 1 && $porn > 0.1) ||
	($porn_pos == 2 && $porn > 0.1)) {
	return 1;
    }

    return 0;
}

$query = $database->query("SELECT id_post, idx, attachment FROM ForumPostAttachment WHERE censor=0");
$results = $query->fetchAll(PDO::FETCH_OBJ);
$query = NULL;

//print_r($results);

$upload_path = WWWROOTDIR.'/'.$UPLOAD_DIR;

foreach($results as $row) {

    if (file_exists($upload_path.'/'.$row->attachment)) {
	$image_ext = strtolower(substr(strrchr($row->attachment, '.'), 1));
	if ($image_ext == 'jpg' || $image_ext == 'jpeg' || $image_ext == 'gif' || $image_ext == 'png' || $image_ext == 'webp') {
	    if (file_exists($upload_path.'/small-'.$row->attachment)) {
		if ($image_ext == 'gif') {
		    for ($i = -1; $i < 1; $i++) {
			printf("Processing %s[%d]\n", $row->attachment, $i);
			system("convert -resize 299x299\> $upload_path/{$row->attachment}[$i] /tmp/small-{$row->attachment}.jpg");
			$ret = censor("/tmp/small-{$row->attachment}.jpg");
			unlink("/tmp/small-{$row->attachment}.jpg");
			if ($ret != 0) {
			    break;
			}
		    }
		} else {
			printf("Processing %s\n", $row->attachment);
//			system("convert $upload_path/small-{$row->attachment} -delete 1--1 /tmp/small-{$row->attachment}.jpg");
			system("convert -resize 299x299\> $upload_path/{$row->attachment} -delete 1--1 /tmp/small-{$row->attachment}.jpg");
			$ret = censor("/tmp/small-{$row->attachment}.jpg");
			unlink("/tmp/small-{$row->attachment}.jpg");
		}

		if ($ret > 0) {
		    $database->exec("UPDATE ForumPostAttachment SET censor=-1 WHERE id_post={$row->id_post} AND idx={$row->idx}");
		} else if ($ret == 0) {
		    $database->exec("UPDATE ForumPostAttachment SET censor=1 WHERE id_post={$row->id_post} AND idx={$row->idx}");
		}
	    }
	} else if ($image_ext == 'mp4' || $image_ext == 'mpg4' || $image_ext == 'mpeg4' || $image_ext == 'ogv' || $image_ext == 'webm') {
	    if (file_exists($upload_path.'/'.$row->attachment)) {
		printf("Processing %s\n", $row->attachment);
		system("ffmpeg -y -loglevel warning -sseof -3 -i $upload_path/{$row->attachment} -vf scale=299:-1 -update 1 -q:v 1 /tmp/small-{$row->attachment}.jpg");
		$ret = censor("/tmp/small-{$row->attachment}.jpg");
		unlink("/tmp/small-{$row->attachment}.jpg");

		if ($ret == 0) {
		    system("ffmpeg -y -loglevel warning -i $upload_path/{$row->attachment} -vf scale=299:-1 -vframes 1 /tmp/small-{$row->attachment}.jpg");
		    $ret = censor("/tmp/small-{$row->attachment}.jpg");
		    unlink("/tmp/small-{$row->attachment}.jpg");
		}

		if ($ret > 0) {
		    $database->exec("UPDATE ForumPostAttachment SET censor=-1 WHERE id_post={$row->id_post} AND idx={$row->idx}");
		} else if ($ret == 0) {
		    $database->exec("UPDATE ForumPostAttachment SET censor=1 WHERE id_post={$row->id_post} AND idx={$row->idx}");
		}
	    }
	} else {
	    $database->exec("UPDATE ForumPostAttachment SET censor=1 WHERE id_post={$row->id_post} AND idx={$row->idx}");
	}
    }
}

?>
