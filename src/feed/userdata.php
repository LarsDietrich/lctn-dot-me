<?php

require ("../includes/sql.php");

if (!isset($_GET["user"])) {
	echo "{\"error\":\"No User Supplied\"}";
	die();
} else {
	$user = $_GET["user"];
}

$query = "SELECT created, shorturl FROM url WHERE openid = '" . $user . "' ORDER BY created DESC";
$sql = new Sql();
$results = $sql->execute($query);
$result = array();
$i = 0;
while ($row = mysql_fetch_array($results, MYSQL_NUM)) {
	$arr = array('created' => $row[0], 'url' => $row[1]);
	$result[$i] = $arr;
	$i++;
}

echo "{\"result\":" . json_encode($result) . "}";

?>
