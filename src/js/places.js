var listOfPlaces = [];
var placesFound = false;

/**
 * Generate Places based on location using Foursquare Venues API
 * 
 * http://developer.foursquare.com/venues/
 * 
 * 
 * 
 */
function getPlaces(selectedLocation, category, query) {
	listOfPlaces = [];
	query = "feed/places.php?lat=" + selectedLocation.lat() + "&lng=" + selectedLocation.lng() + "&category=" + category;
	jx.load(query, function(data) {
		processPlacesData(data);
	}, "json");
}

/**
 * Process the weather data returned, convert to array.
 * 
 * @param jsonData -
 *          data returned
 */
function processPlacesData(data) {
	
	var nearbyPlaces = data.response.groups[0].items;

	var output = "";

	if (nearbyPlaces) {
		$.each(nearbyPlaces, function(index, value) {

			latitude = value.location.lat;
			longitude = value.location.lng;

			var cleanedLocation = latitude + "," + longitude;
			
			var iconPath = "images/foursquare-icon.png";
			var checkins = 0;

			if (value.categories[0]) {
				iconPath = value.categories[0].icon;
			}
			
			output = "<tr data-latitude='" + latitude + "' data-longitude='" + longitude	+ "' data-image='" + 	iconPath + "' data-shadow-image=''><td>";
			output += "<b>" + value.name + "</b><br/>";

			if (value.categories[0]) {
				output += "<span class=\"hashtag\" onclick=\"updatePlacesLocationInformationFromCategory('" + value.categories[0].id + "')\">" + value.categories[0].name + "</span><br/>";
			}

			
			if (value.location.address) {			
				output += value.location.address;
			} else {
				output += "No address listed.";
			}
			
			output += "<br/>";
			
			if (value.stats.checkinsCount) {
				checkins = value.stats.checkinsCount; 
			}

			
			output += "<div class='item-subtext inline'>" + checkins + " visits</div>";
			output += "&nbsp;&nbsp;<div title='Reposition map to " + cleanedLocation	+ "' class='item-subtext-button inline' style='cursor: pointer;' onclick='useAddressToReposition(\"" + cleanedLocation + "\")'>Center</div>";
			output += "&nbsp;&nbsp;<div title='Get directions to " + cleanedLocation + "' class='item-subtext-button inline' style='cursor: pointer;' onclick='getRouteToLocation(\"" + cleanedLocation + "\")'>Go</div>";
			output += "&nbsp;&nbsp;<div title='Google this place' style='text-decoration:none' class='item-subtext-button inline' style='cursor: pointer;'><a href='http://www.google.com/search?q=" + value.name + "' target='_blank'>Google</a></div>";
			output += "</td></tr>";
			
			listOfPlaces[index] = output;
		});
	}
	
	if (listOfPlaces.length == 0) {
		listOfPlaces[0] = "No places found.";
	} else {
		placesFound = true;
	}

	updatePlacesDisplay();
}

/**
 * Updates the "Places" information
 */
function updatePlacesLocationInformation() {
	updatePlacesLocationInformationFromCategory($("#places_category").val());
}

/**
 * Updates the "Places" information
 */
function updatePlacesLocationInformationFromCategory(categoryId) {
	if (!(selectedLocation.lat() == 0 || selectedLocation.lng() == 0)) {
		$("#places_stream").html("<img class='spinner' src='images/spinner.gif' alt='...' title='Looking for places of interest in the area.'/>");
		getPlaces(selectedLocation, categoryId, "");
	}
}

/**
 * Loads the places container with data from the listOfPlaces array.
 */
function updatePlacesDisplay(page) {
	var output = "<table>";
	for (i = 0; i < listOfPlaces.length; i++) {
		output += listOfPlaces[i];
	}
	output += "</table>";
	output += "<div style='text-align=right'>Data by <a href='http://www.foursquare.com' target='_blank'>Foursquare</a></>";
	$("#places_stream").html(output);
	$("table tr", "#places_stream").hover(function() {
		highlightRow($(this));
	}, function() {
		normalRow($(this));
	});
	updatePlacesFooter();
}

/**
 * Updates the footer information at the bottom of the places container
 */
function updatePlacesFooter() {
	$("#places_footer").html(placesFound ? "<center>" + listOfPlaces.length + " places</center>" : "<center>No places</center>");
}
