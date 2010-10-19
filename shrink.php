<?php
require("includes/functions.php");
$result = getShortUrl($_GET["url"]);
echo "<h1 class='success'>http://test.lctn.me/" . $result . "</h1>";
?>
