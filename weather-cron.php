<?php

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

$database = new PDO("sqlite:" . DBASEFILE);

if (!$database) {
    printf("DataBase error\n");
    exit;
}

$query = "CREATE TABLE IF NOT EXISTS WeatherTable ".
	 "(id INTEGER PRIMARY KEY,".
	 " time INTEGER,".
	 " loc_id INTEGER,".
	 " sunrise INTEGER,".
	 " sunset INTEGER,".
	 " weather_id INTEGER,".
	 " weather_icon NVARCHAR,".
	 " temp REAL,".
	 " feels_like REAL,".
	 " temp_min REAL,".
	 " temp_max REAL,".
	 " pressure REAL,".
	 " humidity REAL,".
	 " wind_speed REAL,".
	 " wind_deg REAL,".
	 " visibility INTEGER,".
	 " clouds_all INTEGER)";
    $database->exec($query);

$sth = $database->prepare("SELECT * FROM WeatherTable ORDER BY id DESC LIMIT 1");
$sth->execute();
$row = $sth->fetch();

if ($row) {
    $last_access = $row['time'];

    $database->exec("DELETE FROM WeatherTable WHERE time <= strftime('%s', datetime('now', '-1 hour'))");

    $now = time();
    if ($now - $last_access < 60) {
	$delay = 60 - ($now - $last_access);
	printf("delay $delay for 1 minute period\n");
	sleep($delay);
    }
}

$content = file_get_contents('https://api.openweathermap.org/data/2.5/weather?id=1489425&appid=0757a4f613d641a36db16a179d62dd70');

if ($content === false) {
    printf("Cannot get weather data\n");
    exit;
}

$json = json_decode($content, true);

$tim = time();
$loc_id		= $json['id'];
$sunrise	= $json['sys']['sunrise'];
$sunset		= $json['sys']['sunset'];
$weather_id	= $json['weather']['0']['id'];
$weather_icon	= $json['weather']['0']['icon'];
$temp		= $json['main']['temp'];
$feels_like	= $json['main']['feels_like'];
$temp_min	= $json['main']['temp_min'];
$temp_max	= $json['main']['temp_max'];
$pressure	= $json['main']['pressure'];
$humidity	= $json['main']['humidity'];
$wind_speed	= $json['wind']['speed'];
$wind_deg	= $json['wind']['deg'];

//$visibility	= -1;
//$clouds_all	= -1;

if (isset($json['visibility'])) {
    $visibility	= $json['visibility'];
} else {
    $visibility	= -1;
}

if (isset($json['clouds']['all'])) {
    $clouds_all	= $json['clouds']['all'];
} else {
    $clouds_all = -1;
}

$database->exec("INSERT INTO WeatherTable".
	" (time, loc_id, sunrise, sunset, weather_id, weather_icon, temp,".
	" feels_like, temp_min, temp_max, pressure, humidity, wind_speed,".
	" wind_deg, visibility, clouds_all) VALUES(".
	" $tim, $loc_id, $sunrise, $sunset, $weather_id, '$weather_icon', $temp,".
	" $feels_like, $temp_min, $temp_max, $pressure, $humidity, $wind_speed,".
	" $wind_deg, $visibility, $clouds_all)");

?>
