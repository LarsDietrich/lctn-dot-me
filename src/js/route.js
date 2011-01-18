var listOfRoute = [];
var directionsDisplay = new google.maps.DirectionsRenderer();
var directionsService = new google.maps.DirectionsService();
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

		for ( var i = 0; i < myRoute.steps.length; i++) {
			var location = myRoute.steps[i].start_point.lat() + "," + myRoute.steps[i].start_point.lng();
			var output = "<tr class=\"pointer\" onmouseover='highlightRow(this," + location
					+ ", \"images/route-marker.png\")' onmouseout='normalRow(this)' onclick='zoomToPoint(" + location + ")'><td>";
			output += myRoute.steps[i].instructions;
			output += "</td></tr>";
			listOfRoute[i] = output;
		}

	} else {
		setMessage("Route lookup failed: " + status);
	}
	updateRouteDisplay(start, end);
}

/**
 * Updates the "route" information
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
		output += "<tr><td>I have no idea how to get from <b><div class=\"inline\" id=\"route_start\">" + start + "</div></b> to <b><div class=\"inline\" id=\"route_end\">" + end
				+ "</div></b>, try a different location.</td</tr>";
	} else {
		output += "<tr><td>To get from <b><div class=\"inline\" id=\"route_start\">" + start + "</div></b> to <b><div class=\"inline\" id=\"route_end\">" + end
				+ "</div></b></td</tr>";
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
 * Activates the Directions window and plots a route from the selected location
 * to the location specified.
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
