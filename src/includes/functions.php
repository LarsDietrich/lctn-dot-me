<?php

/**
 * Return a longUrl for a shortUrl
 *
 * @param $shortUrl - shortUrl to find longUrl for
 */
function getLongURL($shortUrl) {
	require ("sql.php");
	$_sql = new Sql();
	$_query = "select longurl from url where shorturl = '" . $shortUrl . "'";
	$_result = mysql_fetch_assoc($_sql->execute($_query));
	if (count($_result) > 0) {
		return $_result["longurl"];
	} else {
		return("");
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