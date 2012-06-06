<!-- http://www.jankoatwarpspeed.com/post/2009/01/19/Create-Vimeo-like-top-navigation.aspx -->
<ul id="menu">
    <li class="logo">
        <img title="Built <?php echo date("l, dS F, Y @ h:ia", filemtime("index.php")); ?> GMT+2" style="float:left;" alt="" src="images/menu/menu_left.png"/>
<!-- 
        <ul id="main">
            <li>Welcome to <b>LCTN.ME</b>. Find it, Share it!</li>
        </ul>
-->
    </li>
    <li class="searchContainer">
        <div>
        <input title="Enter an address or place name to search for (eg. Eiffel Tower or 22 1st Avenue) then click Find or press Enter" type="text" class="find-address" name="address" id="address" value="Enter an address here" onkeypress="if (event.keyCode == 13) { locateAndRefresh(true);}"/>
        <img src="images/menu/magnifier.png" alt="Search" title="Find this location on the map" name="find" value="Find" onclick="locateAndRefresh(true);"/>
        </div>
<!-- 
        <ul id="search">
            <li><input id="search_google" type="checkbox" />Google Maps</li>
            <li><input id="search_foursquare" type="checkbox" />Foursquare</li>
        </ul>
-->
    </li>

    <li><span>View</span>
        <ul id="view">
	        <li>
	            <img class="corner_inset_left" alt="" src="/images/menu/corner_inset_left.png"/>
				<span id="route" class="config2" onclick="setConfigOption(this);">Directions</span>
	            <img class="corner_inset_right" alt="" src="/images/menu/corner_inset_right.png"/>
	        </li> 
       		<li>
       			<span onclick="setConfigOption(this);" id="general" class="config2" >Local</span>
       		</li>
			<li>
				<span id="picture" class="config2" onclick="setConfigOption(this);">Photos</span>
			</li>
			<li>
	 			<span id="places" class="config2" onclick="setConfigOption(this);">Places</span>
			</li>
			<li>
				<span id="streetview" class="config2" onclick="setConfigOption(this);">Streetview</span>
			</li>
			<li>
	            <span id="twitter" class="config2" onclick="setConfigOption(this);">Twitter</span>
			</li>
	 		<li>
				<span id="webcam" class="config2" onclick="setConfigOption(this);">Webcams</span>
	 		</li>
			<li>
				<span id="wiki" class="config2" onclick="setConfigOption(this);">Wikipedia</span>
			</li>
	        <li class="last">
	            <img class="corner_left" alt="" src="/images/menu/corner_left.png"/>
	            <img class="middle" alt="" src="/images/menu/dot.gif"/>
	            <img class="corner_right" alt="" src="/images/menu/corner_right.png"/>
	        </li>         
        </ul>
    </li>
    
    <li><span name="share" id="share" value="Share">Share</span>
	    <ul>
			<li>
	            <img class="corner_inset_left" alt="" src="/images/menu/corner_inset_left.png"/>
					<span onclick="shortenUrl(this);" data-baseurl="http://twitter.com/home/?status=">Twitter</span>
<!-- 						<img class='social-button' src="images/twitter.png" title="Tweet this location to the world." alt="Twitter"></img>
					</span>
-->
	            <img class="corner_inset_right" alt="" src="/images/menu/corner_inset_right.png"/>
			</li>

			<li>
			<span onclick="shortenUrl(this);" data-baseurl="http://www.facebook.com/sharer.php?u=">Facebook</span>
<!-- 
			<img class='social-button' src="images/facebook.png" title="Share the location with your Facebook friends." alt="Facebook"></img>
					</span>
-->
			</li>

			<li>
					<span onclick="shortenUrl(this);" data-baseurl="http://del.icio.us/post?url=">Delicious</span>
<!-- 
						<img class='social-button' src="images/delicious.png" title="Add the location to your Del.icio.us bookmarks." alt="Del.icio.us"></img>
					</span>
-->
			</li>

			<li>
					<span onclick="shortenUrl(this);" data-baseurl="mailto:?subject=Have a look at this location&body=">Email</span>
<!-- 
					<img class='social-button' src="images/email.png" title="Email the location to a friend." alt="Send by Email"></img>
					</span>
 -->
 			</li>
 			<li>
					<span onclick="shortenUrl(this);" data-baseurl="">Local</span>
 			</li>
 			
	        <li class="last">
	            <img class="corner_left" alt="" src="/images/menu/corner_left.png"/>
	            <img class="middle" alt="" src="/images/menu/dot.gif"/>
	            <img class="corner_right" alt="" src="/images/menu/corner_right.png"/>
	        </li>         
	    </ul>
    </li>
    
    <li>
    	<span title="Try a best guess effort to reposition the map to where you are" onclick="findMe()">FindMe</span>
    </li>
    <li>
		<a title="About lctn.me" href="about.php" rel="#overlay">About</a>
    </li> 
    <li><span >&nbsp;</span></li>
</ul>
