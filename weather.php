<?php
$query = "http://www.worldweatheronline.com/feed/weather.ashx?q=" . $_GET["lat"] . "," . $_GET["lng"] . "&format=json&key=f7d8c40a98131239100311";
$handle = fopen($query, "rb");
$contents = stream_get_contents($handle);
fclose($handle);
echo $contents;
?>
