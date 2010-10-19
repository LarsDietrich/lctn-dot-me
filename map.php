<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
   "DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

	<head>
	    <title>lctn.me</title>
	
		<script type="text/javascript"
			src="http://maps.google.com/maps/api/js?sensor=true">
		</script>
		
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
		  
			}
		  






			
		</script>
	
	</head>
	<body onload="load()" onunload="GUnload()">
		<div id="map" style="width: 640px; height: 480px"></div>
	</body>

</html>
