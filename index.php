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

		<!-- http://lctn -->
		<script type="text/javascript" src="https://www.google.com/jsapi?key=ABQIAAAANICyL01ax9PqYKeJwtOXfxTh05SPp9XRgWyeCyc0ee48nkavlxTTkteFyCb29mhFOfEeXVaj-F6hAw"></script>

		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
		<script src="http://cdn.jquerytools.org/1.2.5/all/jquery.tools.min.js"></script>
		
 		<script type="text/javascript" src="js/gears_init.js"></script> 
		<script type="text/javascript" src="js/jxs.js"> </script>
		<script type="text/javascript" src="js/jquery-cookie.js"> </script>

		<script type="text/javascript" src="js/custom/tweets.js"> </script>
		<script type="text/javascript" src="js/custom/weather.js"> </script>
		<script type="text/javascript" src="js/custom/wikipedia.js"> </script>


		<script type="text/javascript">

			// Current containers supported
			var containers = ["general_container", "wiki_container", "twitter_container", "streetview_container"];
			var active_container = "streetview_container";
			
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

			var hasMoved = false;
			
			// load the necessary data, parse command line for location information and show map
			function load() {
				
				beta();
				updateUrlWindow("");
				
				latitude = <?php if (isset($_GET["lat"])) { echo $_GET["lat"]; } else { echo "999"; }?>;
				longitude = <?php if (isset($_GET["lng"])) { echo $_GET["lng"]; } else { echo "999"; }?>;
				heading = <?php if (isset($_GET["heading"])) { echo $_GET["heading"]; } else { echo "0"; }?>;
				pitch = <?php if (isset($_GET["pitch"])) { echo $_GET["pitch"]; } else { echo "0"; }?>;
				zoom = <?php if (isset($_GET["zoom"])) { echo $_GET["zoom"]; } else { echo "12"; }?>;
				active_container = <?php if (isset($_GET["right_container"])) { echo "\"" . $_GET["right_container"] . "\""; } else { echo "\"\""; }?>;

				if ($.cookie("active_container") == null) {
					$.cookie("active_container", "streetview_container"); 
				}
				
				if (active_container == "") {
					active_container = $.cookie("active_container");
				}
				
				if (latitude == 999 || longitude == 999) {
					findMe();
				} else {
  				    selectedLocation = new google.maps.LatLng(latitude, longitude);
					showMap();
				}

				document.getElementById(active_container).style.display="inline";

				$("[title]").tooltip({ effect: "slide"});

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

			// Try to find the user's location
			function findMe() {
				// Try W3C Geolocation (Preferred)
				if (navigator.geolocation) {
					navigator.geolocation.getCurrentPosition(function(position) {	
							selectedLocation = new google.maps.LatLng(position.coords.latitude,	position.coords.longitude);
							repositionMap();
						}, function(error) {
							selectedLocation = new google.maps.LatLng(0, 0);
							repositionMap();
						});
					// Try Google Gears Geolocation
				} 
//				else if (google.gears) {
//					var geo = google.gears.factory.create('beta.geolocation');
//					geo.getCurrentPosition(function(position) {	
//						selectedLocation = new google.maps.LatLng(position.coords.latitude,	position.coords.longitude);
//						repositionMap();
//					}, function(error) {
//						selectedLocation = new google.maps.LatLng(0, 0);
//						repositionMap();
//					});
//				} 
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
				  linksControl: true,
				  addressControl:true,
				  visible: true,
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
				if (!map) {
					showMap();
				}
				positionMarker.setMap(null);
				positionMarker.setPosition(selectedLocation);
				positionMarker.setMap(map);

				streetViewService.getPanoramaByLocation(selectedLocation, 70, processSVData);
				updateWikiLocationInformation();
				updateTwitterLocationInformation();
				updateGeneralLocationInformation();

				reverseCodeLatLng();
				
				map.setCenter(selectedLocation);
				document.getElementById("url").value="";
				setMessage("", "success");
				scroll(0,0);
				hasMoved = false;
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
				if (message == "") {
					document.getElementById("message").innerHTML="";
				} else {
					jx.load("message.php?message=" + message + "&type=" + type, function(data) { document.getElementById('message').innerHTML=data; });
				}
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
				longurl = root + "?lat=" + selectedLocation.lat() + "&lng=" + selectedLocation.lng() + "&heading=" + heading + "&pitch=" + pitch + "&zoom=" + zoom + "&container=" + active_container ;
				shorturl = "";
				jx.load("shrink.php?shorturl=" + shorturl + "&url=" + escape(longurl), function(data) { document.getElementById("url").value=root + data; updateUrlWindow(root + data);} );
			}

			
			  // Update the social bar with new shortened link
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
				
			}

			function updateGeneralLocationInformation() {
				if (!(selectedLocation.lat() == 0 || selectedLocation.lng() == 0)) {
					document.getElementById("general_stream").innerHTML="Searching..";
					getWeather(selectedLocation, 2);
				}
			}

 		  	// Load the weather display based on whats in tweets array
			function updateGeneralDisplay() {
				var output = "<table><tr>";
				for (i = 0; i < listOfWeather.length; i++) {
					output += listOfWeather[i];
				}				
				output += "</tr><tr><td colspan='6' class='weather-text'>";
				output += "Powered by <a href=\"http://www.worldweatheronline.com/\" title=\"Free local weather content provider\" target=\"_blank\">World Weather Online</a>";
				output += "</td></tr></table>";

				document.getElementById("general_stream").innerHTML = output;
				$("[title]").tooltip({ effect: 'slide'});
 		  	}
			
 		  	function updateTwitterLocationInformation() {
				if (!(selectedLocation.lat() == 0 || selectedLocation.lng() == 0)) {
					document.getElementById("tweet_stream").innerHTML="Searching..";
					getTweets(selectedLocation, document.getElementById("filter").value, document.getElementById("tweet_range").value);
				}
			}

 		  	function updateTwitterLocationInformationFromHashTag(value) {
 		  		document.getElementById("filter").value = value;
				updateTwitterLocationInformation();
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
				$("[title]").tooltip({ effect: "slide"});
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
					next = "<img class='footer-icon' src=\"images/arrow-right.png\" onclick=\"updateTwitterDisplay(" + (page + 1) + ")\"></img>";
				}
				if ((page - 1) >= 1) { 
					previous = "<img class='footer-icon' src=\"images/arrow-left.png\" onclick=\"updateTwitterDisplay(" + (page - 1) + ")\"></img>";
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
					next = "<img class='footer-icon' src=\"images/arrow-right.png\" onclick=\"updateWikiDisplay(" + (page + 1) + ")\"></img>";
				}
				if ((page - 1) >= 1) { 
					previous = "<img class='footer-icon' src=\"images/arrow-left.png\" onclick=\"updateWikiDisplay(" + (page - 1) + ")\"></img>";
				}				

				document.getElementById("wiki_footer").innerHTML = "<center>" + previous + "&nbsp&nbsp;" + next + "</center>";
			}

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
				document.getElementById(containers[position]).style.display="inline";
				active_container = containers[position];
				if (active_container == "streetview_container") {
					streetViewService.getPanoramaByLocation(selectedLocation, 70, processSVData);
				}

				$.cookie("active_container", active_container);
			}

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
				document.getElementById(containers[position]).style.display="inline";
				active_container = containers[position];
				if (active_container == "streetview_container") {
					streetViewService.getPanoramaByLocation(selectedLocation, 70, processSVData);
				}

				$.cookie("active_container", active_container);
			}
			
		</script>
	</head>

	<body onload="load()" onunload="GUnload()">

		<!-- overlayed element -->
		<div class="apple_overlay" id="overlay">
			<!-- the external content is loaded inside this tag -->
			<div class="contentWrap"></div>
		
		</div>

		<div id="displaybox" onclick="beta();" style="display: none;"></div>

		<div class="container">
			<div class="span-21">&nbsp;</div>
			<div class="span-2"><h1><i><span class="title-text">BETA</span></i></h1></div>
			<div class="span-24"><center><hr/></center></div>
			<div class="span-3">
				<h1><span class="title-text">lctn.me</span></h1>
			</div>
			<div class="span-9">
				<h4><i><span class="title-text">Find it, share it</span></i></h4>
			</div>
			<div class="span-12 last">
				<div id="message"></div>
			</div>
			<div class="span-24"><hr/></div>
			
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
					Map
					</div>
	
					<div class="detail">
						<center>
	<!-- 						
						<div id="map" style="width: 40px; height: 40px;"></div>
	-->
						<div id="map" style="width: 468px; height: 465px;"></div>
						</center>
					</div>
					<div class="footer-text fixed-height-footer"></div>
				</div>
			</div>


			<div id="view-container-right" class="span-12 last">

				<div id="streetview_container" class="child">
	
					<div class="header">

						<div class="header-left">
							<img class="container-navigation-icon" src="images/arrow-left.png" onclick="prevContainer('streetview_container')" title="Show previous view"/>
						</div>
						<div class="header-center" title="Shows the streetview at the current location, streetview is only available in certain locations.">
							Streetview
						</div>
						<div class="header-right">
							<img class="container-navigation-icon" src="images/arrow-right.png" onclick="nextContainer('streetview_container')"/>
						</div>

					</div>
					<div class="detail">
						<div id="streetview" style="width: 465px; height: 465px"></div>
					</div>
					<div class="footer-text fixed-height-footer"></div>
				</div>
	
				<div id="twitter_container" class="child">
	
					<div class="header">
						<div class="header-left">
							<img class="container-navigation-icon" src="images/arrow-left.png" onclick="prevContainer('twitter_container')"/>
						</div>
						<div class="header-center"  title="Shows tweets in the surrounding area. Will only show tweets where people have chosen to share the location.">
							Twitter
						</div>
						<div class="header-right">
							<img class="container-navigation-icon" src="images/arrow-right.png" onclick="nextContainer('twitter_container')"/>
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
							<img class="container-navigation-icon" src="images/arrow-left.png" onclick="prevContainer('wiki_container')"/>
						</div>
						<div class="header-center" title="Shows all wikipedia articles in the area.">
							Wikipedia
						</div>
						<div class="header-right">
							<img class="container-navigation-icon" src="images/arrow-right.png" onclick="nextContainer('wiki_container')"/>
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
							<img class="container-navigation-icon" src="images/arrow-left.png" onclick="prevContainer('general_container')"/>
						</div>
						<div class="header-center" title="Shows general information about the location. Currently only supports weather, but more to come.">
							General
						</div>
						<div class="header-right">
							<img class="container-navigation-icon" src="images/arrow-right.png" onclick="nextContainer('general_container')"/>
						</div>
				    </div>
					<div class="detail-padded fixed-height-block">
						<div id="general_stream"></div>
					</div>
					<div class="footer-text fixed-height-footer"></div>
				</div>
			</div>
			<div class="span-24">&nbsp;</div>
			<div class="span-24"><hr/></div>
			
			<div class="span-1"><a href="about.php" rel="#overlay">About</a></div>
			<div class="span-1"><a href="contact.php" rel="#overlay">Contact</a></div>
			<div class="span-21">&nbsp;</div>
			<div class="span-1 last">0.0.1</div>
			<div class="span-24">&nbsp;</div>
			

		</div>

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

	</body>

</html>
