/**
 * 
 * Generate a list of webcams in the area
 * 
 * Developer ID: 05a232850706aea3b0b03db356358f24
 * 
 * http://www.webcams.travel/developers/
 * 
 * Request Sample:
 * http://api.webcams.travel/rest?method=wct.webcams.list_nearby&devid=05a232850706aea3b0b03db356358f24&lat=45.983&lng=7.783&radius=5&unit=km&per_page=10&page=1&format=json&callback=processResults()
 * 
 * Response Sample:
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
// an array to hold the information to display for the webcams (a list of table
// rows with data, formatted correctly).
var listOfWebcams = [];
// boolean to determine if the API query returned any webcams.
var webcamsFound = false;
// holds the JSON object of the latest API lookup for webcams.
var currentWebcams;
// The maximum number of webcams to return. This is an API limitation.
var maxWebcams = 50;

/**
 * Does the API call to get the webcam data with a callback to
 * processWebcamResults.
 * 
 * @param selectedLocation -
 *          the location to check for of type google.maps.LatLng()
 * @param range -
 *          the range to check for, in kilometers
 */
function getWebcams(selectedLocation, range) {
	jQuery(function() {
		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.src = "http://api.webcams.travel/rest?method=wct.webcams.list_nearby&devid=05a232850706aea3b0b03db356358f24&lat=" + selectedLocation.lat() + "&lng="
				+ selectedLocation.lng() + "&radius=" + range + "&unit=km" + "&per_page=" + maxWebcams + "&format=json&callback=processWebcamResults";
		$("body").append(script);
	});
}

/**
 * Updates the "Webcam" information, if id is supplied, will call method to load
 * individual webcam in window.
 */
function updateWebcamLocationInformation() {
	listOfWebcams = new Array();
	if (!(selectedLocation.lat() == 0 || selectedLocation.lng() == 0)) {
		$("#webcam_stream").html("<img class='spinner' src='images/spinner.gif' alt='...' title='Loading webcam information'/>");
		getWebcams(selectedLocation, $("#webcam_range").val());
	}
}

/**
 * Given a JSON object, will populate listOfWebcams with rows to display. If a
 * number is supplied, will use it to retrieve the relevant item from
 * listOfWebcams and display it in "fullscreen" mode.
 * 
 * @param data
 *          -either a JSON object from the API or a number representing the
 *          position of the webcam in listOfWebcams array
 */
function processWebcamResults(data) {
	if (data.webcams) {
		var webcams = data.webcams.webcam;
		currentWebcams = webcams;
		webcamsFound = true;
		if (webcams) {
			totalWebcams = data.webcams.count;
			$.each(webcams, function(index, webcam) {
				var isTimelapse = webcam.timelapse && (webcam.timelapse.available == 1);
					var output = "<tr data-latitude='" + webcam.latitude + "' data-longitude='" + webcam.longitude + "' data-image='/images/webcam.png' data-shadow-image='/images/webcam-shadow.png'>";
					output += "<td>";
					output += "<div id='triggers'><img src='" + webcam.thumbnail_url + "' rel='#mies" + index + "'/></div>";
					output += "<div class='simple_overlay' id='mies" + index + "'>"; 
					
					if (webcam.timelapse && (webcam.timelapse.available == 1)) {
						//video
						output += "<a href='" + webcam.timelapse.format_flv + "' style='display:block;width:445px;height:320px;z-index:9000' id='player'></a>";
					} else {
						//still image
						output += "<img src='http://images.webcams.travel/webcam/" + webcam.webcamid + ".jpg'/><br/>";
					}

					output += "</div>";
					output += "</td>";
					output += "<td>This webcam, named <b>";
					output += webcam.title;
					output += isTimelapse ? "</b>, is a timelapse <b>video</b>" : "</b>, is a fixed <b>image</b>";
					output += " located in " + webcam.city + ".";
					output += " It is owned and maintained by ";
					output += "<a href='" + webcam.user_url + "' target='_blank'>" + webcam.user + "</a><br/>";
					output += "<div class='item-subtext inline'>" + getWebcamTimeCreated(webcam.last_update) + "</div>&nbsp;|&nbsp;";
					output += "<div title='Reposition map to location' class='item-subtext inline' style='cursor: pointer;' onclick='useAddressToReposition('"
							+ webcam.latitude + "," + webcam.longitude + "')'>Center There!</div>" + "</div>&nbsp;|&nbsp;";
					output += "<div title='Get directions to location' class='item-subtext inline' style='cursor: pointer;' onclick='getRouteToLocation('"
							+ webcam.latitude + "," + webcam.longitude + "')'>Go There!</div>";
					output += "</td></tr>";
					listOfWebcams[index] = output;
				});
			if (listOfWebcams.length == 0) {
				listOfWebcams[0] = "<tr><td>No webcams found, try a bigger search area or a different location.</td></tr>";
			}
		}
	}
	updateWebcamDisplay();
}

/**
 * Return the formated time the webcam was last refreshed.
 * 
 * @param time
 *          (sample: 1293095945)
 */
function getWebcamTimeCreated(time) {
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
 * Load the webcam display based on whats in listOfWebcams array.
 */
function updateWebcamDisplay() {
	var output = "<table>";
	for (i = 0; i < listOfWebcams.length; i++) {
		output += listOfWebcams[i];
	}
	output += "</tr><tr><td colspan='2'>";
	output += "Powered by <a href='http://www.webcams.travel/' title='Webcams Worldwide' target='_blank'>Webcams.Travel</a>";
	output += "</td></tr></table>";
	$("#webcam_stream").html(output);
	$("img[rel]").overlay();
	$("table tr", "#webcam_stream").hover(function() {
		highlightRow($(this));
	}, function() {
		normalRow($(this));
	});

	updateWebcamFooter();
	if ($("#player")) {
		flowplayer("player", "js/flowplayer-3.2.5.swf");
	}
}

/**
 * Updates the footer information at the bottom of the webcams window
 */
function updateWebcamFooter() {
	$("#webcam_footer").html(webcamsFound ? "<center>" + listOfWebcams.length + " webcams</center>" : "<center>No webcams</center>");
}
