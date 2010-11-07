/**
 * Generate tweets based on location
 */

function getTweets(selectedLocation, filter, range) {
	listOfTweets = [];
	jQuery(function() {
		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.src = "http://search.twitter.com/search.json?lang=en&rpp=100&q="
				+ filter + "&geocode=" + selectedLocation.lat() + ","
				+ selectedLocation.lng() + "," + range
				+ "km&callback=processTheseTweets&_=" + new Date().getTime();
		$("body").append(script);
	});
}

function processTheseTweets(jsonData) {
	var shtml = '';
	var results = jsonData.results;
	var i = 0;
	var tweet = "";
	if (results) {
		$.each(results, function(index, value) {
			tweet = "<p class='title'><span>"
					+ "<a target= '_blank' href='http://twitter.com/"
					+ value.from_user.substring(0, value.from_user.length)
					+ "'>" + value.from_user + "</a>" + "</span>: "
					+ formatText(value.text) + "&nbsp;"
					+ getTweetLocation(value.location) + "</p>";
			listOfTweets[i] = tweet;
			i++;
		});
		if (listOfTweets.length == 0) {
			listOfTweets[0] = "No tweets found, try a bigger search area or search for something different";
		}
		updateTwitterDisplay(1);
	}
}

function formatText(text) {
	var splitString = text.split(" ");
	var result = "";
	var add = "";
	var value;
	
	for (i = 0; i < splitString.length; i++) {
		value = splitString[i];
		if (value.substring(0, 4) == "http") {
			add = "<a target= '_blank' href='" + value + "'>" + value + "</a>";
		} else if (value.substring(0, 1) == "@") {
			add = "<a target= '_blank' href='http://twitter.com/"
					+ value.substring(1) + "'>" + value + "</a>";
		} else if (value.substring(0, 1) == "#") {
			add = "<span class='hashtag' onclick=\"updateTwitterLocationInformationFromHashTag('"
					+ value.substring(1)
					+ "')\">"
					+ value
					+ "</span>";
		} else {
			add = value;
		}
		result = result + " " + add;
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
		if (isNumeric(splitResult[0].replace(" ", ""))
				&& isNumeric(splitResult[1].replace(" ", ""))) {
			image = "find-hilite.png";
		}
	}
	title = "Reposition map to " + result;
	output = "<img class=\"reposition-image\" title=\"" + title + "\" src=\"images/" + image
			+ "\" onclick=\"locationFromAddress('" + result + "')\"/>";

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
