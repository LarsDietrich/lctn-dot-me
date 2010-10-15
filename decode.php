<?php

require("includes/functions.php");

$url= mysql_escape_string($_GET["decode"]);
$longUrl = getLongUrl($url);

if ($longUrl == "") {
	$longUrl = "http://test.lctn.me/404.php";
} 

header("location:$longUrl");


?>
