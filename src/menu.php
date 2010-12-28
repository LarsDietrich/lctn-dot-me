	<ul class="topnav">
	    <li><a href="about.php" rel="#overlay">About</a></li>
	    <li><a href="contact/contact.php" rel="#overlay">Contact</a></li>
		<?php //include("openid/google.php")?>
	    <li><img src="/images/topnav_bg_divider.png"/></li>
		<li><a><label onclick="setConfigOption(this);" id="general" title="The window containing general information" class="config" >General</label></a></li>
		<li><a><label title="Toggle the window showing the streetview of the current location (if available)" id="streetview" class="config" onclick="setConfigOption(this);">Streetview</label></a></li>
		<li><a><label title="Toggle the window showing tweets around the current location" id="twitter" class="config" onclick="setConfigOption(this);">Twitter</label></a></li>
		<li><a><label title="Toggle the window showing webcams around the location" id="webcam" class="config" onclick="setConfigOption(this);">Webcams</label></a></li>
		<li><a><label title="Toggle the window showing wikipedia articles around the location" id="wiki" class="config" onclick="setConfigOption(this);">Wikipedia</label></a></li>
	    <li><img src="/images/topnav_bg_divider.png"/></li>
		<li><div class="message" id="message"></div></li>
	</ul>
	