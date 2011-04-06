<div class="topmenu">	
	<ul class="topnav">
	    <li><a href="about.php" rel="#overlay"><label class="config">About</label></a></li>
	    <li><a href="contact/contact.php" rel="#overlay"><label class="config">Contact</label></a></li>
	    <li><img src="/images/topnav_bg_divider.png"/></li>
	    <li><a><label class="config" title="Try to find your current location, dependant on browser capabilities, may not always work or be accurate." onclick="findMe()">Find Me</label></a></li>

	    <li><img src="/images/topnav_bg_divider.png"/></li>

		<li><a><label onclick="setConfigOption(this);" id="general" title="Open to see some localized information about the location. Shows weather, coordinates and time related information." class="config" >Local</label></a></li>
 		<li><a><label title="Show places of interest around the current location. Data supplied by Foursquare." id="places" class="config" onclick="setConfigOption(this);">Places</label></a></li>
		<li><a><label title="Get directions between two points, updates the map to show the route and gives you a friendly route description." id="route" class="config" onclick="setConfigOption(this);">Directions</label></a></li>
		<li><a><label title="Show you a street view of the chosen location. Not available everywhere." id="streetview" class="config" onclick="setConfigOption(this);">Streetview</label></a></li>
		<li><a><label title="Wikipedia articles in the area with short description, click to link through to actual article. Can filter by range." id="wiki" class="config" onclick="setConfigOption(this);">Wikipedia</label></a></li>
		<li><a><label title="Show tweets by people that have enabled location based tweets. Can filter on range and text." id="twitter" class="config" onclick="setConfigOption(this);">Twitter</label></a></li>
		<li><a><label title="Show publically available photos in the area (Uses Flickr and Instagram services). Can filter by range." id="picture" class="config" onclick="setConfigOption(this);">Photos</label></a></li>
		<li><a><label title="See still image and time capture webcams in the area. Can filter by range." id="webcam" class="config" onclick="setConfigOption(this);">Webcams</label></a></li>
	    <li><img src="/images/topnav_bg_divider.png"/></li>
		<?php if (isset($_COOKIE["fbs_179640572057712"])) {?>
<!-- 			<li><a><label title="Personalized information about what you've done" id="user" class="config" onclick="setConfigOption(this);">You</label></a></li>
-->
		<?php }?>

	</ul>
</div>