<?php

require("includes/functions.php");

$url= mysql_escape_string($_GET["decode"]);
$longUrl = getLongUrl($url);

header("location:$longUrl");

?>
