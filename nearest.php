<?php
$result = "<ul>";
$result = $result . "<h5 class='info'>";
$result = $result . "lat: " . $_GET["lat"];
$result = $result . "</h5>";
$result = $result . "<h5 class='info'>";
$result = $result . "lng: " . $_GET["lng"];
$result = $result . "</h5>";
echo $result;
?>
