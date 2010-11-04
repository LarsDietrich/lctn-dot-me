/**
 * Generate wiki entries based on location
 */
function articles(selectedLocation, range) {
	listOfWikis = [];
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
	var wiki = "";
	var j = 0;
	if (_articles.length < 15) {
		limit = _articles.length;
	}
	if (_articles) {
		for (i = 0; i < limit; i++) {
			wiki = "<a target= '_blank' href='" + _articles[i].url + "'>" + _articles[i].title + "</a>&nbsp;";
			wiki += getWikiLocation(_articles[i]);
			wiki += "<br><br>";
			listOfWikis[j] = wiki;
			j++;
		}
		if (listOfWikis.length == 0) {
			listOfWikis[0] = "No results found";
		}
		updateWikiDisplay(1);
	}
}

function getWikiLocation(_articles) {
	var result = _articles.lat + "," + _articles.lng;
	result = "<img class=\"reposition-image\" title=\"Reposition map to article location\"src=\"images/find-hilite.png\" onclick=\"locationFromAddress('" + result + "')\"/>";
	return result;
}

