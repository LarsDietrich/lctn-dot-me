<?php
require("includes/functions.php");
$input = $_GET["decode"];
$output = "";
//$input = "rse1";
$url= mysql_escape_string($input);
$longUrl = getLongUrl($url);
if ($longUrl == "") {
	$output = "error/404.php";
	die();
} else {
	$output = "index.php?q=" . $longUrl;
}

header("Location: $output");

?>