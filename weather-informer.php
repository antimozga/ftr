<?php

require_once('config.php');

$database = new PDO("sqlite:" . DBASEFILE);

if (!$database) {
    printf("DataBase error\n");
    exit;
}

function getCelsius($kelvin) {
    return round($kelvin - 273.15, 1);
}

function getMmHg($hpa) {
    return round($hpa / 1.33322, 1);
}

function windDirection($wind) {
    if ($wind > 348.75 || $wind <= 11.25) {
	return "Северный";
    } else if ($wind <= 33.75) {
	return "ССВ";
    } else if ($wind <= 56.25) {
	return "СВ";
    } else if ($wind <= 78.75) {
	return "ВСВ";
    } else if ($wind <= 101.25) {
	return "Восточный";
    } else if ($wind <= 123.75) {
	return "ВЮВ";
    } else if ($wind <= 146.25) {
	return "ЮВ";
    } else if ($wind <= 168.75) {
	return "ЮЮВ";
    } else if ($wind <= 191.25) {
	return "Южный";
    } else if ($wind <= 213.75) {
	return "ЮЮЗ";
    } else if ($wind <= 236.25) {
	return "ЮЗ";
    } else if ($wind <= 258.75) {
	return "ЗЮЗ";
    } else if ($wind <= 281.25) {
	return "Западный";
    } else if ($wind <= 303.75) {
	return "ЗСЗ";
    } else if ($wind <= 326.25) {
	return "СЗ";
    } else if ($wind <= 348.75) {
	return "ССЗ";
    }

    return "*";
}

function windDesc($speed) {
    if ($speed < 0.3) {
	return "штиль";
    } else if ($speed < 1.6) {
	return "тихий";
    } else if ($speed < 3.4) {
	return "легкий";
    } else if ($speed < 5.5) {
	return "слабый";
    } else if ($speed < 8.0) {
	return "умеренный";
    } else if ($speed < 10.8) {
	return "свежий";
    } else if ($speed < 13.9) {
	return "сильный";
    } else if ($speed < 17.2) {
	return "крепкий";
    } else if ($speed < 20.8) {
	return "очень крепкий";
    } else if ($speed < 24.5) {
	return "шторм";
    } else if ($speed < 28.5) {
	return "сильный шторм";
    } else if ($speed < 33) {
	return "жесткий шторм";
    } else {
	return "ураган";
    }
}

function cloudsDesc($cloud) {
    if ($cloud < 11) {
	return "Ясно";
    } else if ($cloud < 25) {
	return "Малооблачно";
    } else if ($cloud < 50) {
	return "Переменная облачность";
    } else if ($cloud < 80) {
	return "Облачно с прояснениями";
    } else {
	return "Пасмурно";
    }
}

$group = array(
    '200' => 'Слабый дождь с грозой',
    '201' => 'Дождь с грозой',
    '202' => 'Сильный дождь с грозой',
    '210' => 'Слабая гроза',
    '211' => 'Гроза',
    '212' => 'Сильная гроза',
    '221' => 'Местами гроза',
    '230' => 'Гроза, слабая морось',
    '231' => 'Гроза, морось',
    '232' => 'Гроза, сильная морось',

    '300' => 'Слабая морось',
    '301' => 'Морось',
    '302' => 'Сильная морось',
    '310' => 'Слабо моросящий дождь',
    '311' => 'Моросящий дождь',
    '312' => 'Сильная изморось',
    '313' => 'Ливень и изморось',
    '314' => 'Сильный ливень и изморось',
    '321' => 'Моросящий дождь',

    '500' => 'Слабый дождь',
    '501' => 'Умеренный дождь',
    '502' => 'Сильный дождь',
    '503' => 'Очень сильный дождь',
    '504' => 'Ливень',
    '511' => 'Холодный дождь',
    '520' => 'Слабый ливень',
    '521' => 'Ливень',
    '522' => 'Сильный ливень',
    '531' => 'Местами ливень',

    '600' => 'Слабый снег',
    '601' => 'Снег',
    '602' => 'Сильный снег',
    '611' => 'Дождь со снегом',
    '612' => 'Слабый дождь со снегом',
    '613' => 'Ливень со снегом',
    '615' => 'Слабый дождь и снег',
    '616' => 'Дождь и снег',
    '620' => 'Слабый снегопад',
    '621' => 'Снегопад',
    '622' => 'Сильный снегопад',

    '701' => 'Дымка',
    '711' => 'Дым',
    '721' => 'Туман',
    '731' => 'Песчанный/пылевой вихрь',
    '741' => 'Густой туман',
    '751' => 'Песок',
    '761' => 'Пыль',
    '762' => 'Вулканический пепел',
    '771' => 'Шквал',
    '781' => 'Торнадо',

    '800' => 'Ясное небо',

    '801' => 'Незначительная облачность',
    '802' => 'Разбросанная облачность',
    '803' => 'Значительная облачность',
    '804' => 'Сплошная облачность',

);

$days = array('воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота');
$months = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');

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

$day = date('j');
$mday = date('w');
$month = date('n') - 1;
$year = date('Y');

if ($row) {
    echo '<div class="weather">';
    echo '<div style="float: left;">
<img src="https://openweathermap.org/img/wn/'.$row['weather_icon'].'@2x.png" alt=""/>
<div style="text-align: center; font-size: smaller;"><a href="https://openweathermap.org/city/1489425" target="_blank">OpenWeather</a></div>
</div>';
    echo '<div style="float: right;">';
    echo '<span class="w_title">Погода</span>
<span class="w_temp" id="w_temp">
'.getCelsius($row['temp']).' &#176;C</span><br/>
<span id="w_sign">'.$group[$row['weather_id']].'<br/>'.cloudsDesc($row['clouds_all']).'</span><br/>
<span id="w_wind">Ветер '.windDesc($row['wind_speed']).', '.round($row['wind_speed'], 1).' м/с '.windDirection($row['wind_deg']).'</span><br/>
<span id="w_press">Давление '.getMmHg($row['pressure']).' ммрс</span><br/>
<span id="w_humid">Влажность '.round($row['humidity']).'&#37;</span><br/>
<b id="curr_day">'.$days[$mday].'</b><br/>
<span id="curr_date">'.$day.' '.$months[$month].' '.$year.'</span>';
    echo '</div>';
    echo '</div>';
}

?>
