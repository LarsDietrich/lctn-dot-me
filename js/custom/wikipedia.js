/**
 * Generate wiki entries based on location
 */
function articles(selectedLocation, range) {
	query = "wikipedia.php?lat=" + selectedLocation.lat() + "&lng="
			+ selectedLocation.lng() + "&radius=" + range * 1000;
	jx.load(query, function(data) {
		process(data);
	}, "json");
}

function process(data) {
	var shtml = '';
	var _articles = data.articles;
	var limit = 15;
	if (_articles.length < 15) {
		limit = _articles.length;
	}
	if (_articles) {
		for (i = 0; i < limit; i++) {
			shtml += "<a target= '_blank' href='" + _articles[i].url + "'>" + _articles[i].title + "</a>&nbsp;";
			shtml += getWikiLocation(_articles[i]);
			shtml += "<br><br>";
		}
		if (shtml.length == 0) {
			shtml = "No results found";
		}
		document.getElementById("wiki_stream").innerHTML = shtml;
	}
}

function getWikiLocation(_articles) {
	var result = _articles.lat + "," + _articles.lng;
	result = "<img src=\"images/find-hilite.png\" onclick=\"locationFromAddress('" + result + "')\"/>";
	return result;
}

