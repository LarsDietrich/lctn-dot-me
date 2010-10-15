<?php

require("includes/functions.php");

$url= mysql_escape_string($_GET["decode"]);
$longUrl = getLongUrl($url);

if ($longUrl == "") {
	$longUrl = "404.php";
} 

header("location:$longUrl");


?>
