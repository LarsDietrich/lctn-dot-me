var listOfPicture = [];
var pictureFound = false;
var flickrApiKey = "ae3b1fba3889813580d536b3ed159fa6";

/**
 * 
 * http://api.flickr.com/services/rest?method=flickr.photos.search&api_key=ae3b1fba3889813580d536b3ed159fa6&lat=48.8&lon=2.3&radius=32
 * 
 * http://www.flickr.com/services/api/flickr.photos.search.html
 * 
 * Entry method for generating pictures, calls the flickr service using selected
 * parameters. Once complete, calls a method to process the results.
 * 
 * @param selectedLocation -
 *          the location to check for pictures.
 * @param range -
 *          the range of pictures to find, in km
 */
function getPictures(selectedLocation, range) {
	listOfPicture = [];
	jQuery(function() {
		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.src = "http://api.flickr.com/services/rest?method=flickr.photos.search&api_key=" + flickrApiKey + "&lat=" + selectedLocation.lat() + "&lon=" + selectedLocation.lng() + "&radius=" + range + "&extras=geo,description&format=json&jsoncallback=jsonFlickrApi()";
		$("body").append(script);
	});
}

/**
 * Called after data retrieved from flickr service. Loads the JSON results of
 * the flickrsearch into an array.
 * 
 * @param data -
 *          flickr data.
 */
function jsonFlickrApi(data) {
	var shtml = '';
	var photos = data.photos;
	
	var output = "";
	
	if (photos) {
		$.each(photos.photo, function(index, value) {

		  var cleanedLocation = Math.round(value.latitude * 10000) / 10000 + "," + Math.round(value.longitude * 10000) / 10000;

			output = "<tr data-latitude=\"" + value.latitude + "\" data-longitude=\"" + value.longitude	+ "\" data-image=\"/images/camera.png\" data-shadow-image=\"/images/camera-shadow.png\">";
			output += "<td width='20%'>";
			output += "<div id='triggers'><img src='http://farm" + value.farm + ".static.flickr.com/" + value.server + "/" + value.id + "_" + value.secret + "_s.jpg' rel='#picture_tag" + index + "'/></div>";
			output += "<div class='simple_overlay' id='picture_tag" + index + "'>"; 
			output += "<img src='http://farm" + value.farm + ".static.flickr.com/" + value.server + "/" + value.id + "_" + value.secret + "_b.jpg'/>";
			output += "</div>";
			output += "</td>";
			output += "<td><b>" + value.title + "</b><br/>" + value.description._content + "<br/>";
			output += "<div title=\"Reposition map to " + cleanedLocation + "\" class=\"item-subtext inline\" style=\"cursor: pointer;\" onclick=\"useAddressToReposition('" + cleanedLocation + "')\">Center There!</div>";
			output += "&nbsp;|&nbsp;<div title=\"Get directions to " + cleanedLocation + "\" class=\"item-subtext inline\" style=\"cursor: pointer;\" onclick=\"getRouteToLocation('" + cleanedLocation + "')\">Go There!</div>";
			output += "</td></tr>";
			
			listOfPicture[index] = output;
		});

		if (listOfPicture.length == 0) {
			listOfPicture[0] = "No pictures found, reasons for this include:<ul><li>Search area being too small, try a bigger search area.</li><li>The Picture Search Service may be experiencing problems, try again later.</li></ul>";
		} else {
			pictureFound = true;
		}

		updatePictureDisplay();
	}
}

/**
 * Triggers the retrieval of pictures based on selectedLocation
 */
function updatePictureLocationInformation() {
	if (!(selectedLocation.lat() == 0 || selectedLocation.lng() == 0)) {
		$("#picture_stream").html("<img class='spinner' src='images/spinner.gif' alt='...' title='Looking for pictures'/>");
		getPictures(selectedLocation, $("#picture_range").val());
	}
}

/**
 * Loads the picture container with data from the listOfPicture array.
 */
function updatePictureDisplay() {
	var output = "<table>";
	for (i = 0; i < listOfPicture.length; i++) {
		output += listOfPicture[i];
	}
	output += "</table>";
	$("#picture_stream").html(output);
	$("img[rel*='picture_tag']").overlay();
	$("table tr", "#picture_stream").hover(function() {
		highlightRow($(this));
	}, function() {
		normalRow($(this));
	});
	updatePictureFooter();
}

/**
 * Updates the footer information at the bottom of the picture container
 */
function updatePictureFooter() {
	$("#picture_footer").html(pictureFound ? "<center>" + listOfPicture.length + " pictures</center>" : "<center>No Pictures</center>");
}
