<?php
$query = "http://ws.geonames.org/timezoneJSON?lat=" . $_GET["lat"] . "&lng=" . $_GET["lng"] . "&style=full";
$handle = fopen($query, "rb");
$contents = stream_get_contents($handle);
fclose($handle);
echo $contents;
?>
