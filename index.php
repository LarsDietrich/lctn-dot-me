<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

	<head>
	    <title>lctn.me | A Location Portal - Find it, Share it</title>
	
		<link rel="stylesheet" href="css/blueprint/screen.css" type="text/css" media="screen, projection"/>
		<link rel="stylesheet" href="css/blueprint/print.css" type="text/css" media="print"/> 
		<!--[if lt IE 8]>
		<link rel="stylesheet" href="css/blueprint/ie.css" type="text/css" media="screen, projection"/>
		<![endif]-->
		<link rel="stylesheet" href="css/layout.css" type="text/css"/>
		<link rel="stylesheet" href="css/displaybox.css" type="text/css"/>
		<link rel="stylesheet" href="css/jquery-tools.css" type="text/css"/>
		<link rel="stylesheet" href="css/styled-button.css" type="text/css"/>


		<!-- http://lctn -->
		<script type="text/javascript" src="https://www.google.com/jsapi?key=ABQIAAAANICyL01ax9PqYKeJwtOXfxTh05SPp9XRgWyeCyc0ee48nkavlxTTkteFyCb29mhFOfEeXVaj-F6hAw"></script>


	</head>

	<body onload="load()">

		<!-- overlayed element -->
		<div class="apple_overlay" id="overlay">
			<!-- the external content is loaded inside this tag -->
			<div class="contentWrap"></div>
		
		</div>

		<div id="displaybox" onclick="beta();" style="display: none;"></div>

		<div class="container">
			<div class="span-24"><hr/></div>
			<div class="span-3">
				<h1><span style="color: #4E84A6" class="title-text">lctn.me</span></h1>
			</div>
			<div class="span-9">
				<h4><i><span  style="color: #4E84A6" class="title-text">Find it, share it</span></i></h4>
			</div>
			<div class="span-12 last">
				<div id="message"></div>
			</div>
			<div class="span-24">&nbsp;</div>
			
			<div class="span-12">
				<div class="header">
					Find
				</div>
				<div class="detail">
					<center>
						<input title="Enter an address or place name to search for (eg. Eiffel Tower or 10 Downing Street, London) then click Find or press Enter" type="text" class="title" name="address" id="address" value="" onkeypress="if (event.keyCode == 13) { locationFromAddr();}"/>
						<input class="large button" type="button" name="find" value="Find" onclick="locationFromAddr();"/>
					</center>
				</div>
				<div class="footer-clear"></div>
			</div>

			<div class="span-12 last">
				<div class="header" title="Share this location with friends, generate the link and click on one of the social icons.">
				Share
				</div>
				<div class="detail" >
					<center>
						<input class="large button" type="button" name="generate" value="Shorten" onclick="shortenUrl();"/>
						<input title="Click Shorten to generate a short url. The link will point back to this location and open the page as you see it (with live data updated)." type="text" class="url-text" name="url" id="url" value="" readonly="readonly"/>
						<div class="inline" id="url-window"></div>
					</center>
				</div>
				<div class="footer-clear"></div>
			</div>

			<div class="span-24">&nbsp;</div>

			<div id="view-container-left" class="span-12">
				<div id="map_container">
					<div class="header">
						<div onclick="toggleMapSize()" title="Shows a map of the immediate area around the location. Click to expand and contract map">Map</div>
					</div>
					<div class="detail-map">
						<center>
	<!-- 						
						<div id="map" style="width: 40px; height: 40px;"></div>
	-->
						<div id="map_canvas" style="width: 468px; height: 465px;"></div>
						</center>
					</div>
					<div class="footer-text fixed-height-footer"></div>
				</div>
			</div>

			<div id="view-container-right" class="span-12 last">

				<div id="streetview_container" class="child">
	
					<div class="header">
						<div class="header-left">
							<img title="Click to see previous view" class="container-navigation-icon" src="images/arrow-left.png" onclick="prevContainer('streetview_container')"/>
						</div>
						<div class="header-center" title="Shows the streetview at the current location, streetview is only available in certain locations.">
							Streetview
						</div>
						<div class="header-right">
							<img title="Click to see next view" class="container-navigation-icon" src="images/arrow-right.png" onclick="nextContainer('streetview_container')"/>
						</div>

					</div>
					<div class="detail-map">
						<div id="streetview" style="width: 465px; height: 465px"></div>
					</div>
					<div class="footer-text fixed-height-footer"></div>
				</div>
	
				<div id="twitter_container" class="child">
	
					<div class="header">
						<div class="header-left">
							<img title="Click to see previous view" class="container-navigation-icon" src="images/arrow-left.png" onclick="prevContainer('twitter_container')"/>
						</div>
						<div class="header-center"  title="Shows tweets in the surrounding area for people that have chosen to share their location.">
							Twitter
						</div>
						<div class="header-right">
							<img title="Click to see next view" class="container-navigation-icon" src="images/arrow-right.png" onclick="nextContainer('twitter_container')"/>
						</div>
					</div>
	 				<div class="detail-padded">
						<center>
							Search for <input title="Enter a search term to filter tweets, comma seperate for multiple terms (eg. party, cool)" type="text" name="filter" id="filter" onkeypress="if (event.keyCode == 13) { updateTwitterLocationInformation(); }"/>
							in <input title="How big an area would you like to search for tweets in?" class="short-text" type="text" name="tweet_range" id="tweet_range" value="1" onkeypress="if (event.keyCode == 13) { updateTwitterLocationInformation(); }"/> km
							<input type="button" id="filter_now" name="filter_now" value="Go" onclick="updateTwitterLocationInformation();"/>
						</center>
					</div>
					<div class="detail-padded fixed-height-block-with-title">
						<div id="tweet_stream">No tweets found, try a bigger search area or search for something different</div>
					</div>
					<div class="footer-text fixed-height-footer">
		              	<div id="twitter_footer"></div>
					</div>
				
				</div>

				<div id="wiki_container" class="child">
					<div class="header">
						<div class="header-left">
							<img title="Click to see previous view" class="container-navigation-icon" src="images/arrow-left.png" onclick="prevContainer('wiki_container')"/>
						</div>
						<div class="header-center" title="Shows all wikipedia articles in the area.">
							Wikipedia
						</div>
						<div class="header-right">
							<img title="Click to see next view" class="container-navigation-icon" src="images/arrow-right.png" onclick="nextContainer('wiki_container')"/>
						</div>
					</div>
	 				<div class="detail-padded">
						<center>
							Find me articles within <input title="How big an area would you like to see wikipedia articles for (maximum of 5km)?" class="short-text" type="text" name="wiki_range" id="wiki_range" value="1" onkeyup="if (this.value > 5) this.value = 5; " onkeypress="if (event.keyCode == 13) { updateWikiLocationInformation(); }"/> km
							<input type="button" id="filter_now" name="filter_now" value="Go" onclick="updateWikiLocationInformation();"/>								
						</center>
					</div>
					<div class="detail-padded fixed-height-block-with-title">
						<center>
							<div id="wiki_stream">No entries found, try a bigger search area</div>
						</center>
					</div>
					<div class="footer-text fixed-height-footer">
		              	<div id="wiki_footer"></div>
					</div>
				</div>

				<div id="general_container" class="child">
					<div class="header">
						<div class="header-left">
							<img title="Click to see previous view" class="container-navigation-icon" src="images/arrow-left.png" onclick="prevContainer('general_container')"/>
						</div>
						<div class="header-center" title="Shows general information about the location.">
							General
						</div>
						<div class="header-right">
							<img title="Click to see next view" class="container-navigation-icon" src="images/arrow-right.png" onclick="nextContainer('general_container')"/>
						</div>
				    </div>
					<div class="detail-padded fixed-height-block">
						<div class="general-text inline" id="location_stream"></div>
						<div class="general-text inline" id="timezone_stream"></div>
						<br/><br/>
						<div id="weather_stream"></div>
					</div>
					<div class="footer-text fixed-height-footer"></div>
				</div>
			</div>
			<div class="span-24">&nbsp;</div>
			
			<div class="span-2"><a href="config.php" rel="#overlay">Options</a></div>
