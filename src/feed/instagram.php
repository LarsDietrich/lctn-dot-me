<?php
//$query = "https://api.instagram.com/v1/media/search?lat=48.858844&lng=2.294351&distance=5000&client_id=7d1d783e68dd4dd5b7dbad1ad5316b6d";
//$query = "http://ws.geonames.org/findNearbyWikipediaJSON?lat=" . $_GET["lat"] . "&lng=" . $_GET["lng"] . "&radius=" . $_GET["range"] . "&maxRows=20";
$query = "https://api.instagram.com/v1/media/search?lat=" . $_GET["lat"] . "&lng=" . $_GET["lng"] . "&distance=" . $_GET["range"] . "&client_id=7d1d783e68dd4dd5b7dbad1ad5316b6d";
$handle = fopen($query, "rb");
$contents = stream_get_contents($handle);
fclose($handle);
echo $contents;
?>
