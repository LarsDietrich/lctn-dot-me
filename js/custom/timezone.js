/**
 * Generate timezone entries based on location
 * 
 * The JSON request is build up like this:
 *
 * SAMPLE: 
 *
 * {
 * "time": "2010-11-06 22:57",
 * "countryName": "Austria",
 * "sunset": "2010-11-06 16:56",
 * "rawOffset": 1,
 * "dstOffset": 2,
 * "countryCode": "AT",
 * "gmtOffset": 1,
 * "lng": 10.2,
 * "sunrise": "2010-11-06 07:08",
 * "timezoneId": "Europe/Vienna",
 * "lat": 47.01
 * }
 */
function getTimezone(selectedLocation) {
	var query = "timezone.php?lat=" + selectedLocation.lat() + "&lng=" + selectedLocation.lng();
	jx.load(query, function(jsonData) {processTimezoneData(jsonData);}, "json");
}

function processTimezoneData(jsonData) {
		var time = jsonData.time.substring(11);
		var sunrise = jsonData.sunrise.substring(11);
		var sunset = jsonData.sunset.substring(11);
		output = "The time at this location is <b>" + time + "</b>, sunrise will be at <b>" + sunrise + "</b> and sunset at <b>" + sunset + "</b>.";
	document.getElementById("timezone_stream").innerHTML = output;
}

/**
 * Triggers the retrieval of the timezone information based on
 * selectedLocation.
 */
function updateTimezoneLocationInformation() {
	if (!(selectedLocation.lat() == 0 || selectedLocation.lng() == 0)) {
		document.getElementById("timezone_stream").innerHTML = "<img class='spinner' src='images/spinner.gif' alt='...' title='Loading timezone information'/>";
		getTimezone(selectedLocation);
	}
}
