<?php
//$query = "http://ws.geonames.org/findNearbyWikipediaJSON?lat=40.7126&lng=-74.0076&radius=10&maxRows=15";
$query = "http://ws.geonames.org/findNearbyWikipediaJSON?lat=" . $_GET["lat"] . "&lng=" . $_GET["lng"] . "&radius=" . $_GET["range"] . "&maxRows=20";
$handle = fopen($query, "rb");
$contents = stream_get_contents($handle);
fclose($handle);
echo $contents;
?>
