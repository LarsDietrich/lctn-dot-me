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
		processRouteLookup(result, status);
	});

}

/**
 * Process the route data returned, convert to array.
 */
function processRouteLookup(result, status) {
	if (status == google.maps.DirectionsStatus.OK) {
		directionsDisplay.setDirections(result);

	  var myRoute = result.routes[0].legs[0];
	  
	  for (var i = 0; i < myRoute.steps.length; i++) {
	  	var location = myRoute.steps[i].start_point.lat() + "," + myRoute.steps[i].start_point.lng();
			var output = "<tr class=\"pointer\" onmouseover='highlightRow(this," + location	+ ", \"images/route-marker.png\")' onmouseout='normalRow(this)' onclick='zoomToPoint(" + location + ")'><td>";
	  	output += i + ".&nbsp;" + myRoute.steps[i].instructions;
	  	output += "</td></tr>";
	  	listOfRoute[i] = output;
	  }
	
	} else {
		setMessage("Route lookup failed: " + status);
	}
	updateRouteDisplay();
}

/**
 * Updates the "route" information
 */
function updateRouteInformation() {
	if (!(selectedLocation.lat() == 0 || selectedLocation.lng() == 0)) {
		$("#route_stream").html("<img class='spinner' src='images/spinner.gif' alt='...' title='Loading route'/>");
		var start = $("#route_from").val();
		var end = selectedLocation.lat() + "," + selectedLocation.lng();
		getRoute(start, end);
	}
}

/**
 * Load the route display based on whats in route array.
 */
function updateRouteDisplay() {
	var output = "<table>";
  output += "<tr><td>Directions from <b>" + $("#route_from").val() + "</b> to <b>" + $("#address").val() + "</b> :</td</tr>";
  output += "<tr><td><hr/></td</tr>";
	for (i = 0; i < listOfRoute.length; i++) {
		output += listOfRoute[i];
	}
	output += "</table>";
	$("#route_stream").html(output);
}

function zoomToPoint(lat, lng) {
	var point = new google.maps.LatLng(lat, lng);
	map.setCenter(point);
	map.setZoom(15);
}