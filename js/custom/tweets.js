/**
 * Generate tweets based on location
 */
function tweets(selectedLocation, filter, range) {
	range = range * 0.621371192237334;
	jQuery(function() {
		// Execute this code when the page is ready to work
		// Create a Script Tag
		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.src = "http://search.twitter.com/search.json?&q=" + filter + "&geocode="
				+ selectedLocation.lat() + "," + selectedLocation.lng()
				+ "," + range + "mi&callback=processTheseTweets&_=" + new Date().getTime();
		// Add the Script to the Body element, which will in turn load the
		// script and run it.
		$("body").append(script);
	});
}

function processTheseTweets(jsonData) {
	var shtml = '';
	var results = jsonData.results;
	if (results) {
		// if there are results (it should be an array), loop through it with a
		// jQuery function
		$.each(results, function(index, value) {
			shtml += "<p class='title'><span class='author'>"
					+ "<a target= '_blank' href='http://twitter.com/"
					+ value.from_user.substring(0, value.from_user.length) + "'>"
					+ value.from_user + "</a>" + "</span>: "
					+ formatText(value.text) + "</p>";
		});
		if (shtml.length == 0) {
			shtml = "No results found";
		}
		$("#tweet_stream").html(shtml);
	} 
}

function formatText(text) {
	var splitString = text.split(" ");
	var result = "";

	for (i = 0; i < splitString.length; i++) {
		value = splitString[i];
		if (!(value.match("http") == null)) {
			value = "<a target= '_blank' href='" + value + "'>" + value + "</a>";
		} else if (!(value.match("@") == null)) {
			value = "<a target= '_blank' href='http://twitter.com/" + value.substring(1) + "'>"
					+ value + "</a>";
		}
		result = result + " " + value;
	}
	return result;
}
