<?php require ("includes/functions.php")?>
<?php if (!(isset($_POST["shorten"]))) { ?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
   "DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

	<head>
	    <title>lctn.me</title>
	
		<script type="text/javascript"
			src="http://maps.google.com/maps/api/js?sensor=true">
		</script>
		
		<script type="text/javascript">
			var map;
			function load() { 
			  var myLatlng = new google.maps.LatLng(-25.363882,131.044922);
			  var myOptions = {
			    zoom: 4,
			    center: myLatlng,
			    mapTypeId: google.maps.MapTypeId.ROADMAP
			  }
			  
			  map = new google.maps.Map(document.getElementById("map"), myOptions);

			  google.maps.event.addListener(map, 'click', function(event) {
				  url = buildUrl(event.latLng);
				  document.getElementById('url').value=url;

				  var marker = new google.maps.Marker({
				      position: event.latLng, 
				      map: map
				  });
			  });
			}
		  
			function buildUrl(location) {
//				result = "http://maps.google.com/maps/api/staticmap?";
//				result = result + "zoom=14&size=300x200&sensor=false";
//				result = result + "&center=" + location.lat() + "," + location.lng();
//				result = result + "&markers=color:blue|label:Test|" + location.lat() + "," + location.lng();

				result = "http://test.lctn.me/map.php?lat=" + location.lat() + "&lng=" + location.lng();
				
				return result;
			}
		
		</script>
	
	</head>
	<body onload="load()" onunload="GUnload()">
		
		<div id="map" style="width: 640px; height: 480px"></div>
	
		<br/>
	
		<form id="urlShortener" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<strong> Url:</strong>
			<input id="url" name="url" size="45" type="text">
			<input id="Submit" name="shorten" value="shorten" type="submit">
		</form>
	
	</body>

</html>


<?php } else { ?>

<?php 

echo "Your shortened URL is: http://test.lctn.me/" . getShortUrl($_POST["url"]) 

?>

<?php } ?>