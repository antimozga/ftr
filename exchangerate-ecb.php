<?php

function getRate($value) {
    return round(1 / $value, 4);
}

$content = file_get_contents('https://api.exchangeratesapi.io/latest?base=RUB');

if ($content === false) {
    printf("Cannot get weather data\n");
    exit;
}

$json = json_decode($content, true);

echo '<div class="rates" style="width:10em;">';
echo '<span class="w_title">Курс <a class="rates_link" href="https://www.ecb.europa.eu/stats/policy_and_exchange_rates/euro_reference_exchange_rates/html/index.en.html" target="_blank">ECB</a></span><br/>';
echo '<span style="width:4em;display:inline-block;">CNY 元</span>'.getRate($json['rates']['CNY']).'<br/>';
echo '<span style="width:4em;display:inline-block;">EUR €</span>'.getRate($json['rates']['EUR']).'<br/>';
echo '<span style="width:4em;display:inline-block;">PLN zł</span>'.getRate($json['rates']['PLN']).'<br/>';
echo '<span style="width:4em;display:inline-block;">TRY ₺</span>'.getRate($json['rates']['TRY']).'<br/>';
echo '<span style="width:4em;display:inline-block;">USD $</span>'.getRate($json['rates']['USD']).'<br/>';
echo '</div>';
?>
