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
	
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>

		<script type="text/javascript" src="script/jx_compressed.js"> </script>

		<script type="text/javascript">
			var map;
			function load() { 
			  var myLatlng = new google.maps.LatLng(<?php echo $_GET["lat"] . "," . $_GET["lng"]; ?>);
			  var myOptions = {
			    zoom: 10,
			    center: myLatlng,
			    mapTypeId: google.maps.MapTypeId.ROADMAP
			  }
			  
			  map = new google.maps.Map(document.getElementById("map"), myOptions);

			  var marker = new google.maps.Marker({
			      position: myLatlng, 
			      map: map
			  });

			  
			  jx.load("nearest.php?lat=" + myLatlng.lat() + "&lng=" + myLatlng.lng(), function(data) { document.getElementById('nearest').innerHTML=data; });
			  
			  google.maps.event.addListener(map, 'click', function(event) {
				  jx.load("nearest.php?lat=" + event.latLng.lat() + "&lng=" + event.latLng.lng(), function(data) { document.getElementById('nearest').innerHTML=data; });
			  });
			  
			}
		</script>
	
	</head>
	<body onload="load()" onunload="GUnload()">
		
		<div class="container showgrid">
			<div class="span-11">
				<div id="map" style="width: 400px; height: 400px"></div>
			</div>
			<div class="span-13 last">
				<div id="nearest">
				</div>
			</div>
			<div class="span-24">
				BOTTOM
			</div>
		</div>


	</body>

</html>
