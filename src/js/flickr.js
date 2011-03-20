var listOfFlickr = [];
var flickrFound = false;
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
function getFlickrs(selectedLocation, range) {
	listOfFlickr = [];
	jQuery(function() {
		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.src = "http://api.flickr.com/services/rest?method=flickr.photos.search&api_key=" + flickrApiKey + "&lat=" + selectedLocation.lat() + "&lon=" + selectedLocation.lng() + "&radius=" + range + "&extras=geo&format=json&jsoncallback=jsonFlickrApi()";
		$("body").append(script);
	});
}

/**
 * Called after data retrieves from flickr service. Loads the JSON results of
 * the flickr search into an array.
 * 
 * @param data -
 *          flickr data.
 */
function jsonFlickrApi(data) {
	var shtml = '';
	var photos = data.photos;
	
	var output = "";
	var i = 0;
	
	if (photos) {
		$.each(photos.photo, function(index, value) {

			output = "<tr data-latitude=\"" + value.latitude + "\" data-longitude=\"" + value.longitude	+ "\" data-image=\"/images/twitter-icon.png\" data-shadow-image=\"/images/twitter-icon-shadow.png\">";
			output += "<td><img class='twitter-pic' src='";
			output += "http://farm" + value.farm + ".static.flickr.com/" + value.server + "/" + value.id + "_" + value.secret + "_s.jpg";
			output += "'/></td>";
			output += "<tr>";
			
			listOfFlickr[i] = output;
			i++;
		});

		if (listOfFlickr.length == 0) {
			listOfFlickr[0] = "No pictures found, reasons for this include:<ul><li>Search area being too small, try a bigger search area.</li><li>The Flickr Search Service may be experiencing problems, try again later.</li></ul>";
		} else {
			flickrFound = true;
		}

		updateFlickrDisplay();
	}
}

/**
 * Triggers the retrieval of flickr pictures based on selectedLocation
 */
function updateFlickrLocationInformation() {
	if (!(selectedLocation.lat() == 0 || selectedLocation.lng() == 0)) {
		$("#flickr_stream").html("<img class='spinner' src='images/spinner.gif' alt='...' title='Looking for pictures'/>");
		getFlickrs(selectedLocation, $("#flickr_range").val());
	}
}

/**
 * Loads the flickr container with data from the listOfFlickr array.
 */
function updateFlickrDisplay() {
	var output = "<table>";
	for (i = 0; i < listOfFlickr.length; i++) {
		output += listOfFlickr[i];
	}
	output += "</table>";
	$("#flickr_stream").html(output);
	$("table tr", "#flickr_stream").hover(function() {
		highlightRow($(this));
	}, function() {
		normalRow($(this));
	});
	updateFlickrFooter();
}

/**
 * Updates the footer information at the bottom of the flickr container
 */
function updateFlickrFooter() {
	$("#flickr_footer").html(flickrFound ? "<center>" + listOfFlickr.length + " pictures</center>" : "<center>No Pictures</center>");
}
