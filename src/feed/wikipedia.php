<?php
//$query = "http://api.wikilocation.org/articles?lat=" . $_GET["lat"] . "&lng=" . $_GET["lng"] . "&radius=" . $_GET["radius"];
$query = "http://ws.geonames.org/findNearbyWikipediaJSON?lat=" . $_GET["lat"] . "&lng=" . $_GET["lng"] . "&radius=" . $_GET["range"] . "&maxRows=32";
$handle = fopen($query, "rb");
$contents = stream_get_contents($handle);
fclose($handle);
echo $contents;
?>
