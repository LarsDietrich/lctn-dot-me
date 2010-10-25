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
		<script type="text/javascript" src="js/jx_compressed.js"> </script>
		
		<script type="text/javascript">

			var map;
			var streetview;
			var panorama;
			var selectedLocation;
			var position;
			var sv = new google.maps.StreetViewService();
			var geocoder = new google.maps.Geocoder();
			var heading = 0;
			var pitch = 0;
			var zoom = 12;
			var input;
			
//			var initialLocation;
//			var browserSupportFlag =  new Boolean();

			function load() {
				document.getElementById('url').innerHTML="<h3 class='info'><i>Select a position on the map or search for it, then click Generate to get your short link</i></h2>";

				latitude = <?php if (isset($_GET["lat"])) { echo $_GET["lat"]; } else { echo "999"; }?>;
				longitude = <?php if (isset($_GET["lng"])) { echo $_GET["lng"]; } else { echo "999"; }?>;
				heading = <?php if (isset($_GET["heading"])) { echo $_GET["heading"]; } else { echo "0"; }?>;
				pitch = <?php if (isset($_GET["pitch"])) { echo $_GET["pitch"]; } else { echo "0"; }?>;
				zoom = <?php if (isset($_GET["zoom"])) { echo $_GET["zoom"]; } else { echo "12"; }?>;

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
			}

			// Show map at current position
			function showMap(position) {
				showMap(position.latitude, position.longitude);
			}
			
			// Show map at latitiude and longitude
			function showMap(lat, lng) { 

			  selectedLocation = new google.maps.LatLng(lat, lng);

			  var myOptions = {
			    zoom: zoom,
			    center: selectedLocation,
			    mapTypeId: google.maps.MapTypeId.ROADMAP,
			    streetViewControl: false
			  }
			  
			  map = new google.maps.Map(document.getElementById("map"), myOptions);

			  position = new google.maps.Marker({
			      position: selectedLocation, 
			      map: map
			  });
				
			  var panoOptions = {
			      navigationControl: true,
				  navigationControlOptions: {
				    style: google.maps.NavigationControlStyle.DEFAULT
				  }
			  };

  			  panorama = new google.maps.StreetViewPanorama(document.getElementById("streetview"), panoOptions);

			  google.maps.event.addListener(map, 'click', function(event) {
  			    selectedLocation = event.latLng;
				repositionMarker();
			  });

  			  google.maps.event.addListener(map, 'zoom_changed', function() {
				  zoom = map.getZoom();
  		  	  });

  			  google.maps.event.addListener(panorama, 'position_changed', function() {
				selectedLocation = event.latLng;
				repositionMarker();
  			  });

  			  google.maps.event.addListener(panorama, 'pov_changed', function() {
  			      heading = panorama.getPov().heading;
  			      pitch = panorama.getPov().pitch;
  			  });

  			  repositionMarker();
	  			  
			  //jx.load("more_info.php?lat=" + selectedLocation.lat() + "&lng=" + selectedLocation.lng(), function(data) { document.getElementById('nearest').innerHTML=data; });

			}

			// Moves the marker to a new location, saves the location to the database
			function repositionMarker() {
//				  jx.load("nearest.php?lat=" + selectedLocation.lat() + "&lng=" + selectedLocation.lng(), function(data) { document.getElementById('nearest').innerHTML=data; });
			  position.setMap(null);
			  position.setPosition(selectedLocation);
			  position.setMap(map);
			  sv.getPanoramaByLocation(selectedLocation, 50, processSVData);
			  clearMessage();
			  map.setCenter(selectedLocation);
			  reverseCodeLatLng();
			}

			// Try find street view data and load appropriate panel
			function processSVData(data, status) {
				if (status == google.maps.StreetViewStatus.OK) {
			      var markerPanoID = data.location.pano;
			      panorama.setPano(markerPanoID);
			      panorama.setPov({
			        heading: heading,
			        pitch: pitch,
			        zoom: 1
			      });
				  panorama.setVisible(true);
				  position.setMap(null);
				  selectedLocation = data.location.latLng;
				  position.setPosition(selectedLocation);
				  position.setMap(map);
			  	} else {
				  message = "Streetview not available at this location";
				  jx.load("message.php?message=" + message + "&type=error", function(data) { document.getElementById('message').innerHTML=data; });
			  }
			  
			}

			// Determines if supplied shortened url is available and sets it as the url to use
			function customUrl() {
				var url = document.getElementById("shorturl").value;
				jx.load("custom_url.php?url=" + url, function(data) {document.getElementById('custom_url_message').innerHTML=data; });
			}

			// Clears the message field
			function clearMessage() {
				document.getElementById('message').innerHTML="";
			}
		
			// Reverse geocodes the address, moves the marker to the new location
			function codeAddress() {
			  var address = document.getElementById("address").value;
  			  geocoder.geocode( { 'address': address}, function(results, status) {
			    if (status == google.maps.GeocoderStatus.OK) {
			      selectedLocation = results[0].geometry.location;
				  repositionMarker();
			    } else {
				  message = "Geocode was not successful for the following reason: " + status;
				  jx.load("message.php?message=" + message + "&type=error", function(data) { document.getElementById('message').innerHTML=data; });
			    }
			  });
			}

			 // returns the reverse geocoded address of the current location
			 function reverseCodeLatLng() {
				geocoder.geocode({'latLng': selectedLocation}, function(results, status) {
					output = "";
		  			if (status == google.maps.GeocoderStatus.OK) {
					    if (results.length > 0) {
					    	for ( var i = 0, len = results.length; i < 5 && i < len; ++i ){
						    	address = results[i].formatted_address;
								output = output + "<div class='info'>" + address + "</div>";
					    	}
					    } else {
			   		      output = "<div class='error'>No Addresses Found</div>";
					    }
				    } else {
				        message = "Geocoder failed due to: " + status;
					    jx.load("message.php?message=" + message + "&type=error", function(data) { document.getElementById('message').innerHTML=data; });
		  	        }
		  		    document.getElementById('other_info').innerHTML=output;
	  		    });
			  }

			  function shortenUrl() {
				  root = "http://test.lctn.me/";
				  longurl = root + "?lat=" + selectedLocation.lat() + "&lng=" + selectedLocation.lng() + "&heading=" + heading + "&pitch=" + pitch + "&zoom=" + zoom ;
				  shorturl = "";
				  jx.load("shrink.php?shorturl=" + shorturl + "&url=" + escape(longurl), function(data) { document.getElementById('url').innerHTML=data; });
				}

		</script>
	
	</head>
	
	<body onload="load()" onunload="GUnload()">
		<div class="container">
			<div class="span-24">
			&nbsp;
			</div>			
			<div class="span-10">
				<input type="text" class="title" name="address" id="address" value="Search for a place" onkeypress="if (event.keyCode == 13) { codeAddress(); }"/>
				<input style="height: 33px; padding-top: 2px" type="button" name="find" value="Find" onclick="codeAddress()"/>
			</div>
			<div class="span-14 last">
			&nbsp;
			</div>
<!-- 
			<div class="span-3">
				<h3>Custom URL</h3>
			</div>
			<div class="span-8">
				<input type="text" class="title" name="shorturl" id="shorturl" value="" onkeyup="customUrl()"/>
			</div>
			<div class="span-3 last">
				<div id="custom_url_message"><div id="custom_url_available"></div></div>
			</div>
 -->
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
				<div class="large">
				Name(s) for this place
				</div>
				<div id="other_info">
				</div>
				<div id="message">
				</div>
			</div>
			<div class="span-24">
			&nbsp;
			</div>
			<div class="span-22">
				<div id="url">
				</div>
			</div>
			<div class="span-2 last">
				<input style="height: 45px" type="button" name="generate" value="Generate" onclick="shortenUrl()"/>
			</div>
		</div>


	</body>

</html>
