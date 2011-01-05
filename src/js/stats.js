/**
 * Get statistics
 */
function getStatistics() {
	var query = "stats.php";
	jx.load(query, function(data) {
		processStatistics(data);
	}, "json");
}

/**
 * Process the statistics.
 */
function processStatistics(data) {
	$("#statistics").html("People have searched for places " + data.total + " times in the last " + getStatsTimeStart(data.result[0].created));
}

/**
 * Parses the time value of the url to get the "time ago" value
 * 
 * @param time
 */
function getStatsTimeStart(time) {
	// 2011-01-05 15:10:16
	time = time.replace("-", ":").replace(" ", ":").replace("-", ":");
	var timeSplit = time.split(":");
	var date = new Date(timeSplit[0], timeSplit[1] - 1, timeSplit[2], timeSplit[3], timeSplit[4], timeSplit[5]);
	var currentDate = new Date();
	var seconds = Math.ceil((currentDate.getTime() - date.getTime()) / 1000);
	var response = "";
	if (seconds < 60) {
		response = seconds + " second(s)";
	} else if (seconds >= 60 && seconds < 3600) {
		response = Math.round(seconds / 60) + " minute(s)";
	} else if (seconds >= 3600 && seconds < 86400) {
		response = Math.round(seconds / 3600) + " hour(s)";
	} else {
		response = Math.round(seconds / 86400) + " day(s)";
	}
	return response;
}
