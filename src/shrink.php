<?php
require("includes/sql.php");
$result = getShortUrl($_GET["url"], $_GET["user"]);
//$result = getShortUrl("http://test.test.test", "TEST");

echo $result;

/**
 * Check is longUrl exists, return the shortened version from the database (or, if
 * shortUrl supplied, return the shortUrl).
 * 
 * Saves the shortUrl / longUrl combination if not exist.
 *
 * @param $longUrl - url to shorten
 * @param $user - user to capture against
 */
function getShortUrl($longUrl, $user) {
	$sql = new Sql();
	$exists = longUrlExists($longUrl);

	if ((strlen($exists) > 0)) {
		if (strlen($shortUrl) == 0) {
			return $exists;
		} else {
			return $shortUrl;
		}
	}
	
	$id = rand(10000,99999);
	$shortUrl = base_convert($id,20,36);

	$now = date("Y-m-d H:i:s");
	$query = "insert into url (longurl, shorturl, openid, created) values ('" . $longUrl . "','" . $shortUrl . "','" . $user . "', '" . $now . "')";
	$sql->execute($query);

	return $shortUrl;
}

/**
 * Checks if there is an entry for the  url supplied, if so, returns shortUrl for it.
 *
 * @param $longUrl - url to check
 * @return the shortUrl
 */
function longUrlExists($longUrl) {
	$sql = new Sql();
	$query = "select shorturl from url where longurl = '" . $longUrl . "'";
	$result = mysql_fetch_assoc($sql->execute($query));
	if (count($result) > 0) {
		return $result["shorturl"];
	}
}


?>
