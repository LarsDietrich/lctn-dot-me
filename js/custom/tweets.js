/**
 * Generate tweets based on location
 */
function tweets(selectedLocation, filter, range) {
	jQuery(function() {
		// Execute this code when the page is ready to work
		// Create a Script Tag
		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.src = "http://search.twitter.com/search.json?&q=" + filter
				+ "&geocode=" + selectedLocation.lat() + ","
				+ selectedLocation.lng() + "," + range
				+ "km&callback=processTheseTweets&_=" + new Date().getTime();
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
					+ value.from_user.substring(0, value.from_user.length)
					+ "'>" + value.from_user + "</a>" + "</span>: "
					+ formatText(value.text) + "&nbsp;"
					+ getTweetLocation(value.location) + "</p>";
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
			value = "<a target= '_blank' href='" + value + "'>" + value
					+ "</a>";
		} else if (!(value.match("@") == null)) {
			value = "<a target= '_blank' href='http://twitter.com/"
				
				+ value.substring(1) + "'>" + value + "</a>";
		}
		result = result + " " + value;
	}
	return result;
}

function getTweetLocation(text) {
	var result = text.replace("\u00dcT: ", "");
	var output = "";
	result = result.replace("iPhone: ", "");
	var image = "find.png";
	
	splitResult = result.split(",");
	if (splitResult.length == 2) {
		if (isNumeric(splitResult[0]) && isNumeric(splitResult[1])) {
			image = "find-hilite.png";
		}
	}
	title = "Reposition map to " + result;
	output = "<img title=\"" + title + "\" src=\"images/" + image + "\" onclick=\"locationFromAddress('" + result + "')\"/>";

	return output;
}

function isNumeric(sText) {
	var validChars = "-0123456789.";
	var isNumber = true;
	var char;

	for (i = 0; i < sText.length && isNumber == true; i++) {
		char = sText.charAt(i);
		if (validChars.indexOf(char) == -1) {
			isNumber = false;
		}
	}
	return isNumber;
}
