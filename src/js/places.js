var listOfPlaces = [];
var placesFound = false;
var placesCategory;

/**
 * Generate Places based on location
 * 
 * From: http://simplegeo.com/
 * {
 * 	"total":25,
 * 	"type":"FeatureCollection",
 * 	"features":
 * 		[{"geometry":
 * 			{	"type":"Point",
 * 				"coordinates":[-87.690362,42.037728]
 * 			},
 * 			"type":"Feature",
 * 			"id":"SG_2Op05JLFDH0SQDfqKYfJB5_42.037728_-87.690362@1291672243",
 * 			"properties":
 * 					{	"province":"IL",
 * 						"city":"Evanston",
 * 						"name":"Idg",
 * 						"tags": ["commercial","design","typesetting"],
 * 						"country":"US",
 * 						"phone":"+1 847 475 7772",
 * 						"href":"http://api.simplegeo.com/1.0/features/SG_2Op05JLFDH0SQDfqKYfJB5_42.037728_-87.690362@1291672243.json",
 * 						"address":"1327 Greenleaf St",
 * 						"owner":"simplegeo",
 * 						"classifiers":
 * 							[{"category":"Manufacturing",
 * 								"type":"Manufacturing & Wholesale Goods",
 * 								"subcategory":"Printing"}],
 * 						"postcode":"60202"}},
 */
function getSimpleGeoPlaces(selectedLocation, range, category, query) {
	var client = new simplegeo.PlacesClient('NXBFea3eNcBf3MbgjuTgCF6sSWyLQVKX');
	placesCategory = category;
	listOfPlaces = [];
	var placesOptions = {
			radius : range,
			category : category,
			q: query
	}
	client.search(selectedLocation.lat(), selectedLocation.lng(), placesOptions, function(err, data) {processSimpleGeoPlacesResults(err, data)});	
}

/**
 * Process the weather data returned, convert to array.
 * 
 * @param jsonData -
 *          data returned
 */
function processSimpleGeoPlacesResults(error, data) {
	if (error) {
		var output = "<tr><td>";
		output += error;
		output += "</td></tr>";
		listOfPlaces[0] = output;
	} else {
		var i = 0;
		var features = data.features;

		$.each(features, function(index, value) {
			var properties = value.properties;
			var coordinates = value.geometry.coordinates;
			var output = "<tr onmouseover='highlightLngLatRow(this," + coordinates + ", \"images/" + placesCategory + ".png\")' onmouseout='normalRow(this)'>";
			output += "<td>";
			output += "<b>" + properties.name + "</b><br/>";
			if (properties.phone) { output += "Tel: " + properties.phone + "<br/>"; } else { output += "Phone: Not Listed<br/>";}
			if (properties.address) { output += "Address: " + properties.address + "," + properties.city + "<br/>"; } else { output += "Address: Not Listed<br/>";}
			output += "<div title=\"Reposition to this location\" class=\"item-subtext inline\" style=\"cursor: pointer;\" onclick=\"useAddressToRepositionLngLat('" + value.geometry.coordinates	+ "')\">Go There!</div>&nbsp;|&nbsp;";
			output += "<a href=\"";
			output += "http://www.google.com/search?hl=en&q=%22" + properties.name + "%22&btnG=Google+Search";
			output += "\" target=\"_blank\"><div title=\"Find out more\" class=\"item-subtext inline\" style=\"cursor: pointer;\">Google It!</div></a>";
			output += "</td></tr>";
			listOfPlaces[i] = output;
			i++;
		});
	}
	if (listOfPlaces.length == 0) {
		listOfPlaces[0] = "No places found, reasons for this include:<ul><li>No places matched the name you supplied or</li><li>No places were found matching the category you chose or</li><li>Even though we are continually adding new places, we may not have places for the current location you chose.</li></ul>";
	} else {
		placesFound = true;
	}
	updatePlacesDisplay();
}

/**
 * Updates the "Places" information
 */
function updatePlacesLocationInformation() {
	if (!(selectedLocation.lat() == 0 || selectedLocation.lng() == 0)) {
		$("#places_stream").html("<img class='spinner' src='images/spinner.gif' alt='...' title='Looking for places of interest in the area.'/>");
		getSimpleGeoPlaces(selectedLocation, 100, $("#places_category").val(), "");
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
	$("#places_stream").html(output);
	updatePlacesFooter();
}

/**
 * Updates the footer information at the bottom of the places container
 */
function updatePlacesFooter() {
	$("#places_footer").html(placesFound?"<center>" + listOfPlaces.length + " places</center>" : "<center>No places</center>");
}
