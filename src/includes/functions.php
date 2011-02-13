<?php

/**
 * Return a longUrl for a shortUrl
 *
 * @param $shortUrl - shortUrl to find longUrl for
 */
function getLongURL($shortUrl) {
	require ("sql.php");
	$sql = new Sql();
	$query = "select longurl from url where shorturl = '" . $shortUrl . "'";
	$result = mysql_fetch_assoc($sql->execute($query));
	if (strlen($result["longurl"]) > 0) {
		return $result["longurl"];
	} else {
		$stub = '{"map": {"lat":"","lng":"","zoom":"14","type":"roadmap"}}';
		return base64_encode($stub);
	}
}

/**
 * Determine if a short url exists in the database
 *
 * @param $shortUrl - short url to check for
 *
 * @return boolean
 */
function urlExists($shortUrl) {
	require ("sql.php");
	$sql = new Sql();
	$query = "select shorturl from url where shorturl = '" . $shortUrl . "'";
	$result = mysql_fetch_assoc($sql->execute($query));
	if (count($result) > 0) {
		return true;
	} else {
		return false;
	}
}

?>