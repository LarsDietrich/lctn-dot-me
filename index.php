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
		<link rel="stylesheet" href="css/layout.css" type="text/css"/>
		
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
		<script type="text/javascript" src="http://code.google.com/apis/gears/gears_init.js"></script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
		
		<!-- http://lctn -->
		<script type="text/javascript" src="https://www.google.com/jsapi?key=ABQIAAAANICyL01ax9PqYKeJwtOXfxTh05SPp9XRgWyeCyc0ee48nkavlxTTkteFyCb29mhFOfEeXVaj-F6hAw"></script>
		
		<script type="text/javascript" src="js/jxs.js"> </script>

		<script type="text/javascript" src="js/custom/locate.js"> </script>
		<script type="text/javascript" src="js/custom/tweets.js"> </script>
		<script type="text/javascript" src="js/custom/weather.js"> </script>
		<script type="text/javascript" src="js/custom/wikipedia.js"> </script>
		
		<script type="text/javascript">
			
			// reference to the main map
			var map;
			// reference to the streetview
			var streetview;
			// reference to the panorama of the streetview			
			var panorama;

			// stores current location (google.maps.LatLng)
			var selectedLocation;

			// reference to the position marker on the map (google.maps.Marker)
			var positionMarker;

			// reference to the streetview service for looking up details
			var streetViewService = new google.maps.StreetViewService();
			
			// reference to the geocode for coding / decoding addresses
			var geocoder = new google.maps.Geocoder();

			// information on the streetview/map POV
			var heading = 0;
			var pitch = 0;
			var zoom = 12;

			// tweet array to hold all tweets in area
			var listOfTweets = [];
			var tweetsPerPage = 8;
			
			// wiki array to hold all wikis in area
			var listOfWikis = [];
			var wikisPerPage = 10;

			// weather array
			var listOfWeather = [];
			
			// load the necessary data, parse command line for location information and show map
			function load() {
				updateSocialBar("");
				latitude = <?php if (isset($_GET["lat"])) { echo $_GET["lat"]; } else { echo "999"; }?>;
				longitude = <?php if (isset($_GET["lng"])) { echo $_GET["lng"]; } else { echo "999"; }?>;
				heading = <?php if (isset($_GET["heading"])) { echo $_GET["heading"]; } else { echo "0"; }?>;
				pitch = <?php if (isset($_GET["pitch"])) { echo $_GET["pitch"]; } else { echo "0"; }?>;
				zoom = <?php if (isset($_GET["zoom"])) { echo $_GET["zoom"]; } else { echo "12"; }?>;

				if (latitude == 999 || longitude == 999) {
					var locateResponse = locateMe();
					if (!locateResponse.success)  {
						setMessage("Geolocation not supported", "error");
					}
					selectedLocation = locateResponse.location;
				} else {
  				    selectedLocation = new google.maps.LatLng(latitude, longitude);
				} 
				showMap();
			}

			function clearElements() {
				document.getElementById("address").value="";
				document.getElementById("url").value="";
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
			  positionMarker = new google.maps.Marker({
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

		   // Various listeners to catch changes on the map(s)
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
				positionMarker.setMap(null);
				positionMarker.setPosition(selectedLocation);
				positionMarker.setMap(map);
				streetViewService.getPanoramaByLocation(selectedLocation, 70, processSVData);
				map.setCenter(selectedLocation);
				updateTwitterLocationInformation();
				updateWikiLocationInformation();
				updateWeatherLocationInformation();
				reverseCodeLatLng();
				scroll(0,0);
				document.getElementById("url").value="";
				setMessage("Location updated. See bottom of page for area specific tweets / wiki's", "success");
			}

			// Try find street view data and load appropriate panorama panel and set selectedLocation
			function processSVData(data, status) {
				if (status == google.maps.StreetViewStatus.OK) {
			      var markerPanoID = data.location.pano;
			      panorama.setPano(markerPanoID);
			      panorama.setPov({
			        heading: heading,
			        pitch: pitch,
			        zoom: 1
			      });
				  positionMarker.setMap(null);
				  selectedLocation = data.location.latLng;
				  positionMarker.setPosition(selectedLocation);
				  positionMarker.setMap(map);
				  panorama.setVisible(true);
			  	} else {
				  setMessage("Streetview not available at this location, try clicking on a nearby road", "notice");
				  panorama.setVisible(false);
				}
			}

			function setMessage(message, type) {
				jx.load("message.php?message=" + message + "&type=" + type, function(data) { document.getElementById('message').innerHTML=data; });
			}

			// Sets the selectedLocation based on address in address box
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

			 // Sets the address box based on selectedLocation
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
				root = "http://" + top.location.host + "/";
				longurl = root + "?lat=" + selectedLocation.lat() + "&lng=" + selectedLocation.lng() + "&heading=" + heading + "&pitch=" + pitch + "&zoom=" + zoom ;
				shorturl = "";
				jx.load("shrink.php?shorturl=" + shorturl + "&url=" + escape(longurl), function(data) { updateUrl(root + data); updateSocialBar(root + data); });
				setMessage("Short url created, send this to your friends and it will reload the maps as is.", "success");
			}

			// Update the url block with the supplied link, should be a shortened link
			function updateUrl(link) {
				document.getElementById("url").value=link;
			}

			  // Update the social bar with new shortened link
			function updateSocialBar(link) {
				var size="70px";
				var output = "";
				  
				output += "<a href=\"http://twitter.com/home/?status=";
				output += link + "\"";
				output += " target=\"_blank\"><img height=\"" + size + "\" width=\"" + size + "\" border=\"0\" src=\"images/twitter.jpg\" title=\"Tweet it\" alt=\"Twitter\"></img></a>";

				output += "<a href=\"http://www.facebook.com/sharer.php?u=";
				output += link + "\"";
				output += " target=\"_blank\"><img height=\"" + size + "\" width=\"" + size + "\" border=\"0\" src=\"images/facebook.jpg\" title=\"Add to Facebook\" alt=\"Facebook\"></img></a>";

				output += "<a href=\"http://del.icio.us/post?url=";
				output += link + "\"";
				output += " target=\"_blank\"><img height=\"" + size + "\" width=\"" + size + "\" border=\"0\" src=\"images/delicious.jpg\" title=\"Add to Del.icio.us\" alt=\"Del.icio.us\"></img></a>";

				output += "<a href=\"mailto:?subject=";
				output += link + "\"";
				output += "><img height=\"" + size + "\" width=\"" + size + "\" border=\"0\" src=\"images/email.jpg\" title=\"Send by email\" alt=\"Email\"></img></a>";
				document.getElementById("social").innerHTML=output;
				
			}

			function updateWeatherLocationInformation() {
				if (!(selectedLocation.lat() == 0 || selectedLocation.lng() == 0)) {
					document.getElementById("weather_stream").innerHTML="Searching..";
					getWeather(selectedLocation, 5);
				}
			}

 		  	// Load the weather display based on whats in tweets array
			function updateWeatherDisplay() {
				var output = "<table><tr>";
				for (i = 0; i < listOfWeather.length; i++) {
					output += listOfWeather[i];
				}				
				output += "</tr></table>";
				document.getElementById("weather_stream").innerHTML = output;
 		  	}
			
 		  	function updateTwitterLocationInformation() {
				if (!(selectedLocation.lat() == 0 || selectedLocation.lng() == 0)) {
					document.getElementById("tweet_stream").innerHTML="Searching..";
					getTweets(selectedLocation, document.getElementById("filter").value, document.getElementById("tweet_range").value);
				}
			}

 		  	// Load the twitter display based on whats in tweets array
			function updateTwitterDisplay(page) {
				var startItem = (page - 1) * tweetsPerPage;
				var endItem = page * tweetsPerPage;
				var output = "";

				if (endItem > listOfTweets.length) {
					endItem = listOfTweets.length;
				}
				
				for (i = startItem; i < endItem; i++) {
					output += listOfTweets[i];	
				}				
				document.getElementById("tweet_stream").innerHTML = output;
				updateTwitterPaging(page);
 		  	}

			function updateTwitterPaging(page) {
				var totalPages = Math.round(listOfTweets.length / tweetsPerPage);
				if (totalPages < (listOfTweets.length / tweetsPerPage)) {
					totalPages++;
				}
				var next = "&nbsp;";
				var previous = "&nbsp;";
				
				if ((page + 1) <= totalPages) {				
					next = "<img src=\"images/arrow_right.png\" onclick=\"updateTwitterDisplay(" + (page + 1) + ")\"></img>";
				}
				if ((page - 1) >= 1) { 
					previous = "<img src=\"images/arrow_left.png\" onclick=\"updateTwitterDisplay(" + (page - 1) + ")\"></img>";
				}				
				document.getElementById("twitter_footer").innerHTML = "<center>" + previous + "&nbsp&nbsp;" + next + "</center>";
			}
 		  	
 		  	function updateWikiLocationInformation() {
				if (!(selectedLocation.lat() == 0 || selectedLocation.lng() == 0)) {
					document.getElementById("wiki_stream").innerHTML="Searching..";
					articles(selectedLocation, document.getElementById("wiki_range").value);
				}
			}

 		  	// Load the twitter display based on whats in tweets array
			function updateWikiDisplay(page) {
				var startItem = (page - 1) * wikisPerPage;
				var endItem = page * wikisPerPage;
				var output = "";
				if (endItem > listOfWikis.length) {
					endItem = listOfWikis.length;
				}
				for (i = startItem; i < endItem; i++) {
					output += listOfWikis[i];	
				}				
				document.getElementById("wiki_stream").innerHTML = output;
				updateWikiPaging(page);
 		  	}

			function updateWikiPaging(page) {
				var totalPages = Math.round(listOfWikis.length / wikisPerPage);
				if (totalPages < (listOfWikis.length / wikisPerPage)) {
					totalPages++;
				}
				var next = "&nbsp;";
				var previous = "&nbsp;";
				if ((page + 1) <= totalPages) {				
					next = "<img src=\"images/arrow_right.png\" onclick=\"updateWikiDisplay(" + (page + 1) + ")\"></img>";
				}
				if ((page - 1) >= 1) { 
					previous = "<img src=\"images/arrow_left.png\" onclick=\"updateWikiDisplay(" + (page - 1) + ")\"></img>";
				}				

				document.getElementById("wiki_footer").innerHTML = "<center>" + previous + "&nbsp&nbsp;" + next + "</center>";
			}
			
			</script>
	</head>

	<body onload="load()" onunload="GUnload()">
		<div class="container">
			<div class="span-21">&nbsp;</div>
			<div class="span-2"><h1><i>BETA</i></h1></div>
			<div class="span-24"><center><hr/></center></div>
			<div class="span-3">
				<h1>lctn.me</h1>
			</div>
			<div class="span-9">
				<h4><i>Find it, share it</i></h4>
			</div>
			<div class="span-12 last">
				<div id="message"></div>
			</div>
			<div class="span-24"><hr/></div>
			<div class="span-12">
				<div class="header">
				Start by searching for an address, or place name
				</div>
				<div class="detail">
					<center>
						<input type="text" class="title" name="address" id="address" value="" onkeypress="if (event.keyCode == 13) { locationFromAddr();}"/>
						<input style="height: 33px;" class="large" type="button" name="find" value="Find" onclick="locationFromAddr();"/>
					</center>
				</div>
				<div class="footer-clear"></div>
			</div>

			<div class="span-12 last">
				<div class="header">
				Create short url for this location
				</div>
				<div class="detail">
					<center>
						<input style="height: 33px;"  class="large" type="button" name="generate" value="Go" onclick="shortenUrl();"/>
						<input type="text" class="title" name="url" id="url" value="" readonly="readonly"/>
					</center>
				</div>
				<div class="footer-clear"></div>
			</div>

			<div class="span-24">&nbsp;</div>

			<div id="map_container" class="span-12">

				<div class="header">
				Click anywhere to select a location
				</div>
				<div class="detail">
					<center>
						<div id="map" style="width: 468px; height: 465px;"></div>
					</center>
				</div>
				<div class="footer-straight"></div>
			</div>

			<div id="view-container" class="span-12 last">
				<div class="header">
				Streetview
				</div>
				<div class="detail">
					<center>
						<div id="streetview" style="width: 468px; height: 465px"></div>
					</center>
				</div>
				<div class="footer-straight"></div>
			</div>

			<div class="span-24">&nbsp;</div>

			<div class="span-12">
				<div class="header">
	              	Share the link with your friends
			    </div>
				<div class="detail-padded fixed-height-small">
						<center><div id="social"></div></center>
				</div>
				<div class="footer-clear"></div>
			</div>

			<div class="span-12 last">
				<div class="header">
	              	Weather (under development)
			    </div>
				<div class="detail-padded fixed-height-small">
					<div id="weather_stream"></div>
				</div>
				<div class="footer-clear"></div>
			</div>

			<div class="span-24">&nbsp;</div>

			<div class="span-12 ">
				<div class="header">
	              	Tweets
				</div>
 				<div class="detail-padded">
					<center>
						Search for <input type="text" name="filter" id="filter" onkeypress="if (event.keyCode == 13) { updateTwitterLocationInformation(); }"/>
						in <input type="text" name="tweet_range" id="tweet_range" value="1" onkeypress="if (event.keyCode == 13) { updateTwitterLocationInformation(); }"/> km
						<input type="button" id="filter_now" name="filter_now" value="Go" onclick="updateTwitterLocationInformation();"/>
					</center>
				</div>
				<div class="detail-padded fixed-height-social">
					<div id="tweet_stream">Tweets close to your selected location</div>
				</div>
				<div class="footer-text fixed-height-footer">
	              	<div id="twitter_footer"></div>
				</div>
			</div>

			<div class="span-12 last ">
				<div class="header">
	              	Wiki Articles
				</div>
 				<div class="detail-padded">
					<center>
						Find me articles in a <input type="text" name="wiki_range" id="wiki_range" value="1" onkeypress="if (event.keyCode == 13) { updateWikiLocationInformation(); }"/> km radius
						<input type="button" id="filter_now" name="filter_now" value="Go" onclick="updateWikiLocationInformation();"/>								
					</center>
				</div>
				<div class="detail-padded fixed-height-social">
					<center>
						<div id="wiki_stream">Wikipedia entries close to your location</div>
					</center>
				</div>
				<div class="footer-text fixed-height-footer">
	              	<div id="wiki_footer"></div>
				</div>
			</div>

			<div class="span-24">&nbsp;</div>

			<div class="span-24"><hr/></div>
<!-- 
			<div class="span-1"><a href="about.html">About</a></div>
			<div class="span-1"><a href="contact.html">Contact</a></div>
-->
			<div class="span-23">&nbsp;</div>
			<div class="span-1 last">v0.0.1</div>
			<div class="span-24">&nbsp;</div>
		</div>



	</body>

</html>
