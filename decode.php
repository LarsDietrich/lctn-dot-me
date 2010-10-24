<?php

require("includes/functions.php");

$url= mysql_escape_string($_GET["decode"]);
$_longUrl = getLongUrl($_url);

if ($_longUrl == "") {
	$_longUrl = "404.php";
} 

header("location:$_longUrl");


?>
