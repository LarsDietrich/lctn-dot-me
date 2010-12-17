<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

	<head>
	    <title>lctn.me | A Location Portal - Find it, Share it</title>

		<link rel="stylesheet" href="css/screen.css" type="text/css"/>
		<link rel="stylesheet" href="css/layout.css" type="text/css"/>
		<link rel="stylesheet" href="css/displaybox.css" type="text/css"/>
		<link rel="stylesheet" href="css/jquery-tools.css" type="text/css"/>
		<link rel="stylesheet" href="css/menu.css" type="text/css"/>

	</head>

	

	<body onload="load()">

		<!-- overlay element -->
		<div class="apple_overlay" id="overlay">
			<!-- the external content is loaded inside this tag -->
			<div class="contentWrap"></div>
		</div>

		<div id="displaybox" onclick="beta();" style="display: none;"></div>

		<?php include("menu.php")?>
<!-- 		
		<div class="header-name">
			<div style="font-size: xx-large;display:inline" class="title-text">lctn.me</div>
			<div style="font-size: small; display:inline" class="title-text">Find it, share it</div>
		</div>
-->
		
		<?php 
//			include("container/find.php");
//			include("container/share.php");
//			include("container/map.php");
//			if ($_COOKIE['option_twitter'] == "true") {
//				include("container/twitter.php");
//			}
//			if ($_COOKIE['option_streetview'] == "true") {
//				include("container/streetview.php");
//			}
//			if ($_COOKIE['option_wiki'] == "true") {
//				include("container/wiki.php");
//			}
//			if ($_COOKIE['option_general'] == "true") {
//				include("container/general.php");
//			}
		?>

		<?php 
			include("container/find.php");
			include("container/share.php");
			include("container/map.php");
			include("container/twitter.php");
			include("container/streetview.php");
			include("container/wiki.php");
			include("container/general.php");
		?>

		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
		<script type="text/javascript" src="http://cdn.jquerytools.org/1.2.5/all/jquery.tools.min.js"></script>
		<script type="text/javascript" src="http://www.google.com/jsapi"></script>
		<script type="text/javascript" src="https://www.google.com/jsapi?key=ABQIAAAANICyL01ax9PqYKeJwtOXfxTh05SPp9XRgWyeCyc0ee48nkavlxTTkteFyCb29mhFOfEeXVaj-F6hAw"></script>

 		<script type="text/javascript" src="js/gears_init.js"></script> 
		<script type="text/javascript" src="js/jxs.js"> </script>
		<script type="text/javascript" src="js/jquery-cookie.js"> </script>
		<script type="text/javascript" src="js/interface.js"></script>

		<script type="text/javascript" src="js/tweets.js"> </script>
		<script type="text/javascript" src="js/weather.js"> </script>
		<script type="text/javascript" src="js/wikipedia.js"> </script>
		<script type="text/javascript" src="js/timezone.js"> </script>
		<script type="text/javascript" src="js/uservoice.js"></script>
		<script type="text/javascript" src="js/main.js"></script>

		<script type="text/javascript" src="js/jquery.color.js"></script>

		<script type="text/javascript" src="js/menu.js"></script>

	</body>

</html>
