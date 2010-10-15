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
	if (count($result) > 0) {
		return $result["longurl"];
	} else {
		return("");
	}
}

/**
 * Return a shortened Url and save to database.
 *
 * @param $longUrl - url to shorten
 */
function getShortUrl($longUrl) {
	require ("sql.php");
	$sql = new Sql();
	$exists = longUrlExists($longUrl);
	if (!($exists == "")) {
		return $exists;
	}
	$longurl = mysql_escape_string($longUrl);
	$id = rand(10000,99999);
	$shorturl = base_convert($id,20,36);
	$now = microtime(true) * 1000;
	$query = "insert into url (longurl, shorturl, openid, created) values ('" . $longurl . "','" . $shorturl . "', 'TEST', $now)";
	$sql->execute($query);
	return $shorturl;
}

/**
 * Checks if there is an entry for the  url supplied, if so, returns shortUrl for it.
 * 
 * @param $url - url to check
 */
function longUrlExists($longUrl) {
	$sql = new Sql();
	$query = "select shorturl from url where longurl = '" . $longUrl . "'";
	$result = mysql_fetch_assoc($sql->execute($query));
	if (count($result) > 0) {
		return $result["shorturl"];
	}
}

function redirectTo($longUrl)
{
	// change this to your domain
	header("Referer: http://lctn.me");
	// use a 301 redirect to your destination
	header("Location: $longUrl", TRUE, 301);
	exit;
}

function show404()
{
	// display/include your standard 404 page here
	echo "404 Page Not Found.";
	exit;
}


?>