<?php

require ("includes/sql.php");

if (!isset($_GET["stat"])) {
	die("Must specify stat parameter.");
}

//$_stat = "TEST";
/** 
 * { "stat": [ {"longitude": "10.000", "latitude": "20.000" } ]
 */
$_stat = $_GET["stat"];
$_query = "INSERT stats (date, stat) VALUES ('" . microtime(true) . "', '" . $_stat . "')";
$_sql = new Sql();
$_sql->execute($_query);

?>