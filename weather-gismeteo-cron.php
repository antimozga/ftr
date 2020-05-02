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

$query = "CREATE TABLE IF NOT EXISTS GisMeteoTable " . "(id INTEGER PRIMARY KEY," . " time INTEGER," . " temp REAL," . " temp_feel REAL," . " desc NVARCHAR," . " wind INTEGER," . " wind_desc NVARCHAR," . " pressure INTEGER," . " humidity INTEGER," . " icon NVARCHAR)";
$database->exec($query);

$sth = $database->prepare("SELECT * FROM GisMeteoTable ORDER BY id DESC LIMIT 1");
$sth->execute();
$row = $sth->fetch();

if ($row) {
    $last_access = $row['time'];

    $database->exec("DELETE FROM GisMeteoTable WHERE time <= strftime('%s', datetime('now', '-1 hour'))");

    $now = time();
    if ($now - $last_access < 60) {
        $delay = 60 - ($now - $last_access);
        printf("delay $delay for 1 minute period\n");
        sleep($delay);
    }
}

$options = array(
    'http' => array(
        'user_agent' => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36\r\n"
    )
);
$content = file_get_contents('https://www.gismeteo.ru/weather-tomsk-4652/now/', false, stream_context_create($options));

if ($content === false) {
    printf("Cannot get weather data\n");
    exit();
}

// echo $content;
function findClass($dom, $tag, $class)
{
    $elements = $dom->getElementsByTagName($tag);

    foreach ($elements as $element) {
        if (strpos($element->getAttribute('class'), $class) !== false) {
            return $element;
        }
    }

    return false;
}

function stripSpaces($str)
{
    return preg_replace('/\s+/', '', $str);
}

function strToNum($str)
{
    return str_replace(array(
        '−',
        ','
    ), array(
        '-',
        '.'
    ), $str);
}

$dom = new DomDocument();
$dom->loadHTML($content);

$class = findClass($dom, 'div', 'forecast_now');

$desc = "";
$temp = - 1;
$temp_feel = - 1;
$wind_speed = - 1;
$wind_desc = "";
$pressure = - 1;
$humidity = - 1;
$icon = "";

if ($class !== false) {
    printf("forecast_now\n");

    $t_desc = findClass($class, 'div', 'tooltip');
    if ($t_desc !== false) {
        $desc = stripSpaces($t_desc->getAttribute('data-text'));

        $t_temp = findClass($t_desc, 'span', 'unit_temperature_c');
        if ($t_temp !== false) {
            $temp = strToNum(stripSpaces($t_temp->textContent));
        }

        $t_feel = findClass($t_desc, 'span', 'tab-weather__feel-value');
        if ($t_feel !== false) {
            $t_feel_c = findClass($t_feel, 'span', 'unit_temperature_c');
            if ($t_feel_c !== false) {
                $temp_feel = strToNum(stripSpaces($t_feel_c->textContent));
            }
        }

        $t_icon = findClass($t_desc, 'div', 'tab-icon');
        if ($t_icon !== false) {
            $t_icon_img = findClass($t_icon, 'div', 'img');
            if ($t_icon_img !== false) {
                foreach ($t_icon_img->childNodes as $node) {
                    if ($node->nodeName == "svg") {
                        $svg_str = $dom->saveXML($node);
                        $icon = md5($svg_str) . '.svg';
                        $img_path = dirname($_SERVER['argv']['0']) . '/images/gismeteo/' . $icon;
                        if (! file_exists($img_path)) {
                            file_put_contents($img_path, $svg_str);
                        }
                        break;
                    }
                }
            }
        }
    }

    $t_wind = findClass($class, 'div', 'nowinfo__item_wind');
    if ($t_wind !== false) {
        $t_wind_speed = findClass($t_wind, 'div', 'unit_wind_m_s');
        if ($t_wind_speed !== false) {
            $t_wind_speed_val = findClass($t_wind_speed, 'div', 'nowinfo__value');
            if ($t_wind_speed_val !== false) {
                $wind_speed = strToNum(stripSpaces($t_wind_speed_val->textContent));
            }
            $t_wind_measure = findClass($t_wind_speed, 'div', 'nowinfo__measure_wind');
            if ($t_wind_measure !== false) {
                $wind_desc = preg_replace('/м\/с/', '', stripSpaces($t_wind_measure->textContent));
            }
        }
    }

    $t_press = findClass($class, 'div', 'nowinfo__item_pressure');
    if ($t_press !== false) {
        $t_press_mm_hg = findClass($t_press, 'div', 'unit_pressure_mm_hg_atm');
        if ($t_press_mm_hg !== false) {
            $t_press_mm_hg_val = findClass($t_press_mm_hg, 'div', 'nowinfo__value');
            if ($t_press_mm_hg_val !== false) {
                $pressure = strToNum(stripSpaces($t_press_mm_hg_val->textContent));
            }
        }
    }

    $t_hum = findClass($class, 'div', 'nowinfo__item_humidity');
    if ($t_hum !== false) {
        $t_hum_val = findClass($t_hum, 'div', 'nowinfo__value');
        if ($t_hum_val !== false) {
            $humidity = strToNum(stripSpaces($t_hum_val->textContent));
        }
    }
}

// echo 'desc '.$desc."\n";
// echo 'temp '.$temp."\n";
// echo 'temp feel '.$temp_feel."\n";
// echo 'wind '.$wind_speed."\n";
// echo 'wind desc '.$wind_desc."\n";
// echo 'press '.$pressure."\n";
// echo 'hum '.$humidity."\n";

$tim = time();

$database->exec("INSERT INTO GisMeteoTable" . " (time, temp, temp_feel, desc, wind, wind_desc, pressure, humidity, icon)" . " VALUES($tim, $temp, $temp_feel, '$desc', $wind_speed, '$wind_desc', $pressure, $humidity, '$icon')");

?>
