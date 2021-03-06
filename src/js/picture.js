var listOfPicture = [];
var pictureFound = false;
var flickrApiKey = "ae3b1fba3889813580d536b3ed159fa6";
var pictureRange;
var pictureLocation;
var panoramioStart = 0;
var maxPanoramioPics = 50;
var kmToMinuteRatio = 0.54;

/**
 * Flickr:
 * Site: http://www.flickr.com/services/api/flickr.photos.search.html
 * Call: http://api.flickr.com/services/rest?method=flickr.photos.search&api_key=ae3b1fba3889813580d536b3ed159fa6&lat=48.8&lon=2.3&radius=32
 *
 * Instagram:
 * Site: http://instagr.am/developer
 * Call: https://api.instagram.com/v1/media/search?lat=48.858844&lng=2.294351&distance=5000&client_id=7d1d783e68dd4dd5b7dbad1ad5316b6d
 * 
 * Panoramio:
 * 
 * Site: http://www.panoramio.com/api/data/api.html
 * Call: http://www.panoramio.com/map/get_panoramas.php?set=public&from=0&to=20&minx=-180&miny=-90&maxx=180&maxy=90&size=medium&mapfilter=true&callback=dostuff()
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
	pictureRange = range;
	pictureLocation = selectedLocation;
	listOfPicture = [];
	jQuery(function() {
		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.src = "http://api.flickr.com/services/rest?method=flickr.photos.search&api_key=" + flickrApiKey + "&lat=" + pictureLocation.lat() + "&lon="
				+ pictureLocation.lng() + "&radius=" + pictureRange + "&extras=geo,description&format=json&jsoncallback=jsonFlickrApi()";
		$("body").append(script);
	});
}

/**
 * Retrieve Instagram data from service.
 */
function getInstagram() {
	query = "feed/instagram.php?lat=" + pictureLocation.lat() + "&lng=" + pictureLocation.lng() + "&range=" + pictureRange;
	jx.load(query, function(data) {
		processInstagramData(data);
	}, "json");
}

/**
 * Retrieve Panoramio data from service
 */
function getPanoramio(start) {
	if (!(start)) {
		start = 0;
	}
	panoramioStart = start;
	var range = (pictureRange * kmToMinuteRatio) / 60;
	jQuery(function() {
		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.src = "http://www.panoramio.com/map/get_panoramas.php?set=full&mapfilter=true&from=" + start + "&to=" + (start + 50) + "&minx=" + (pictureLocation.lng() - range) + "&miny=" + (pictureLocation.lat() - range) + "&maxx=" + (pictureLocation.lng() + range) + "&maxy=" + (pictureLocation.lat() + range) + "&size=thumbnail&callback=processPanoramioData()";
		$("body").append(script);
	});	
}
	
/**
 * Called after data retrieved from flickr service. Loads the JSON results of
 * the flickr search into an array.
 * 
 * @param data -
 *          flickr data.
 */
function jsonFlickrApi(data) {
	var photos = data.photos;

	var output = "";

	if (photos) {
		$.each(photos.photo, function(index, value) {

			var cleanedLocation = Math.round(value.latitude * 10000) / 10000 + "," + Math.round(value.longitude * 10000) / 10000;

			output = "<tr data-latitude=\"" + value.latitude + "\" data-longitude=\"" + value.longitude
					+ "\" data-image=\"/images/camera.png\" data-shadow-image=\"/images/camera-shadow.png\">";
			output += "<td width='20%'>";
			
			output += "<div id='triggers'><img class='thumbnail' src='http://farm" + value.farm + ".static.flickr.com/" + value.server + "/" + value.id + "_" + value.secret
					+ "_s.jpg' rel='#picture_tag" + index + "'/></div>";

			output += "<div class='simple_overlay' id='picture_tag" + index + "'>";
			output += "<img src='http://farm" + value.farm + ".static.flickr.com/" + value.server + "/" + value.id + "_" + value.secret + "_b.jpg'/><br/>";
			output += "<span class='photo-footer'>Photo provided by Flickr.</span>";
			output += "</div>";
			
			output += "</td>";
			output += "<td><b>" + value.title + "</b><br/>" + value.description._content + "<br/>";
			output += "<div title=\"Reposition map to " + cleanedLocation
					+ "\" class=\"item-subtext-button inline\" style=\"cursor: pointer;\" onclick=\"useAddressToReposition('" + cleanedLocation + "')\">Center</div>";
			output += "&nbsp;&nbsp;<div title=\"Get directions to " + cleanedLocation
					+ "\" class=\"item-subtext-button inline\" style=\"cursor: pointer;\" onclick=\"getRouteToLocation('" + cleanedLocation + "')\">Go</div>";
			output += "</td></tr>";

			listOfPicture[index] = output;
		});
	}
	getInstagram();
}

