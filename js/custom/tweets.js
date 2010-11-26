var listOfTweets = [];
var tweetsPerPage = 6;

/**
 * Entry method for generating tweets, calls the twitter service using selected
 * parameters. Once complete, calls a method to process the results.
 * 
 * @param selectedLocation -
 *            the location to check for tweets.
 * @param filter -
 *            the filter to use to filter tweets, empty for all.
 * @param range -
 *            the range of tweets to find
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

/**
 * Called after data retrieves from twitter service. Loads the JSON results of
 * the twitter search into an array.
 * 
 * @param jsonData -
 *            twitter data.
 */
function processTheseTweets(jsonData) {
	var shtml = '';
	var results = jsonData.results;
	var i = 0;
	var tweet = "";
	if (results) {
		$.each(results, function(index, value) {
			tweet = "<tr><td><img src='" +  value.profile_image_url + "'/></td>";
			tweet += "<td><p class='title'><span>"
					+ "<a target= '_blank' href='http://twitter.com/"
					+ value.from_user.substring(0, value.from_user.length)
					+ "'>" + value.from_user + "</a>" + "</span>: "
					+ formatTwitterText(value.text) + "&nbsp;"
					+ getTweetLocation(value.location) + "</p></td><tr>";
			listOfTweets[i] = tweet;
			i++;
		});
		if (listOfTweets.length == 0) {
			listOfTweets[0] = "No tweets found, try a bigger search area or search for something different";
		}
		updateTwitterDisplay(1);
	}
}

/**
 * Formats the twitter text, adding hyperlinks to hashtags, web links and @ tags.
 * 
 * @param text -
 *            text to format.
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
			add = "<a target= '_blank' href='http://twitter.com/"
					+ value.substring(1) + "'>" + value + "</a>";
		} else if (value.substring(0, 1) == "#") {
			add = "<span class='hashtag' onclick=\"updateTwitterLocationInformationFromHashTag('"
					+ value.substring(1) + "')\">" + value + "</span>";
		} else {
			add = value;
		}
		result = result + " " + add;
	}
	return result;
}

/**
 * Takes the location supplied by the tweet and hyperlinks it to enable the
 * repositioning of the map when clicked.
 * 
 * @param text -
 *            the location information
 * @return hyperlinked location text
 */
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
	output = "<img class=\"reposition-image\" title=\"" + title
			+ "\" src=\"images/" + image + "\" onclick=\"locationFromAddress('"
			+ result + "')\"/>";

	return output;
}

/**
 * Determine if text supplied is numeric.
 * 
 * @param text -
 *            the text to check
 * 
 * @return true if number, false if not
 */
function isNumeric(text) {
	var validChars = "-0123456789.";
	var isNumber = true;
	var char;

	for (i = 0; i < text.length && isNumber == true; i++) {
		char = text.charAt(i);
		if (validChars.indexOf(char) == -1) {
			isNumber = false;
		}
	}
	return isNumber;
}

/**
 * Triggers the retrieval of new tweets based on selectedLocation
 */
function updateTwitterLocationInformation() {
	if (isEnabled("twitter")) {
		if (!(selectedLocation.lat() == 0 || selectedLocation.lng() == 0)) {
			document.getElementById("tweet_stream").innerHTML = "<img class='spinner' src='images/spinner.gif' alt='...' title='Looking for tweets'/>";
			getTweets(selectedLocation,
					document.getElementById("filter").value, document
							.getElementById("tweet_range").value);
		}
	} else {
		document.getElementById("tweet_stream").innerHTML="";
	}
}

/**
 * Triggers the retrievel of new tweets based on selectedLocation and clicked
 * hashtag
 */
function updateTwitterLocationInformationFromHashTag(value) {
	document.getElementById("filter").value = value;
	updateTwitterLocationInformation();
}

/**
 * Loads the twitter container with data from the listOfTweets array.
 * 
 * @param page -
 *            the page number to display tweets for
 * 
 */
function updateTwitterDisplay(page) {
	var startItem = (page - 1) * tweetsPerPage;
	var endItem = page * tweetsPerPage;
	var output = "";

	if (endItem > listOfTweets.length) {
		endItem = listOfTweets.length;
	}

	output += "<table>";

	for (i = startItem; i < endItem; i++) {
		output += listOfTweets[i];
	}
	
	output += "</table>";
	
	document.getElementById("tweet_stream").innerHTML = output;
	if (isEnabled("popup")) {
		$("[title]").tooltip( {effect : "slide"});
	}
	updateTwitterPaging(page);
}

/**
 * Updates the paging information at the bottom of the twitter container
 * 
 * @param page -
 *            current page number.
 */
function updateTwitterPaging(page) {
	var totalPages = Math.round(listOfTweets.length / tweetsPerPage);
	if (totalPages < (listOfTweets.length / tweetsPerPage)) {
		totalPages++;
	}
	var next = "&nbsp;";
	var previous = "&nbsp;";

	if ((page + 1) <= totalPages) {
		next = "<img class='footer-icon' src=\"images/arrow-right.png\" onclick=\"updateTwitterDisplay("
				+ (page + 1) + ")\"></img>";
	}
	if ((page - 1) >= 1) {
		previous = "<img class='footer-icon' src=\"images/arrow-left.png\" onclick=\"updateTwitterDisplay("
				+ (page - 1) + ")\"></img>";
	}
	document.getElementById("twitter_footer").innerHTML = "<center>" + previous
			+ "&nbsp&nbsp;" + next + "</center>";
}
