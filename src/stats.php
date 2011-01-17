<?php

require ("includes/sql.php");

if (isset($_GET["do"])) {
	$do = $_GET["do"];

	if ($do == "show") {
		show();
	} else if ($do == "stat" && isset($_GET["lat"]) && isset($_GET["lng"])) {
		add();
	} else if ($do == "related" && isset($_GET["lat"]) && isset($_GET["lng"])) {
		related();
	} else {
		die("Invalid parameters.");
	}
}

/**
 * { "stat": [ {"longitude": "10.000", "latitude": "20.000" } ]
 */
function add() {
	$lng = $_GET["lng"];
	$lat = $_GET["lat"];
	$now = date("Y-m-d H:i:s");
	$query = "INSERT stats (created, longitude, latitude) VALUES ('" . $now . "', '" . $lng . "', '" . $lat . "')";
	$sql = new Sql();
	$sql->execute($query);
	echo("{\"result\":\"ok\"}");
}

function show() {
	$query = "SELECT created, latitude, longitude FROM stats order by created asc";
	$sql = new Sql();
	$results = $sql->execute($query);

	$result = array();

	$i = 0;

	while ($row = mysql_fetch_array($results, MYSQL_NUM)) {
		$arr = array('longitude' => $row[2], 'latitude' => $row[1], 'created' => $row[0]);
		$result[$i] = $arr;
		$i++;
	}

	echo "{\"total\":\"" . $i . "\", \"result\": " . json_encode($result) . "}";
}

function related() {
	$min_lng = $_GET["lng"] - 1;
	$max_lng = $_GET["lng"] + 1;
	$min_lat = $_GET["lat"] - 1;
	$max_lat = $_GET["lat"] + 1;
	
	$total = 0;

	if ($min_lng < -180) {
		$min_lng = -1 * ($min_lng + 180);
	}

	if ($min_lat < -90) {
		$min_lng = -1 * ($min_lng + 90);
	}

	if ($max_lng > 180) {
		$max_lng = -1 * ($max_lng - 180);
	}

	if ($min_lat > 90) {
		$min_lng = -1 * ($min_lng - 90);
	}

	$query = "SELECT created, latitude, longitude FROM stats WHERE longitude < " . $max_lng . " AND longitude > " . $min_lng . " AND latitude < " . $max_lat . " AND latitude > " . $min_lat . " ORDER BY created ASC";
	$sql = new Sql();
	$results = $sql->execute($query);

	$result = array();

	$i = 0;

	while ($row = mysql_fetch_array($results, MYSQL_NUM)) {
		$arr = array('longitude' => $row[2], 'latitude' => $row[1], 'created' => $row[0]);
		$result[$i] = $arr;
		$i++;
	}

	echo "{\"total\":\"" . $i . "\", \"result\": " . json_encode($result) . "}";
	
}

?>