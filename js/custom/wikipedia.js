var listOfWikis = [];
var wikisPerPage = 10;

/**
 * Calls the wikipedia backend php script to retrieve wiki articles for the
 * parameters supplied.
 * 
 * @param selectedLocation -
 *            the location to get wiki articles for
 * @param range -
 *            the range to check for wiki articles
 */
function getArticles(selectedLocation, range) {
	listOfWikis = [];
	query = "wikipedia.php?lat=" + selectedLocation.lat() + "&lng="
			+ selectedLocation.lng() + "&radius=" + range * 1000;
	jx.load(query, function(data) {
		processWikiData(data);
	}, "json");
}

/**
 * Processes the wiki data retrieved and converts it to an array.
 * 
 * @param data -
 *            data to process
 */
function processWikiData(data) {
	var shtml = '';
	var _articles = data.articles;
	var limit = 15;
	var wiki = "";
	var j = 0;
	if (_articles.length < 15) {
		limit = _articles.length;
	}
	if (_articles) {
		for (i = 0; i < limit; i++) {
			wiki = "<a target= '_blank' href='" + _articles[i].url + "'>"
					+ _articles[i].title + "</a>&nbsp;";
			wiki += getWikiLocation(_articles[i]);
			wiki += "<br><br>";
			listOfWikis[j] = wiki;
			j++;
		}
		if (listOfWikis.length == 0) {
			listOfWikis[0] = "No entries found, try a bigger search area";
		}
		updateWikiDisplay(1);
	}
}

/**
 * Converts the location information to a clickable link image. Clicking the
 * image will reposition the map.
 * 
 * @param _article -
 *            article with location information
 * @return - hyperlinked image
 */
function getWikiLocation(_article) {
	var result = _article.lat + "," + _article.lng;
	result = "<img class=\"reposition-image\" title=\"Reposition map to article location\"src=\"images/find-hilite.png\" onclick=\"locationFromAddress('"
			+ result + "')\"/>";
	return result;
}

/**
 * Triggers the retrieval of the wikipedia information based on
 * selectedLocation.
 */
function updateWikiLocationInformation() {
	if (isEnabled("wiki")) {
		if (!(selectedLocation.lat() == 0 || selectedLocation.lng() == 0)) {
			document.getElementById("wiki_stream").innerHTML = "<img class='spinner' src='images/spinner.gif' alt='...'/>";
			getArticles(selectedLocation,
					document.getElementById("wiki_range").value);
		}
	} else {
		document.getElementById("wiki_stream").innerHTML="";
	}
}

/**
 * Loads the wikipedia container with the contents of listOfWikis.
 * 
 * @param page -
 *            page to display data for.
 */
function updateWikiDisplay(page) {
	var startItem = (page - 1) * wikisPerPage;
	var endItem = page * wikisPerPage;
	var output = "";
	if (endItem > listOfWikis.length) {
		endItem = listOfWikis.length;
	}
	for (i = startItem; i < endItem; i++) {
		output += listOfWikis[i];
	}
	document.getElementById("wiki_stream").innerHTML = output;
	updateWikiPaging(page);
}

/**
 * Updates the paging information at the bottom of the wiki container
 * 
 * @param page -
 *            page to create paging for.
 */
function updateWikiPaging(page) {
	var totalPages = Math.round(listOfWikis.length / wikisPerPage);
	if (totalPages < (listOfWikis.length / wikisPerPage)) {
		totalPages++;
	}
	var next = "&nbsp;";
	var previous = "&nbsp;";
	if ((page + 1) <= totalPages) {
		next = "<img class='footer-icon' src=\"images/arrow-right.png\" onclick=\"updateWikiDisplay("
				+ (page + 1) + ")\"></img>";
	}
	if ((page - 1) >= 1) {
		previous = "<img class='footer-icon' src=\"images/arrow-left.png\" onclick=\"updateWikiDisplay("
				+ (page - 1) + ")\"></img>";
	}

	document.getElementById("wiki_footer").innerHTML = "<center>" + previous
			+ "&nbsp&nbsp;" + next + "</center>";
}
