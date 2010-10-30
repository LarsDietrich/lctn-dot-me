<?php

$_text = "";
$_url = $_GET["url"];

if (strlen($_url) > 0) {
	$_result = shortUrlExists($_url);
	if ($_result) {
		$_text = $_text . "<div id='custom_url_available' class='error'>Unavailable</div>";
	} else {
		$_text = $_text . "<div id='custom_url_available' class='success'>Available</div>";
	}
}

echo $_text;

/**
 * Check is a given short url is already used.
 * 
 * @param $shortUrl
 */
function shortUrlExists($shortUrl) {
	require ("includes/sql.php");
	$_sql = new Sql();
	$_query = "select shortUrl from url where shortUrl = '" . $shortUrl . "'";
	$_result = mysql_fetch_assoc($_sql->execute($_query));
	if (strlen($_result["shortUrl"]) == 0) {
		return false;
	} else {
		return true;
	}
}

?>
