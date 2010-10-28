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
		<link rel="stylesheet" href="css/main.css" type="text/css"/>
		
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
		<script type="text/javascript" src="http://code.google.com/apis/gears/gears_init.js"></script>
		<script type="text/javascript" src="js/jxs.js"> </script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>

		<script type="text/javascript" src="js/custom/locate.js"> </script>
		<script type="text/javascript" src="js/custom/tweets.js"> </script>
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

			// load the necessary data, parse command line for location information and show map
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
				setMessage("This is an experimental app, a fun idea I've been toying with. <a href='mailto:rick@tonoli.co.za?subject=Tell me more about lctn.me'>Email me</a> for more info.", "info");
				streetViewService.getPanoramaByLocation(selectedLocation, 70, processSVData);
				map.setCenter(selectedLocation);
				updateTwitterLocationInformation();
				updateWikiLocationInformation();
				reverseCodeLatLng();
				scroll(0,0);
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
				  panorama.setVisible(true);
				  positionMarker.setMap(null);
				  selectedLocation = data.location.latLng;
				  positionMarker.setPosition(selectedLocation);
				  positionMarker.setMap(map);
			  	} else {
				  setMessage("Streetview not available at this location, try clicking on a road, or searching for another place", "info");
				}
			}

//			// Determines if supplied shortened url is available and sets it as the url to use
//			function customUrl() {
//				var url = document.getElementById("shorturl").value;
//				jx.load("custom_url.php?url=" + url, function(data) {document.getElementById('custom_url_message').innerHTML=data; });
//			}

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
			}

			// Update the url block with the supplied link, should be a shortened link
			function updateUrl(link) {
				document.getElementById("url").value=link;
			}

			  // Update the social bar with new shortened link
			function updateSocialBar(link) {
				data = "<a href=\"http://twitter.com/home/?status=";
				data = data + link + "\"";
				data = data + " target=\"_blank\"><img height=\"80px\" width=\"80px\" border=\"0\" src=\"images/twitter.jpg\" alt=\"Twitter\"></img></a>";
				document.getElementById("twitter").innerHTML=data;
				data = "<a href=\"http://www.facebook.com/sharer.php?u=";
				data = data + link + "\"";
				data = data + " target=\"_blank\"><img height=\"80px\" width=\"80px\" border=\"0\" src=\"images/facebook.jpg\" alt=\"Facebook\"></img></a>";
				document.getElementById("facebook").innerHTML=data;
			}

 		  	function updateTwitterLocationInformation() {
				if (!(selectedLocation.lat() == 0 || selectedLocation.lng() == 0)) {
					document.getElementById("tweet_stream").innerHTML="Loading..";
					tweets(selectedLocation, document.getElementById("filter").value, document.getElementById("tweet_range").value);
				}
			}

 		  	function updateWikiLocationInformation() {
				if (!(selectedLocation.lat() == 0 || selectedLocation.lng() == 0)) {
					document.getElementById("wiki_stream").innerHTML="Loading..";
					articles(selectedLocation, document.getElementById("wiki_range").value);
				}
			}

		</script>
	</head>

	<body onload="load()" onunload="GUnload()">
		<div class="container">
			<div class="span-24">
				<h3>ALPHA</h3>
			</div>
			<div class="span-12">
				<table>
					<thead>
			            <tr>
			              	<th class="span-12">Find a place</th>
			            </tr>
		           </thead>
 		           <tbody>
						<tr>
							<td>
								<center>
									<input type="text" class="title" name="address" id="address" value="" onkeypress="if (event.keyCode == 13) { locationFromAddr();}"/>
									<input style="height: 33px;" class="large" type="button" name="find" value="Find" onclick="locationFromAddr();"/>
								</center>
							</td>
						</tr>
          		   </tbody>
				</table>
			</div>
			<div class="span-12 last">
				<table>
					<thead>
			            <tr>
			              	<th class="span-12">Create short url</th>
			            </tr>
		           </thead>
 		           <tbody>
						<tr>
							<td>
							<center>
								<input type="text" class="title large" name="url" id="url" value="" readonly="readonly"/>
								<input style="height: 33px;"  class="large" type="button" name="generate" value="Generate" onclick="shortenUrl();"/>
							</center>
							</td>
						</tr>
          		   </tbody>
				</table>
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
			<div class="span-12">
				<table>
					<thead>
			            <tr>
			              	<th class="span-12">Map</th>
			            </tr>
		           </thead>
 		           <tbody>
						<tr>
							<td>
								<center>
								<div id="map" style="width: 460px; height: 460px;"></div>
								</center>
							</td>
						</tr>
          		   </tbody>
				</table>
			</div>
			<div class="span-12 last">
				<table>
					<thead>
			            <tr>
			              	<th class="span-12">Streetview of current location</th>
			            </tr>
		           </thead>
 		           <tbody>
						<tr>
							<td>
								<center>
								<div id="streetview" style="width: 460px; height: 460px"></div>
								</center>
							</td>
						</tr>
          		   </tbody>
				</table>
			</div>
			<div class="span-24">
				<table>
					<thead>
			            <tr>
			              	<th class="span-20">Share with your friends</th>
			            </tr>
		           </thead>
 		           <tbody>
						<tr>
							<td>
								<center>
									<div class="span-2" id="twitter">
										<a href="http://twitter.com/home/?status=" target="_blank">
											<img height="80px" width="80px" border="0" src="images/twitter.jpg" alt="Twitter"></img>
										</a>
									</div>
									<div class="span-2" id="facebook">
										<a href="http://www.facebook.com/sharer.php?u=" target="_blank">
											<img height="80px" width="80px" border="0" src="images/facebook.jpg" alt="Facebook"></img>
										</a>
									</div>
								</center>
							</td>
						</tr>
          		   </tbody>
				</table>
			</div>
			<div class="span-12 ">
				<table>
					<thead>
			            <tr>
			              	<th class="span-12">Twitter</th>
			            </tr>
		           </thead>
 		           <tbody>
						<tr>
							<td>
								<center>
									Search for <input type="text" name="filter" id="filter" onkeypress="if (event.keyCode == 13) { updateTwitterLocationInformation(); }"/>
									in <input type="text" name="tweet_range" id="tweet_range" value="1" onkeypress="if (event.keyCode == 13) { updateTwitterLocationInformation(); }"/> km
									<input type="button" id="filter_now" name="filter_now" value="Go" onclick="updateTwitterLocationInformation();"/>
								</center>
							</td>
						</tr>
						<tr>
							<td>
								<div id="tweet_stream">Tweets close to your selected location</div>
							</td>
						</tr>
          		   </tbody>
					<tfoot>
			            <tr>
			              	<th class="span-12"><div id="wiki_paging"></div></th>
			            </tr>
		           </tfoot>
				</table>
			</div>
			<div class="span-12 last ">
				<table>
					<thead>
			            <tr>
			              	<th class="span-12">Wikipedia</th>
			            </tr>
		           </thead>
 		           <tbody>
						<tr>
							<td>
								<center>
									Find me articles in a <input type="text" name="wiki_range" id="wiki_range" value="1" onkeypress="if (event.keyCode == 13) { updateWikiLocationInformation(); }"/> km radius
									<input type="button" id="filter_now" name="filter_now" value="Go" onclick="updateWikiLocationInformation();"/>								
								</center>
							</td>
						</tr>
						<tr>
							<td>
								<center>
									<div id="wiki_stream">Wikipedia entries close to your location</div>
								</center>
							</td>
						</tr>
          		   </tbody>
					<tfoot>
			            <tr>
			              	<th class="span-12"><div id="wiki_paging"></div></th>
			            </tr>
		           </tfoot>
				</table>
			</div>
			<div class="span-24">&nbsp;</div>
			<div class="span-24"><hr/></div>
			<div class="span-1"><a href="about.html">About</a></div>
			<div class="span-1"><a href="contact.html">Contact</a></div>
			<div class="span-22 last"></div>
			<div class="span-24">&nbsp;</div>
		</div>



	</body>

</html>
