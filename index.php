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
		<script type="text/javascript" src="js/jxs.js"> </script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>

		<script type="text/javascript" src="js/custom/locate.js"> </script>
		<script type="text/javascript" src="js/custom/tweets.js"> </script>
		<script type="text/javascript" src="js/custom/wikipedia.js"> </script>
		<script type="text/javascript" src="js/custom/ajax.js"> </script>
		
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
			var twitter_filter = "";
			
			function load() {
				latitude = <?php if (isset($_GET["lat"])) { echo $_GET["lat"]; } else { echo "999"; }?>;
				longitude = <?php if (isset($_GET["lng"])) { echo $_GET["lng"]; } else { echo "999"; }?>;
				heading = <?php if (isset($_GET["heading"])) { echo $_GET["heading"]; } else { echo "0"; }?>;
				pitch = <?php if (isset($_GET["pitch"])) { echo $_GET["pitch"]; } else { echo "0"; }?>;
				zoom = <?php if (isset($_GET["zoom"])) { echo $_GET["zoom"]; } else { echo "12"; }?>;

				if (latitude == 999 || longitude == 999) {
					locateMe();
					if (!locateResponse.success)  {
						setMessage("Geolocation not supported", "error");
					}
					selectedLocation = locateResponse.location;
				} else {
  				    selectedLocation = new google.maps.LatLng(latitude, longitude);
				} 

				showMap();
			}

			// show the map
			function showMap() { 
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

		      setupListeners();
  			  
  			  repositionMarker();
	  			  
			}

		   function setupListeners() {
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
			}
			// Moves the marker to a new location specified by selectedLocation. Refreshes screen for anything
			// that uses the location (like tweets and streetview)
			function repositionMarker() {
			  position.setMap(null);
			  position.setPosition(selectedLocation);
			  position.setMap(map);
			  sv.getPanoramaByLocation(selectedLocation, 70, processSVData);
			  clearMessage();
			  map.setCenter(selectedLocation);
			  refreshWhat();
			  scroll(0,0);
			  reverseCodeLatLng();
			}

			// Try find street view data and load appropriate panel and set selected location
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
				  setMessage("Streetview not available at this location, try clicking on a road, or searching for another place", "info");
			  }
			  
			}