/**
 * Called after retrieval of the instagram pictures, loads results into the
 * picture array
 * 
 * @param data - instagram JSON data
 */
function processInstagramData(data) {
	var photos = data.data;
	var startPosition = listOfPicture.length;
	var output = "";

	if (photos) {
		$.each(photos, function(index, value) {
			
			var cleanedLocation = Math.round(value.location.latitude * 10000) / 10000 + "," + Math.round(value.location.longitude * 10000) / 10000;

			output = "<tr data-latitude=\"" + value.location.latitude + "\" data-longitude=\"" + value.location.longitude
					+ "\" data-image=\"/images/camera.png\" data-shadow-image=\"/images/camera-shadow.png\">";
			output += "<td width='20%'>";
			output += "<div id='triggers'><img class='thumbnail' src='" + value.images.thumbnail.url + "' rel='#picture_tag" + (index + startPosition) + "'/></div>";
			output += "<div class='simple_overlay' id='picture_tag" + (index + startPosition) + "'>";
			output += "<img src='" + value.images.standard_resolution.url + "'/><br/>";
			output += "<span class='photo-footer'>Photo provided by Instagram.</span>";
			output += "</div>";
			
			output += "</td>";
			output += "<td><b>" + value.location.name + "</b><br/>";
			
			if (value.caption && value.caption.text) {
				output += value.caption.text + "<br/>";
			}
			
			output += "<div title=\"Reposition map to " + cleanedLocation
					+ "\" class=\"item-subtext-button inline\" style=\"cursor: pointer;\" onclick=\"useAddressToReposition('" + cleanedLocation + "')\">Center</div>";
			output += "&nbsp;&nbsp;<div title=\"Get directions to " + cleanedLocation
					+ "\" class=\"item-subtext-button inline\" style=\"cursor: pointer;\" onclick=\"getRouteToLocation('" + cleanedLocation + "')\">Go</div>";
			output += "</td></tr>";
			
			listOfPicture[index + startPosition] = output;
		});
	}
	getPanoramio();
}

/**
 * Called after retrieval of the panoramio pictures, loads results into the
 * picture array
 * 
 * @param data - panoramio JSON data
 */
function processPanoramioData(data) {

	var photos = data.photos;
	var startPosition = listOfPicture.length;
	var output = "";

	var count = data.count;

	if (photos && (panoramioStart < maxPanoramioPics)) {
		$.each(photos, function(index, value) {
			var cleanedLocation = Math.round(value.latitude * 10000) / 10000 + "," + Math.round(value.longitude * 10000) / 10000;
			output = "<tr data-latitude=\"" + value.latitude + "\" data-longitude=\"" + value.longitude + "\" data-image=\"/images/camera.png\" data-shadow-image=\"/images/camera-shadow.png\">";
			output += "<td width='20%'>";
			output += "<div id='triggers'><img class='thumbnail' src='" + value.photo_file_url + "' rel='#picture_tag" + (index + startPosition) + "'/></div>";
			output += "<div class='simple_overlay' id='picture_tag" + (index + startPosition) + "'>";
			
			output += "<img src='" + value.photo_file_url.replace("thumbnail", "medium") + "'/><br/>";
			
			output += "<span class='photo-footer'>Photo provided by <img style='height: 15px;padding-top: 5px;vertical-align: bottom; width: 95px;' src='/images/panoramio-logo.png'/>. Photos are under the copyright of their owners.</span>";
			output += "</div>";
			output += "</td>";
			output += "<td><b><a href='" + value.photo_url + "' target='_blank'>" + value.photo_title + "</a></b><br/>";
			output += "Added by <a href='" + value.owner_url + "' target='_blank'>" + value.owner_name + "</a> on " + value.upload_date + "<br/>";
			output += "<div title=\"Reposition map to " + cleanedLocation
					+ "\" class=\"item-subtext-button inline\" style=\"cursor: pointer;\" onclick=\"useAddressToReposition('" + cleanedLocation + "')\">Center</div>";
			output += "&nbsp;&nbsp;<div title=\"Get directions to " + cleanedLocation
					+ "\" class=\"item-subtext-button inline\" style=\"cursor: pointer;\" onclick=\"getRouteToLocation('" + cleanedLocation + "')\">Go</div>";
			output += "</td></tr>";
			listOfPicture[index + startPosition] = output;
		});
		
		getPanoramio(panoramioStart + 50);

		if (listOfPicture.length == 0) {
			listOfPicture[0] = "No pictures found, reasons for this include:<ul><li>Search area being too small, try a bigger search area.</li><li>The Picture Search Service may be experiencing problems, try again later.</li></ul>";
		} else {
			pictureFound = true;
		}
	}
	updatePictureDisplay();
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
