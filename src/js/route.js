var listOfRoute = [];
var directionsDisplay = new google.maps.DirectionsRenderer();
var directionsService = new google.maps.DirectionsService();
var totalDistance;
var totalDuration;

/**
 * Generate a route
 * 
 * The JSON request is build up like this:
 */
function getRoute(start, end) {
	listOfRoute = [];
	var directionsService = new google.maps.DirectionsService();
	directionsDisplay.setMap(map);
	var request = {
		origin : start,
		destination : end,
		travelMode : google.maps.DirectionsTravelMode.DRIVING
	};
	directionsService.route(request, function(result, status) {
		processRouteLookup(result, status, start, end);
	});

}

/**
 * Process the route data returned, convert to array.
 */
function processRouteLookup(result, status, start, end) {
	if (status == google.maps.DirectionsStatus.OK) {
		directionsDisplay.setDirections(result);

		var myRoute = result.routes[0].legs[0];
		totalDistance = myRoute.distance.value / 1000;
		totalDuration = myRoute.duration.value;

		for ( var i = 0; i < myRoute.steps.length; i++) {
			var location = myRoute.steps[i].start_point.lat() + "," + myRoute.steps[i].start_point.lng();
			var output = "<tr data-latitude=\"" + myRoute.steps[i].start_point.lat() + "\" data-longitude=\"" + myRoute.steps[i].start_point.lng()
					+ "\" data-image=\"\" class=\"pointer\" onmouseover='highlightRow(this)' onmouseout='normalRow(this)' onclick='zoomToPoint(" + location + ")'><td>";
			output += myRoute.steps[i].instructions;
			if (i < myRoute.steps.length - 1) {
				var distance = myRoute.steps[i].distance.value;
				if (distance < 1000) {
					output += ", travel for <b>" + distance + " meters</b> then... "
				} else {
					output += ", travel for <b>" + (distance / 1000) + " km</b> then... "
				}
			} else {
				var distance = myRoute.steps[i].distance.value;
				if (distance < 1000) {
					output += "after <b>" + distance + " meters</b>"
				} else {
					output += "after <b>" + (distance / 1000) + " km</b> "
				}
			}
			output += "</td></tr>";
			listOfRoute[i] = output;
		}

	} else {
		setMessage("Route lookup failed: " + status);
	}
	updateRouteDisplay(start, end);
}

/**
 * Updates the "route" information window
 */
function updateRouteInformation(start, end) {
	if (!start) {
		start = $("#route_from").val();
	}
	if (!end) {
		end = selectedLocation.lat() + "," + selectedLocation.lng();
	}
	if (start && end) {
		$("#route_stream").html("<img class='spinner' src='images/spinner.gif' alt='...' title='Loading route'/>");
		getRoute(start, end);
	}
}

/**
 * Load the route display based on whats in route array.
 */
function updateRouteDisplay(start, end) {
	var output = "<table>";
	if (listOfRoute.length == 0) {
		output += "<tr><td>I have no idea how to get from <b><div class=\"inline\" id=\"route_start\">" + start
				+ "</div></b> to <b><div class=\"inline\" id=\"route_end\">" + end + "</div></b>, try a different location.</td</tr>";
	} else {
		output += "<tr><td>To get from <b><div class=\"inline\" id=\"route_start\">" + start + "</div></b> to <b><div class=\"inline\" id=\"route_end\">" + end
				+ "</div></b>, a distance of about <b>" + totalDistance + "km</b></td</tr>";
		for (i = 0; i < listOfRoute.length; i++) {
			output += listOfRoute[i];
		}
	}
	output += "</table>";
	$("#route_stream").html(output);
	renderAddress("start");
	renderAddress("end");
}

/**
 * Activates the Directions window and plots a route from the location specified
 * by "start" to the location specified.
 * 
 * @param location
 */
function getRouteToLocation(location) {
	if (!isEnabled("route")) {
		$("#route_container").css("display", "inline");
	}
	var start = selectedLocation.lat() + "," + selectedLocation.lng();
	$("#route_from").val($("#address").val());
	var end = location;
	updateRouteInformation(start, end);
}

/**
 * Renders the full address of selected location in the address box.
 * 
 * @param control -
 *          the control containing the location, comma seperated latitude then
 *          longitude
 */
function renderAddress(control) {
	var split = $("#route_" + control).html().split(",");
	var location = new google.maps.LatLng(split[0], split[1]);
	geocoder.geocode( {
		'latLng' : location
	}, function(results, status) {
		var address = "";
		if (status == google.maps.GeocoderStatus.OK) {
			if (results.length > 0) {
				address = results[0].formatted_address;
				$("#route_" + control).html(address);
			}
		}
	});
}
