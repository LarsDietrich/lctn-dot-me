/*
 * Javascript to attempt to locate a person using built in browser geolocation.
 */

function response() {
	this.location = new google.maps.LatLng(0, 0);
	this.message = "Unavailable";
	this.success = false;
}


function locateMe() {
	var locateResponse = new response();
	try {
		// Try W3C Geolocation (Preferred)
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
			// Try Google Gears Geolocation
		} else if (google.gears) {
			var geo = google.gears.factory.create('beta.geolocation');
			geo.getCurrentPosition(successCallback, errorCallback);
		} else {
			locateResponse.message = "Geolocation not supported";
		}
	} catch (err) {
		locateResponse.message = "Error: " + err.description;
	}
	return locateResponse;
}

function successCallback(position) {
	locateResponse.location = new google.maps.LatLng(position.latitude, position.longitude);
	locateResponse.message = "Success";
	locateResponse.success = true;
}

function errorCallback(error) {
	locateResponse.message = "Error" + error.message;
}