<!-- 
			<div class="span-2"><a href="contact/contact.php" rel="#overlay">Contact</a></div>
-->
			<div class="span-2"><a href="about.php" rel="#overlay">About</a></div>
			<div class="span-17">&nbsp;</div>
			<div class="span-1 last" style="font-weight: bold; color: #4E84A6">0.0.6</div>
			<div class="span-24">&nbsp;</div>

		</div>

		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
		<script src="http://cdn.jquerytools.org/1.2.5/all/jquery.tools.min.js"></script>
		
 		<script type="text/javascript" src="js/gears_init.js"></script> 
		<script type="text/javascript" src="js/jxs.js"> </script>
		<script type="text/javascript" src="js/jquery-cookie.js"> </script>

		<script type="text/javascript" src="js/custom/tweets.js"> </script>
		<script type="text/javascript" src="js/custom/weather.js"> </script>
		<script type="text/javascript" src="js/custom/wikipedia.js"> </script>
		<script type="text/javascript" src="js/custom/timezone.js"> </script>

		<script type="text/javascript" src="http://www.google.com/jsapi"></script>

		<script type="text/javascript">
			var uservoiceOptions = {
			  /* required */
			  key: 'lctn',
			  host: 'lctn.uservoice.com', 
			  forum: '86707',
			  showTab: true,  
			  /* optional */
			  alignment: 'left',
			  background_color:'#73654C', 
			  text_color: 'white',
			  hover_color: '#4E84A6',
			  lang: 'en'
			};
			
			function _loadUserVoice() {
			  var s = document.createElement('script');
			  s.setAttribute('type', 'text/javascript');
			  s.setAttribute('src', ("https:" == document.location.protocol ? "https://" : "http://") + "cdn.uservoice.com/javascripts/widgets/tab.js");
			  document.getElementsByTagName('head')[0].appendChild(s);
			}
			_loadSuper = window.onload;
			window.onload = (typeof window.onload != 'function') ? _loadUserVoice : function() { _loadSuper(); _loadUserVoice(); };
		</script>

		<script type="text/javascript">

			// Current containers supported
			var containers = ["general_container", "wiki_container", "twitter_container", "streetview_container"];
			var activeContainer = "streetview_container";

			var nextAvailableContainer;
			var prevAvailableContainer;
			
			// reference to the main map
			var map;
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

			var maptype = "roadmap";
						
			// load the necessary data, parse command line for location information and show map
			function load() {

				if (isEnabled("beta")) {
					beta();
				}

				updateUrlWindow("");

				latitude = <?php if (isset($_GET["lat"])) { echo $_GET["lat"]; } else { echo "999"; }?>;
				longitude = <?php if (isset($_GET["lng"])) { echo $_GET["lng"]; } else { echo "999"; }?>;
				heading = <?php if (isset($_GET["heading"])) { echo $_GET["heading"]; } else { echo "0"; }?>;
				pitch = <?php if (isset($_GET["pitch"])) { echo $_GET["pitch"]; } else { echo "0"; }?>;
				zoom = <?php if (isset($_GET["zoom"])) { echo $_GET["zoom"]; } else { echo "12"; }?>;
				maptype = <?php if (isset($_GET["maptype"])) { echo "\"" . $_GET["maptype"] . "\""; } else { echo "\"roadmap\""; }?>;
				activeContainer = <?php if (isset($_GET["container"])) { echo "\"" . $_GET["container"] . "\""; } else { echo "\"\""; }?>;

				if ($.cookie("activeContainer") == null) {
					$.cookie("activeContainer", "general_container"); 
				}
				
				if (activeContainer == "") {
					activeContainer = $.cookie("activeContainer");
				}
				
				$.cookie("option_" + activeContainer.split("_")[0], "true");
				
				if (latitude == 999 || longitude == 999) {
					findMe();
				} else {
  				    selectedLocation = new google.maps.LatLng(latitude, longitude);
					showMap();
					repositionMarker();
				}
				
				document.getElementById(activeContainer).style.display="inline";

				if (isEnabled("popup")) {
					$("[title]").tooltip({ effect: "slide"});
				}

				$(function() {

					// if the function argument is given to overlay, it is assumed to be the onBeforeLoad event listener.

					$("a[rel]").overlay({
						mask: '#C7D9D4',
						effect: 'apple',

						onBeforeLoad: function() {
							// grap wrapper element inside content
							var wrap = this.getOverlay().find(".contentWrap");
							// load the page specified in the trigger
							wrap.load(this.getTrigger().attr("href"));

						}
					});
				});
			}

			// 
			// Tries to get the users current location using build in geolocation functionality.
			//
			function findMe() {
				if (navigator.geolocation) {
					navigator.geolocation.getCurrentPosition(function(position) {	
							selectedLocation = new google.maps.LatLng(position.coords.latitude,	position.coords.longitude);
							setMessage("Repositioning map to best guess of where you are (accuracy not guaranteed)", "info");
							repositionMarker();
						}, function(error) {
							setMessage("Tried to get your location, but there was a problem, sorry", "error");
							selectedLocation = new google.maps.LatLng(0, 0);
							repositionMarker();
						});
				} 
				else if (google.gears) {
					var geo = google.gears.factory.create('beta.geolocation');
					geo.getCurrentPosition(function(position) {	
						selectedLocation = new google.maps.LatLng(position.coords.latitude,	position.coords.longitude);
						setMessage("Repositioning map to best guess of where you are (accuracy not guaranteed)", "info");
						repositionMarker();
					}, function(error) {
						selectedLocation = new google.maps.LatLng(0, 0);
						setMessage("Tried to get your location, but there was a problem, sorry", "error");
						repositionMarker();
					});
				} 
			}

			//
			// Loads the Google Map 
			//
			function showMap() { 
				
			  var myOptions = {
				  zoom: zoom,
				  center: selectedLocation,
				  mapTypeId: maptype,
  				  streetViewControl: false
  			   }

			  map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

			  positionMarker = new google.maps.Marker({
			      position: selectedLocation, 
			      map: map,
			      title: "You are here"
			  });

			  var panoOptions = {
				  addressControlOptions: {
					    position: google.maps.ControlPosition.BOTTOM,
					    style: {
					      "fontWeight" : "bold",
					      "backgroundColor" : "#191970",
					      "color" :"#A9203E"
					    }
				  },
				  navigationControlOptions: {
				    style: google.maps.NavigationControlStyle.SMALL
				  },
				  enableCloseButton: false,
				  linksControl: true
			  };

			  panorama = new google.maps.StreetViewPanorama(document.getElementById("streetview"), panoOptions);
		
		      setupListeners();


			}

		   //
		   // Various listeners to listen for changes on the map(s):
		   // 
		   function setupListeners() {
			 google.maps.event.addListener(map, 'click', function(event) {
  			    selectedLocation = event.latLng;
				repositionMarker();
			  });

			 
 			  google.maps.event.addListener(map, 'zoom_changed', function() {
				  zoom = map.getZoom();
  		  	  });

			  google.maps.event.addListener(map, 'maptypeid_changed', function() {
				  maptype = map.getMapTypeId();
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

		    //
			// Moves the marker to a new location specified by selectedLocation. Refreshes screen for anything
			// that uses the location (like tweets and streetview)
			//
			function repositionMarker() {

				if (!map) {
					showMap();
				}
				positionMarker.setMap(null);
				positionMarker.setPosition(selectedLocation);
				positionMarker.setMap(map);
				streetViewService.getPanoramaByLocation(selectedLocation, 70, processSVData);
				updateWikiLocationInformation();
				updateTwitterLocationInformation();
				updateWeatherLocationInformation();
				reverseCodeLatLng();				
				map.setCenter(selectedLocation);
				document.getElementById("url").value="";
				setMessage("", "");
				scroll(0,0);
				updateStats();
			}

		    // Tracks a click on the map for statistics purposes.
			function updateStats() {
				jx.load("stats.php?do=stat&lat=" + selectedLocation.lat() + "&lng=" + selectedLocation.lng(), function(data) {});
			}
		    
			//
			// Try find street view data, load appropriate panorama panel and set selectedLocation.
			// If the selectedLocation does not have a streetview associated with it, will
			// attempt to find a streetview within a specified distance and reposition the map 
			// to that point.
			//
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
				  setMessage("Streetview not available at this location.", "notice");
				  panorama.setVisible(false);
				}
			}

			// 
			// Sets a message in the upper right message display area
			// 
			function setMessage(message, type) {
				if (message == "") {
					document.getElementById("message").innerHTML="";
				} else {
					jx.load("message.php?message=" + message + "&type=" + type, function(data) { document.getElementById('message').innerHTML=data; });
				}
			}

			//
			// Sets the selectedLocation based on address in address box
			//
			function locationFromAddr() {
				var address = document.getElementById("address").value;
				setMessage("", "");
				geocoder.geocode( { 'address': address}, function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
					      selectedLocation = results[0].geometry.location;
					      repositionMarker();
					    } else {
						  setMessage("Not able to locate that place, please try something else.", "info");
					    }
					});
				}

			//
			// Reverse geocodes the address, moves the marker to the new location
			//
			function locationFromAddress(address) {
				document.getElementById("address").value = address;
				locationFromAddr();
			}
			
			//
			// Sets the address box based on current selectedLocation coordinates
			//
			function reverseCodeLatLng() {
				geocoder.geocode({'latLng': selectedLocation}, function(results, status) {
					document.getElementById("timezone_stream").innerHTML="";
					output = "";
					var address = "";
					if (status == google.maps.GeocoderStatus.OK) {
						if (results.length > 0) {
							address = results[0].formatted_address;
							document.getElementById("address").value = address;
							updateTimezoneLocationInformation();
						} else {
							setMessage("No addresses were found at this location.", "info");
						}
					} else {
						setMessage("Unable to determine address from current location", "error");
					}
					updateGeneralLocationInformation(address);
				});
			}

			//
			// Determine the shortened URL based on the current location, saves to DB
			//
			function shortenUrl() {
				root = "http://" + top.location.host + "/";
				longurl = root + "?lat=" + selectedLocation.lat() + "&lng=" + selectedLocation.lng() + "&heading=" + heading + "&pitch=" + pitch + "&zoom=" + zoom + "&container=" + activeContainer + "&maptype=" + maptype ;
				shorturl = "";
				jx.load("shrink.php?shorturl=" + shorturl + "&url=" + escape(longurl), function(data) { document.getElementById("url").value=root + data; updateUrlWindow(root + data);} );
			}

			//
			// Updates the icons on the social bar with the current shortened link
			//
			function updateUrlWindow(link) {
				var output = "";

				output += "<a href=\"http://twitter.com/home/?status=";
				output += link + "\"";
				output += " target=\"_blank\"><img class='social-button' src=\"images/twitter.jpg\" title=\"Tweet this link to the world.\" alt=\"Twitter\"></img></a>";

				output += "<a href=\"http://www.facebook.com/sharer.php?u=";
				output += link + "\"";
				output += " target=\"_blank\"><img class='social-button' src=\"images/facebook.jpg\" title=\"Share the link with your Facebook friends.\" alt=\"Facebook\"></img></a>";

				output += "<a href=\"http://del.icio.us/post?url=";
				output += link + "\"";
				output += " target=\"_blank\"><img class='social-button' src=\"images/delicious.jpg\" title=\"\Add the link to your Del.icio.us account.\" alt=\"Del.icio.us\"></img></a>";

				output += "<a href=\"mailto:?subject=";
				output += link + "\"";
				output += "><img class='social-button' src=\"images/email.jpg\" title=\"Email the link to a friend.\" alt=\"Send by Email\"></img></a>";

				document.getElementById("url-window").innerHTML=output;

				if (isEnabled("popup")) {
					$("[title]").tooltip( {effect : "slide"});
				}
							
			}

			function updateGeneralLocationInformation(address) {
				var output = "You are positioned at <b>" + Math.round(selectedLocation.lng() * 10000) / 10000 + "</b> longitude and <b>" + Math.round(selectedLocation.lat() * 10000) / 10000 + "</b> latitude";
				if (!(address == "")) {
					output += ", which is also known as <b>" + address + "</b>";
				}
				output += ".";
				document.getElementById("location_stream").innerHTML=output;
			}

 		  	//
 		  	// Display the beta page.
 		  	//
			function beta(){
				var thediv=document.getElementById('displaybox');
				if(thediv.style.display == "none"){
					thediv.style.display = "";
					thediv.innerHTML = "<span class='displaybox-large'/>BETA</span><br/><span class='displaybox-normal'>This site is still under development, feel free to use it but expect some issues. I cannot take responsibility for the stability and accuracy of data being displayed.<br/><br/>Thank you for trying out the site.</span><br/><br/><span class='displaybox-normal'/>(click anywhere to close)</span>";
				}else{
					thediv.style.display = "none";
					thediv.innerHTML = '';
				}
				return false;
			}

 		  	//
 		  	// Loads the next container in the sequence based on the containers array.
 		  	// Containers that are dependant on focus for refresh are refreshed (Streetview)
 		  	//
			function nextContainer(container) {
				position = 0;
				for (i = 0; i < containers.length; i++) {
					if (containers[i] == container) {
						position = i;
					}
				}
				if (position == containers.length-1) {
					position = 0;
				} else {
					position++;
				}
				
				document.getElementById(container).style.display="none";

				if (isEnabled(containers[position].split("_")[0])) {
					document.getElementById(containers[position]).style.display="inline";
					activeContainer = containers[position];
					if (activeContainer == "streetview_container") {
						streetViewService.getPanoramaByLocation(selectedLocation, 70, processSVData);
					}
					$.cookie("activeContainer", activeContainer);
				} else {
					nextContainer(containers[position]);
				}
			}

 		  	//
 		  	// Loads the previous container in the sequence based on the containers array.
 		  	// Containers that are dependant on focus for refresh are refreshed (Streetview)
 		  	//
			function prevContainer(container) {
				position = 0;
				for (i = 0; i < containers.length; i++) {
					if (containers[i] == container) {
						position = i;
					}
				}
				if (position == 0) {
					position = containers.length-1;
				} else {
					position--;
				}

				document.getElementById(container).style.display="none";

				if (isEnabled(containers[position].split("_")[0])) {
					document.getElementById(containers[position]).style.display="inline";
					activeContainer = containers[position];
					if (activeContainer == "streetview_container") {
						streetViewService.getPanoramaByLocation(selectedLocation, 70, processSVData);
					}
					$.cookie("activeContainer", activeContainer);
				} else {
					prevContainer(containers[position]);
				}
			}
			
 		  	//
 		  	// Queries cookies for option to see if it's set to true. If null, assumes as never set
 		  	// and sets to true.
 		  	//
			function isEnabled(option) {
				var result = false;
				var cookie = $.cookie("option_" + option);

				if ( cookie == null ) {
					$.cookie("option_" + option, "true");
				}
				
				if ($.cookie("option_" + option) == "true") {
					result = true;
				}
				return result;
			}

			function toggleMapSize() {
				var max_width="955px";
				var max_width_map="955px";
				var normal_width="470px";
				var normal_width_map = "470px";

				if ($("#map_container").css("width") == max_width) {
					$("#map_container").css("width", normal_width);
					$("#map").css("width", normal_width);
					$("#" + activeContainer).css("display", "block");
					google.maps.event.trigger(map, "resize");
				} else {
					$("#" + activeContainer).css("display", "none");
					$("#map_container").css("width", max_width);
					$("#map").css("width", max_width);
					google.maps.event.trigger(map, "resize");
				}
			}

			</script>

	</body>

</html>
