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
		<script type="text/javascript" src="http://code.google.com/apis/gears/gears_init.js"></script>
		<script type="text/javascript" src="script/jx_compressed.js"> </script>

		<script type="text/javascript">

			var map;
			var streetview;
			var panorama;
			var latLng;
			var position;
			var sv = new google.maps.StreetViewService();

//			var initialLocation;
//			var browserSupportFlag =  new Boolean();

			function load() {
				document.getElementById('url').innerHTML="<h1 class='success'>Click anywhere on the map to select a location</h1>";

				latitude = <?php if (isset($_GET["lat"])) { echo $_GET["lat"]; } else { echo "999"; }?>;
				longitude = <?php if (isset($_GET["lng"])) { echo $_GET["lng"]; } else { echo "999"; }?>;

				if (latitude == 999 || longitude == 999) {
					locateMeAndShowMap();
				} else {
					showMap(latitude, longitude);
					showStreetViewMap(latitude, longitude);
				} 
			}
			
			// Locate me and load map
			function locateMeAndShowMap() {
				
			//TODO: Fix this, doesnt seem to work in FF
			  // Try W3C Geolocation (Preferred)
//				if(navigator.geolocation) {
//				    browserSupportFlag = true;
//				    navigator.geolocation.getCurrentPosition(showMap(position));
			//  // Try Google Gears Geolocation
//				} else if (google.gears) {
//				    browserSupportFlag = true;
//				    var geo = google.gears.factory.create('beta.geolocation');
//				    geo.getCurrentPosition(showMap);
//				} else {
//					alert("Geolocation not supported");
//				}
				showMap(0,0);
				showStreetViewMap(0,0);
			}

			// Show map at current position
			function showMap(position) {
				showMap(position.latitude, position.longitude);
			}
			
			// Show map at latitiude and longitude
			function showMap(lat, lng) { 

			  var myLatlng = new google.maps.LatLng(lat, lng);

			  latLng = myLatlng;
			  
			  var myOptions = {
			    zoom: 10,
			    center: myLatlng,
			    mapTypeId: google.maps.MapTypeId.ROADMAP
			  }
			  
			  map = new google.maps.Map(document.getElementById("map"), myOptions);

			  position = new google.maps.Marker({
			      position: myLatlng, 
			      map: map
			  });

			  jx.load("nearest.php?lat=" + myLatlng.lat() + "&lng=" + myLatlng.lng(), function(data) { document.getElementById('nearest').innerHTML=data; });

			  google.maps.event.addListener(map, 'click', function(event) {

				  latLng = event.latLng
				  url = "http://test.lctn.me/map.php?lat=" + event.latLng.lat() + "&lng=" + event.latLng.lng();
				  jx.load("shrink.php?url=" + escape(url), function(data) { document.getElementById('url').innerHTML=data; });
				  jx.load("nearest.php?lat=" + event.latLng.lat() + "&lng=" + event.latLng.lng(), function(data) { document.getElementById('nearest').innerHTML=data; });

				  setNewMarker(event.latLng)
				  
				  sv.getPanoramaByLocation(latLng, 50, processSVData);
				  clearMessage();
				  map.setCenter(event.latLng);
			  });

			  var panoOptions = {
			      navigationControl: true,
				  navigationControlOptions: {
				    style: google.maps.NavigationControlStyle.DEFAULT
				  }
			  };

  			  panorama = new google.maps.StreetViewPanorama(document.getElementById("streetview"), panoOptions);

  			  google.maps.event.addListener(panorama, 'position_changed', function() {
				setNewMarker(event.latLng);  			      
    			map.setCenter(event.latLng);
  			  });

			}

			function setNewMarker(latLng) {
			  position.setMap(null);
			  position.setPosition(latLng);
			  position.setMap(map);
			}

			
			function processSVData(data, status) {
  			  			  
			  if (status == google.maps.StreetViewStatus.OK) {
			      var markerPanoID = data.location.pano;
			      panorama.setPano(markerPanoID);
			      panorama.setPov({
			        heading: 270,
			        pitch: 0,
			        zoom: 1
			      });
				  panorama.setVisible(true);
				  setNewMarker(data.location.latLng);
			  	} else {
				  message = "Streetview not available at this location";
				  jx.load("message.php?message=" + message + "&type=error", function(data) { document.getElementById('message').innerHTML=data; });
			  }
			  
			}
			
			function clearMessage() {
				document.getElementById('message').innerHTML="";
			}

		</script>
	
	</head>
	
	<body onload="load()" onunload="GUnload()">
		
		<div class="container">
			<div class="span-24">
			&nbsp;
			</div>
			<div class="span-9">
				<div id="map" style="width: 350px; height: 350px"></div>
			</div>
			<div class="span-9">
				<div id="streetview" style="width: 350px; height: 350px"></div>
			</div>
			<div class="span-6 last">
				<div id="message">
				</div>
				<div id="nearest">
				</div>
			</div>
			<div class="span-24">
			&nbsp;
			</div>
			<div class="span-24">
			&nbsp;
			</div>
			<div class="span-24">
				<div id="url">
				</div>
			</div>
		</div>


	</body>

</html>
