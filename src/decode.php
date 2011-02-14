<?php
require("includes/functions.php");
require("includes/sql.php");
if (isset($_GET["decode"])) {
	$input = $_GET["decode"];
	$output = "";
	//	$input = "rse1";
	$url = mysql_escape_string($input);
	$valid = urlExists($url);
	if (!$valid) {
		$output = "error/404.php";
	} else {
		$output = "index.php?url=" . $url;
	}
	header("Location: $output");
} else if (isset($_GET["url"])) {

	$output = getLongURL($_GET["url"]);
	echo $output;
}

?>