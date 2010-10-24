<?php
require("includes/sql.php");

$result = getShortUrl($_GET["url"], $_GET["shorturl"]);

echo "<h3 class='info'>http://test.lctn.me/" . $result . "</h3>";

/**
 * Check is longUrl exists, return the shortened version from the database (or, if
 * shortUrl supplied, return the shortUrl).
 * 
 * Saves the shortUrl / longUrl combination is not exist.
 *
 * @param $longUrl - url to shorten
 * @param $shortUrl - custom short url to use
 */
function getShortUrl($longUrl, $shortUrl) {
	$_sql = new Sql();
	$_exists = longUrlExists($longUrl);

	if ((strlen($_exists) > 0)) {
		if (strlen($shortUrl) == 0) {
			return $_exists;
		} else {
			return $shortUrl;
		}
	}

	$_longUrl = mysql_escape_string($longUrl);

	if (strlen($shortUrl) == 0) {
		$id = rand(10000,99999);
		$shortUrl = base_convert($id,20,36);
	}

	$_now = microtime(true) * 1000;
	$_query = "insert into url (longurl, shorturl, openid, created) values ('" . $_longUrl . "','" . $shortUrl . "', 'TEST', $_now)";
	$_sql->execute($_query);
	return $shortUrl;
}

/**
 * Checks if there is an entry for the  url supplied, if so, returns shortUrl for it.
 *
 * @param $url - url to check
 * @return the shortUrl
 */
function longUrlExists($longUrl) {
	$_sql = new Sql();
	$_query = "select shorturl from url where longurl = '" . $longUrl . "'";
	$_result = mysql_fetch_assoc($_sql->execute($_query));
	if (count($_result) > 0) {
		return $_result["shorturl"];
	}
}


?>
