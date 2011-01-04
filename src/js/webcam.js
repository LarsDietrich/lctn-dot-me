var listOfWebcams = [];
var webcamsFound = false;

/**
 * 
 * Generate a list of webcams in the area
 * 
 * Developer ID: 05a232850706aea3b0b03db356358f24
 * 
 * Request:
 * http://api.webcams.travel/rest?method=wct.webcams.list_nearby&devid=05a232850706aea3b0b03db356358f24&lat=45.983&lng=7.783&radius=5&unit=km&per_page=10&page=1&format=json&callback=processResults()
 * 
 * Response:
 * 
 * {"status":"ok", "webcams":{ "count":13, "page":1, "per_page":10, "webcam":[
 * {"user":"feratel","userid":"31893","user_url":"http:\/\/www.webcams.travel\/user\/31893","webcamid":"1232544583","title":"Gornergrat","view_count":"19120","comment_count":"0","url":"http:\/\/www.webcams.travel\/webcam\/1232544583","icon_url":"http:\/\/images.webcams.travel\/icon\/1232544583.png","thumbnail_url":"http:\/\/images.webcams.travel\/thumbnail\/1232544583.jpg","daylight_icon_url":"http:\/\/archive.webcams.travel\/daylight\/icon\/1232544583.png","daylight_thumbnail_url":"http:\/\/archive.webcams.travel\/daylight\/thumbnail\/1232544583.jpg","latitude":"45.983600","longitude":"7.783050","continent":"EU","country":"CH","city":"Blatten","last_update":1293095945,"rating_avg":"5.00","rating_count":3,"timelapse":{"available":"0"},"timezone":"Europe\/Zurich","timezone_offset":"1"},
 * {"user":"user_189","userid":"3643","user_url":"http:\/\/www.webcams.travel\/user\/3643","webcamid":"1010218306","title":"Zermatt
 * Gornergrat,
 * Matterhorn","view_count":"5145793","comment_count":"11","url":"http:\/\/www.webcams.travel\/webcam\/1010218306","icon_url":"http:\/\/images.webcams.travel\/icon\/1010218306.png","thumbnail_url":"http:\/\/images.webcams.travel\/thumbnail\/1010218306.jpg","daylight_icon_url":"http:\/\/archive.webcams.travel\/daylight\/icon\/1010218306.png","daylight_thumbnail_url":"http:\/\/archive.webcams.travel\/daylight\/thumbnail\/1010218306.jpg","latitude":"45.983231","longitude":"7.782161","continent":"EU","country":"CH","city":"Zermatt","last_update":1293149797,"rating_avg":"2.41","rating_count":212,"timelapse":{"available":"0"},"timezone":"Europe\/Zurich","timezone_offset":"1"},
 * ]}});
 * 
 * 
 */
function getWebcams(selectedLocation, range) {
	jQuery(function() {
		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.src = "http://api.webcams.travel/rest?method=wct.webcams.list_nearby&devid=05a232850706aea3b0b03db356358f24&lat=" + selectedLocation.lat() + "&lng="
				+ selectedLocation.lng() + "&radius=" + range + "&unit=km"
				+ "&format=json&callback=processWebcamResults";
		$("body").append(script);
	});
}

/**
 * Updates the "Webcam" information
 */
function updateWebcamLocationInformation(id) {
	listOfWebcams = new Array();
	if (id) {
		processWebcamResults(id);
	} else {
		if (!(selectedLocation.lat() == 0 || selectedLocation.lng() == 0)) {
			document.getElementById("webcam_stream").innerHTML = "<img class='spinner' src='images/spinner.gif' alt='...' title='Looking for latest webcams'/>";
			getWebcams(selectedLocation, $("#webcam_range").val());
		}
	}
}

/**
 * Process the webcam data returned, convert to array.
 * 
 * @param data -
 *          webcam data returned
 */
function processWebcamResults(data) {
	if (data.webcams) {
		var webcams = data.webcams.webcam;
		if (webcams) {
			var i = 0;
			totalWebcams = data.webcams.count;
			$.each(
							webcams,
							function(index, webcam) {
								var output = "<tr onmouseover='highlightRow(this," + webcam.latitude + "," + webcam.longitude
										+ ", \"images/camera.png\")' onmouseout='normalRow(this)'>";
								// output += "<td><a href=\"" + webcam.url + "\"
								// target=\"_blank\"><img title=\"Click to view on
								// Webcams.Travel\" src=\"" + webcam.thumbnail_url + "\"/></a>";
								output += "<td><img class=\"pointer\" onclick=\"updateWebcamLocationInformation(" + webcam.webcamid + ")\" title=\"Click to view\"  src=\""
										+ webcam.thumbnail_url + "\"/>";
								output += "</td><td>This title of this webcam is <b>";
								output += webcam.title;
								output += "</b>. It is owned by ";
								output += "<a href=\"" + webcam.user_url + "\" target=\"_blank\">" + webcam.user + "</a>";
								output += " and located in " + webcam.city + "<br/>";
								output += "<div class=\"tweet-age inline\">" + getWebcamTimeCreated(webcam.last_update) + "</div>&nbsp;|&nbsp;";
								output += "<div title=\"Reposition map to location\" class=\"tweet-age inline\" style=\"cursor: pointer;\" onclick=\"currentWebcamPage = 1;useAddressToReposition('"
										+ webcam.latitude + "," + webcam.longitude + "')\">Go There!</div>";
								output += "</td></tr>";
								listOfWebcams[i] = output;
								i++;
							});
		}
	} else {
		var output = "<tr><td>";
		output += "<a href=\"http://images.webcams.travel/webcam/" + data
				+ ".jpg\" class=\"lumen\" title=\"test\"><img id=\"webcam-full\" class=\"webcam-full-image\" src=\"http://images.webcams.travel/webcam/" + data
				+ ".jpg\"/></a><br/>";
		output += "<div onclick= \"updateWebcamLocationInformation()\" class=\"tweet-age inline pointer\">Close</div>&nbsp;|&nbsp;";
		output += "<div onclick= \"$('#webcam-full').attr('src', 'http://images.webcams.travel/webcam/" + data
				+ ".jpg')\" class=\"tweet-age inline pointer\">Refresh</div>";
		output += "</td></tr>";
		infoMarker.setMap(null);
		infoMarker == null;
		listOfWebcams[0] = output;
	}
	updateWebcamDisplay();
}

function getWebcamTimeCreated(time) {
	// 1293095945
	var now = new Date();
	var seconds = Math.ceil(((now.getTime() / 1000) - time));
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
	return response;
}

/**
 * Load the webcam display based on whats in webcam array.
 */
function updateWebcamDisplay() {
	var output = "<table>";
	for (i = 0; i < listOfWebcams.length; i++) {
		output += listOfWebcams[i];
	}
	output += "</tr><tr><td colspan='2'>";
	output += "Powered by <a href=\"http://www.webcams.travel/\" title=\"Webcams Worldwide\" target=\"_blank\">Webcams.Travel</a>";
	output += "</td></tr></table>";
	$("#webcam_stream").html(output);
	updateWebcamFooter();
}

/**
 * Updates the footer information at the bottom of the window
 */
function updateWebcamFooter() {
		$("#webcam_footer").html(webcamsFound?"<center>" + listOfWebcams.length + " webcams</center>" : "<center>No webcams</center>");
}
