<div class="topmenu">	
	<ul class="topnav">
	    <li><a href="about.php" rel="#overlay"><label class="config">About</label></a></li>
	    <li><a href="contact/contact.php" rel="#overlay"><label class="config">Contact</label></a></li>
	    <li><img src="/images/topnav_bg_divider.png"/></li>
	    <li><a><label class="config" title="Try to find your current location, dependant on browser capabilities, may not always work or be accurate." onclick="findMe()">Find Me</label></a></li>

	    <li><img src="/images/topnav_bg_divider.png"/></li>

		<li><a><label onclick="setConfigOption(this);" id="general" title="Open to see some general information about the location. Currently shows weather, coordinates and time related information." class="config" >General</label></a></li>
 		<li><a><label title="Toggle the window showing places around the current location" id="places" class="config" onclick="setConfigOption(this);">Places</label></a></li>
		<li><a><label title="Open to get directions between two points, updates the map to show the route and gives you a friendly route description." id="route" class="config" onclick="setConfigOption(this);">Directions</label></a></li>
		<li><a><label title="Open this window to show you a street view of the chosen location. Not available everywhere." id="streetview" class="config" onclick="setConfigOption(this);">Streetview</label></a></li>
		<li><a><label title="Open to see wikipedia articles in the area with short description, click to link through to actual article. Can filter by range." id="wiki" class="config" onclick="setConfigOption(this);">Wikipedia</label></a></li>
		<li><a><label title="Based on your location, will show you tweets posted for people that have enabled location based tweets. Filter on range and text." id="twitter" class="config" onclick="setConfigOption(this);">Twitter</label></a></li>
		<li><a><label title="Show photos in the area based on the Flickr service." id="picture" class="config" onclick="setConfigOption(this);">Photos</label></a></li>
		<li><a><label title="Open to see available webcams in the area, mostly still image but some time capture video is available. Filter by range." id="webcam" class="config" onclick="setConfigOption(this);">Webcams</label></a></li>
	    <li><img src="/images/topnav_bg_divider.png"/></li>
		<?php if (isset($_COOKIE["fbs_179640572057712"])) {?>
			<li><a><label title="Personalized information about what you've done" id="user" class="config" onclick="setConfigOption(this);">You</label></a></li>
		<?php }?>

	</ul>
</div>