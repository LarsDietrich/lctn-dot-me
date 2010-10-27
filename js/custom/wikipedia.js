/**
 * Generate wiki entries based on location
 */
function articles(selectedLocation, filter, range) {
	query = "wikipedia.php?lat=" + selectedLocation.lat() + "&lng="
			+ selectedLocation.lng() + "&radius=" + range * 1000;
	jx.load(query, function(data) {
		process(data);
	}, "json");
}

function process(data) {
	var shtml = '';
	var _articles = data.articles;
	if (_articles) {
		for (i = 0; i < _articles.length; i++) {
			shtml += "<a target= '_blank' href='" + _articles[i].url + "'>" + _articles[i].title + "</a>";
			shtml += getLocation(_articles[i]);
			shtml += "<br><br>";
			if (shtml.length == 0) {
				shtml = "No results found";
			}
			document.getElementById("wiki_stream").innerHTML = shtml;
		}
	}
}

function getLocation(_articles) {
	var result = _articles.lat + "," + _articles.lng;
	result = "<img src=\"images/find.png\" onclick=\"locationFromAddress('" + result + "')\"/>";
	return result;
}

