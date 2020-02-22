<?php
function show_gismeteo()
{
?>

<!-- Gismeteo informer START -->
<link rel="stylesheet" type="text/css" href="https://ost1.gismeteo.ru/assets/flat-ui/legacy/css/informer.min.css">
<div id="gsInformerID-LmDepdviaYr672" class="gsInformer" style="width:240px;height:100px">
    <div class="gsIContent">
        <div id="cityLink">
            <a href="https://www.gismeteo.ru/weather-tomsk-4652/" target="_blank" title="Погода в Томске">
                <img src="https://ost1.gismeteo.ru/assets/flat-ui/img/gisloader.svg" width="24" height="24" alt="Погода в Томске">
            </a>
            </div>
        <div class="gsLinks">
            <table>
                <tr>
                    <td>
                        <div class="leftCol">
                            <a href="https://www.gismeteo.ru/" target="_blank" title="Погода">
                                <img alt="Погода" src="https://ost1.gismeteo.ru/assets/flat-ui/img/logo-mini2.png" align="middle" border="0" width="11" height="16" />
                                <img src="https://ost1.gismeteo.ru/assets/flat-ui/img/informer/gismeteo.svg" border="0" align="middle" style="left: 5px; top:1px">
                            </a>
                            </div>
                            <div class="rightCol">
                                <a href="https://www.gismeteo.ru/weather-tomsk-4652/2-weeks/" target="_blank" title="Погода в Томске на 2 недели">
                                    <img src="https://ost1.gismeteo.ru/assets/flat-ui/img/informer/forecast-2weeks.ru.svg" border="0" align="middle" style="top:auto" alt="Погода в Томске на 2 недели">
                                </a>
                            </div>
                        </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<script async src="https://www.gismeteo.ru/api/informer/getinformer/?hash=LmDepdviaYr672"></script>
<!-- Gismeteo informer END -->

<?php
}
?>

