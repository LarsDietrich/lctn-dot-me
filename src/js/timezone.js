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

var color_morning = "ADD5F7";
var color_late_morning = "7FB2F0";
var color_midday = "4E7AC7";
var color_afternoon = "35478C";
var color_night = "000000";

function getTimezone(selectedLocation) {
	var query = "feed/timezone.php?lat=" + selectedLocation.lat() + "&lng=" + selectedLocation.lng();
	jx.load(query, function(jsonData) {processTimezoneData(jsonData);}, "json");
}

function processTimezoneData(jsonData) {
	var time = jsonData.time.substring(11);
	var sunrise = jsonData.sunrise.substring(11);
	var sunset = jsonData.sunset.substring(11);
	output = "The time at this location is <b>" + time + "</b>, sunrise will be at <b>" + sunrise + "</b> and sunset at <b>" + sunset + "</b>.";
	document.getElementById("timezone_stream").innerHTML = output;
	updateBackground(time, sunrise, sunset);

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

function updateBackground(time, sunrise, sunset) {
	var color = color_morning;
	
	if ((time.substring(0,2) > sunset.substring(0,2)) || (time.substring(0,2) < sunrise.substring(0,2))) {
		color = color_night;
	} else if ((time.substring(0,2) > sunrise.substring(0,2)) && (time.substring(0,2) <= 9)) {
		color = color_morning;
	} else if ((time.substring(0,2) > 9) && (time.substring(0,2) <= 12)) {
		color = color_late_morning;
	} else if ((time.substring(0,2) > 12) && (time.substring(0,2) <= 15)) {
		color = color_midday;
	} else if ((time.substring(0,2) > 15) && (time.substring(0,2) <= sunset.substring(0,2))) {
		color = color_afternoon;
	}

	$("body").animate({backgroundColor: "#" + color}, 1000);

}