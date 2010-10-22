<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
   "DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<title>lctn.me</title>

<link rel="stylesheet" href="css/blueprint/screen.css" type="text/css" media="screen, projection"/>
<link rel="stylesheet" href="css/blueprint/print.css" type="text/css" media="print"/> 
<!--[if lt IE 8]>
<link rel="stylesheet" href="css/blueprint/ie.css" type="text/css" media="screen, projection"/>
<![endif]-->

<script type="text/javascript" src="http://code.google.com/apis/gears/gears_init.js"> </script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"> </script>
<script type="text/javascript" src="script/jx_compressed.js"> </script>

<script type="text/javascript">

var initialLocation;
var browserSupportFlag =  new Boolean();

function initialize() {

  // Try W3C Geolocation (Preferred)
	if(navigator.geolocation) {
	    browserSupportFlag = true;
	    navigator.geolocation.getCurrentPosition(load);
  // Try Google Gears Geolocation
	} else if (google.gears) {
	    browserSupportFlag = true;
	    var geo = google.gears.factory.create('beta.geolocation');
	    geo.getCurrentPosition(load);
	} else {
		alert("Geolocation not supported");
	}
}

function load(position) { 
	alert("hello");
	
  var myLatlng = new google.maps.LatLng(position.latitude,position.longitude);
  var myOptions = {
    zoom: 3,
    center: myLatlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  }

  map = new google.maps.Map(document.getElementById("map"), myOptions);

  google.maps.event.addListener(map, 'click', function(event) {

	  url = buildUrl(event.latLng);
	  
	  jx.load("shrink.php?url=" + escape(url), function(data) { document.getElementById('url').innerHTML=data; });
	  
	  var marker = new google.maps.Marker({
	      position: event.latLng, 
	      map: map
	  });
  });
}
  
function buildUrl(location) {
	result = "http://test.lctn.me/map.php?lat=" + location.lat() + "&lng=" + location.lng();
	return result;
}

</script>

</head>
<body onload="initialize()" onunload="GUnload()">
	<div id="map" style="width: 400px; height: 400px"></div>
	<br/>
	<div id="url"></div>
</body>

</html>