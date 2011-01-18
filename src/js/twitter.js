// holds all the tweet entries found
var listOfTweets = [];
var tweetsFound = false;

/**
 * Entry method for generating tweets, calls the twitter service using selected
 * parameters. Once complete, calls a method to process the results.
 * 
 * @param selectedLocation -
 *          the location to check for tweets.
 * @param filter -
 *          the filter to use to filter tweets, empty for all.
 * @param range -
 *          the range of tweets to find, in km
 */
function getTweets(selectedLocation, filter, range) {
	listOfTweets = [];
	jQuery(function() {
		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.src = "http://search.twitter.com/search.json?rpp=200&q=" + filter + "&geocode=" + selectedLocation.lat() + "," + selectedLocation.lng() + ","
				+ range + "km&callback=processTheseTweets&_=" + new Date().getTime();
		$("body").append(script);
	});
}

/**
 * Called after data retrieves from twitter service. Loads the JSON results of
 * the twitter search into an array.
 * 
 * @param jsonData -
 *          twitter data.
 */
function processTheseTweets(jsonData) {
	var shtml = '';
	var results = jsonData.results;
	var warning = jsonData.warning;
	var i = 0;
	var output = "";

	if (results) {
		$.each(results, function(index, value) {
			var location = cleanTweetLocation(value.location);
			var latitude = 999;
			var longitude = 999;
			if (isNumeric(location)) {
				latitude = location.split(",")[0];
				longitude = location.split(",")[1];
			}

			var cleanedLocation = cleanTweetLocation(value.location);

			output = "<tr data-latitude=\"" + latitude + "\" data-longitude=\"" + longitude	+ "\" data-image=\"/images/twitter-icon.png\" data-shadow-image=\"/images/twitter-icon-shadow.png\">";
			output += "<td><img class='twitter-pic' src='" + value.profile_image_url + "'/></td>";
			output += "<td><span><a target= '_blank' href='http://twitter.com/" + value.from_user.substring(0, value.from_user.length) + "'>" + value.from_user
					+ "</a>" + ": " + formatTwitterText(value.text) + "</span><br/>" + getTwitterTimeCreated(value.created_at) + "&nbsp;|&nbsp;";
			output += "<div title=\"Reposition map to " + cleanedLocation + "\" class=\"item-subtext inline\" style=\"cursor: pointer;\" onclick=\"useAddressToReposition('" + cleanedLocation + "')\">Center There!</div>" + "&nbsp;|&nbsp;";
			output += "<div title=\"Get directions to " + cleanedLocation + "location\" class=\"item-subtext inline\" style=\"cursor: pointer;\" onclick=\"getRouteToLocation('" + cleanedLocation + "')\">Go There!</div>";
			output += "</td><tr>";
			listOfTweets[i] = output;
			i++;
		});

		if (listOfTweets.length == 0) {
			listOfTweets[0] = "No tweets found, reasons for this include:<ul><li>Search area being too small, try a bigger search area.</li><li>Search phrase not found, try search for something else.</li><li>The Twitter Search Service may be experiencing problems, try again later.</li></ul>";
		} else {
			tweetsFound = true;
		}

		updateTwitterDisplay();
	}
}

/**
 * Parses the time value of the tweet to get the "time ago" value
 * 
 * @param time
 */
function getTwitterTimeCreated(time) {
	// Fri, 26 Nov 2010 13:32:59 +0000
	var tweetDate = new Date(eval('"' + time + '"'));
	var currentDate = new Date();
	var seconds = Math.ceil((currentDate.getTime() - tweetDate.getTime()) / 1000);
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
	return "<div class='item-subtext inline'>" + response + "</div>";
}

/**
 * Formats the twitter text, adding hyperlinks to hashtags, web links and @ tags.
 * 
 * @param text -
 *          text to format.
 * 
 * @return a the supplied text, formatted with hiperlinks for found elements
 */
function formatTwitterText(text) {
	var splitString = text.split(" ");
	var result = "";
	var add = "";
	var value;

	for (i = 0; i < splitString.length; i++) {
		value = splitString[i];
		if (value.substring(0, 4) == "http") {
			add = "<a target= '_blank' href='" + value + "'>" + value + "</a>";
		} else if (value.substring(0, 1) == "@") {
			add = "<a target= '_blank' href='http://twitter.com/" + value.substring(1) + "'>" + value + "</a>";
		} else if (value.substring(0, 1) == "#") {
			add = "<span class='hashtag' onclick=\"updateTwitterLocationInformationFromHashTag('" + value.substring(1) + "')\">" + value + "</span>";
		} else {
			add = value;
		}
		result = result + " " + add;
	}
	return result;
}

/**
 * Cleans the tweet location, strips out extra characters.
 * 
 * @param text -
 *          the tweet location text.
 */
function cleanTweetLocation(text) {
	var result = text.replace("\u00dcT: ", "");
	result = result.replace("iPhone: ", "");
	return result;
}

/**
 * Determine if text supplied is numeric.
 * 
 * @param text -
 *          the text to check
 * 
 * @return true if number, false if not
 */
function isNumeric(text) {
	var validChars = "-0123456789., ";
	var isNumber = true;
	var character;

	for (i = 0; i < text.length && isNumber == true; i++) {
		character = text.charAt(i);
		if (validChars.indexOf(character) == -1) {
			isNumber = false;
		}
	}
	return isNumber;
}

/**
 * Triggers the retrieval of new tweets based on selectedLocation
 */
function updateTwitterLocationInformation() {
	if (!(selectedLocation.lat() == 0 || selectedLocation.lng() == 0)) {
		$("#tweet_stream").html("<img class='spinner' src='images/spinner.gif' alt='...' title='Looking for tweets'/>");
		getTweets(selectedLocation, $("#filter").val(), $("#tweet_range").val());
	}
}

/**
 * Triggers the retrievel of new tweets based on selectedLocation and clicked
 * hashtag
 */
function updateTwitterLocationInformationFromHashTag(value) {
	$("#filter").val(value);
	updateTwitterLocationInformation();
}

/**
 * Loads the twitter container with data from the listOfTweets array.
 */
function updateTwitterDisplay() {
	var output = "<table>";
	for (i = 0; i < listOfTweets.length; i++) {
		output += listOfTweets[i];
	}
	output += "</table>";
	output += "<div style='text-align=right'><img src=\"images/powered-by-twitter-sig.gif\"/></>";
	$("#tweet_stream").html(output);
	$("table tr", "#tweet_stream").hover(function() {
		highlightRow($(this));
	}, function() {
		normalRow($(this));
	});
	updateTwitterFooter();
}

/**
 * Updates the footer information at the bottom of the twitter container
 */
function updateTwitterFooter() {
	$("#twitter_footer").html(tweetsFound ? "<center>" + listOfTweets.length + " tweets</center>" : "<center>No tweets</center>");
}
