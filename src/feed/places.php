<?php
$consumer_key = "5KROG0ZN3XWZU1UNKU35RU3RCCCSG2XGNBCWBNHMSNLAIGBS";
$consumer_secret = "U4POBPBX2NVLXPWSRNN4CYLXHLHH2BRR3RGWRR0DEHBBP4EW";
//$query = "https://api.foursquare.com/v2/venues/search?ll=40.7,-74&client_id=". $consumer_key . "&client_secret=" . $consumer_secret;
if ($_GET['category'] == "") {
	$query = "https://api.foursquare.com/v2/venues/search?ll=" . $_GET['lat'] . "," . $_GET['lng'] . "&limit=50&client_id=". $consumer_key . "&client_secret=" . $consumer_secret;
} else {
	$query = "https://api.foursquare.com/v2/venues/search?ll=" . $_GET['lat'] . "," . $_GET['lng'] . "&limit=50&categoryId=" . $_GET['category'] . "&client_id=". $consumer_key . "&client_secret=" . $consumer_secret;
}
$handle = fopen($query, "rb");
$contents = stream_get_contents($handle);
fclose($handle);
echo $contents;
?>
