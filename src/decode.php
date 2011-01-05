<!-- Decode the incoming short link and generate a long url -->
<?php

require("includes/functions.php");
//$_url="rse1";
$_url= mysql_escape_string($_GET["decode"]);
$_longUrl = getLongUrl($_url);

if ($_longUrl == "") {
	$result = "error/404.php";
	die();
} else {
	$result = "?q=" . base64_encode($_longUrl);
}

header("location:$result");

?>