//			// Determines if supplied shortened url is available and sets it as the url to use
//			function customUrl() {
//				var url = document.getElementById("shorturl").value;
//				jx.load("custom_url.php?url=" + url, function(data) {document.getElementById('custom_url_message').innerHTML=data; });
//			}

			// Clears the message field
			function clearMessage() {
				setMessage("This is an experimental app, a fun idea I've been toying with. <a href='mailto:rick@tonoli.co.za?subject=Tell me more about lctn.me'>Email me</a> for more info.", "info");
			}

			function setMessage(message, type) {
				jx.load("message.php?message=" + message + "&type=" + type, function(data) { document.getElementById('message').innerHTML=data; });
			}

			function locationFromAddr() {
				var address = document.getElementById("address").value;
				geocoder.geocode( { 'address': address}, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
				      selectedLocation = results[0].geometry.location;
				      repositionMarker();
				    } else {
					  setMessage("Unable to determine location from address: " + status, "error");
				    }
				});
			}
			
			// Reverse geocodes the address, moves the marker to the new location
			function locationFromAddress(address) {
				document.getElementById("address").value = address;
				locationFromAddr();
			}

			 // returns the reverse geocoded address of the current location
			 function reverseCodeLatLng() {
				geocoder.geocode({'latLng': selectedLocation}, function(results, status) {
					output = "";
		  			if (status == google.maps.GeocoderStatus.OK) {
					    if (results.length > 0) {
					    	address = results[0].formatted_address;
							document.getElementById("address").value = address;
					    } else {
			   		      setMessage("No Addresses Found");
					    }
				    } else {
					    setMessage("Unable to determined address: " + status, "error");
				    }
	  		    });
			  }
			  
			  // Determine the shortened URL based on the current location, saves to DB
			  function shortenUrl() {
				  root = "http://test.lctn.me/";
				  longurl = root + "?lat=" + selectedLocation.lat() + "&lng=" + selectedLocation.lng() + "&heading=" + heading + "&pitch=" + pitch + "&zoom=" + zoom ;
				  shorturl = "";
				  jx.load("shrink.php?shorturl=" + shorturl + "&url=" + escape(longurl), function(data) { updateUrl(data); updateSocialBar(data); });
			  }

			  // update the url block with the supplied link, should be a shortened link
			  function updateUrl(link) {
				  data = "<h3 class='info'>http://test.lctn.me/" + link + "</h3>"; 
				  document.getElementById("url").innerHTML=data;
		      }

			  // update the social bar with new shortened link
			  function updateSocialBar(link) {
					data = "<a href=\"http://twitter.com/home/?status=";
					data = data + "http://test.lctn.me/" + link + "\"";
					data = data + " target=\"_blank\"><img height=\"80px\" width=\"80px\" border=\"0\" src=\"images/twitter.jpg\" alt=\"Twitter\"></img></a>";
					document.getElementById("twitter").innerHTML=data;
					data = "<a href=\"http://www.facebook.com/sharer.php?u=";
					data = data + "http://test.lctn.me/" + link + "\"";
					data = data + " target=\"_blank\"><img height=\"80px\" width=\"80px\" border=\"0\" src=\"images/facebook.jpg\" alt=\"Facebook\"></img></a>";
					document.getElementById("facebook").innerHTML=data;
			  }

  			  	function refreshWhat() {
  				  if (!(selectedLocation.lat() == 0 || selectedLocation.lng() == 0)) {
					document.getElementById("tweet_stream").innerHTML="Loading..";
					document.getElementById("wiki_stream").innerHTML="Loading..";
    				tweets(selectedLocation, document.getElementById("filter").value, document.getElementById("range").value);
  				  	articles(selectedLocation, document.getElementById("filter").value, document.getElementById("range").value);
  				  }
  			  	}
		</script>
	</head>

	<body onload="load()" onunload="GUnload()">
		<div class="container">
			<div class="span-24 error">
				<center><h3>ALPHA RELEASE</h3></center>
			</div>			
			<div class="span-10 colborder">
				<input type="text" class="title" name="address" id="address" value="" onkeypress="if (event.keyCode == 13) { locationFromAddr();}"/>
				<input style="height: 33px; padding-top: 2px" type="button" name="find" value="Find" onclick="locationFromAddr();"/>
			</div>
			<div class="span-13 last">
				<div id="message">
				</div>
			</div>
			<div class="span-24"><hr/></div>
			
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
			<div class="span-12">
				<div id="map" style="width: 400px; height: 400px"></div>
			</div>
			<div class="span-12 last">
				<div id="streetview" style="width: 400px; height: 400px"></div>
			</div>
			<div class="span-11 colborder">
			&nbsp;
			</div>
			<div class="span-12 last">
			&nbsp;
			</div>
			<div class="span-24"><hr/></div>
			<div class="span-22">
				<div id="url">
					<h3 class='info'><i>Select a position on the map or search for it, orientate your streetview (if available) and click Generate to get a short link</i></h3>
				</div>
			</div>
			<div class="span-2 last">
				<input style="height: 45px" type="button" name="generate" value="Generate" onclick="shortenUrl();"/>
			</div>
			<div class="span-2">
				<div id="twitter">
					<a href="http://twitter.com/home/?status=" target="_blank">
						<img height="80px" width="80px" border="0" src="images/twitter.jpg" alt="Twitter"></img>
					</a>
				</div>
			</div>
			<div class="span-2">
				<div id="facebook">
					<a href="http://www.facebook.com/sharer.php?u=" target="_blank">
						<img height="80px" width="80px" border="0" src="images/facebook.jpg" alt="Facebook"></img>
					</a>
				</div>
			</div>
			<div class="span-18 last">
			</div>
			<div class="span-24">&nbsp;</div>
			<div class="span-24"><hr/></div>
			<div class="large info span-23">
				<div>
				<center>What's Happening in the Area?</center>
					<center>
						Search for <input type="text" name="filter" id="filter" onkeypress="if (event.keyCode == 13) { refreshWhat(); }"/>
						in <input type="text" name="range" id="range" value="1" onkeypress="if (event.keyCode == 13) { refreshWhat(); }"/> km
						<input type="button" id="filter_now" name="filter_now" value="Go" onclick="refreshWhat();"/>
					</center>
				</div>
			</div>
			<div class="span-11 colborder">
				<center><span class="large">Twitter</span></center>
				<div id="tweet_stream"></div>
			</div>
			<div class="span-10 last colborder">
				<center><span class="large">Wikipedia</span></center>
				<div id="wiki_stream">Loading..</div>
			</div>

		</div>
	</body>

</html>
