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
	</head>

	<body onload="load()">

		<!-- overlay element -->
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
							Find me articles within <input title="How big an area would you like to see wikipedia articles for?" class="short-text" type="text" name="wiki_range" id="wiki_range" value="1" onkeypress="if (event.keyCode == 13) { updateWikiLocationInformation(); }"/> km
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
		<script type="text/javascript" src="http://cdn.jquerytools.org/1.2.5/all/jquery.tools.min.js"></script>
		<script type="text/javascript" src="http://www.google.com/jsapi"></script>
		<script type="text/javascript" src="https://www.google.com/jsapi?key=ABQIAAAANICyL01ax9PqYKeJwtOXfxTh05SPp9XRgWyeCyc0ee48nkavlxTTkteFyCb29mhFOfEeXVaj-F6hAw"></script>

 		<script type="text/javascript" src="js/gears_init.js"></script> 
		<script type="text/javascript" src="js/jxs.js"> </script>
		<script type="text/javascript" src="js/jquery-cookie.js"> </script>

		<script type="text/javascript" src="js/tweets.js"> </script>
		<script type="text/javascript" src="js/weather.js"> </script>
		<script type="text/javascript" src="js/wikipedia.js"> </script>
		<script type="text/javascript" src="js/timezone.js"> </script>
		<script type="text/javascript" src="js/uservoice.js"></script>
		<script type="text/javascript" src="js/main.js"></script>

	</body>

</html>
