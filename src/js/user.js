var userData = "";

/**
 * Generate user detail
 */
function getUserData() {
	var query = "feed/userdata.php?user=" + user;
	jx.load(query, function(data) {
		processUserData(data);
	}, "json");
}

/**
 * Updates the "user" information
 */
function updateUserInformation() {
	$("#user_stream").html("<img class='spinner' src='images/spinner.gif' alt='...' title='Loading...'/>");
	getUserData();
}

/**
 * Process the user data returned.
 */
function processUserData(data) {
	root = "http://" + top.location.host + "/";
	userData = "";
	$.each(data.result, function(index, value) {
		url = root + value.url;
		userData += "<tr><td>";
		userData += "<a href='" + url + "' target='_blank'>" + url + "</a></td><td>";
		userData += getUrlTimeCreated(value.created) + "</td></tr>";
	});
	updateUserDisplay();
}

/**
 * Load the route display based on whats in route array.
 */
function updateUserDisplay() {
	var output = "<table>";
	output += "<tr><td colspan=\"2\"><b>Locations you've shortened:</b></td></tr>";
	output += "<tr><td colspan=\"2\"><hr/></td></tr>";
	output += userData;
	output += "</table>";
	$("#user_stream").html(output);
}

/**
 * Parses the time value of the url to get the "time ago" value
 * @param time
 */
function getUrlTimeCreated(time) {
	// 2011-01-05 15:10:16
	time = time.replace("-", ":").replace(" ", ":").replace("-", ":");
	var timeSplit = time.split(":");
	var date = new Date(timeSplit[0],timeSplit[1] - 1,timeSplit[2],timeSplit[3],timeSplit[4],timeSplit[5]);
	var currentDate = new Date();
	var seconds = Math.ceil((currentDate.getTime() - date.getTime()) / 1000);
	var response = "";
	if (seconds < 60) {
		response = seconds + " second(s) ago";
	} else if (seconds >= 60 && seconds < 3600) {
		response = Math.round(seconds / 60) + " minute(s) ago";
	} else if (seconds >= 3600 && seconds < 86400) {
		response = Math.round(seconds / 3600) + " hour(s) ago";
	} else {
		response = Math.round(seconds / 86400) + " day(s) ago";
	}
	return "<div class='tweet-age inline'>" + response + "</div>";
}
