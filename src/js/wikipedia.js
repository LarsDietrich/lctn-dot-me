//{"geonames":
//	[{"summary":"The Glärnisch is a mountain of the Glarus Alps, Switzerland, consisting of two ridges of either side of the ''Glärnischfirn'' glacier: the ''Ruchen'' at 2901 m to the west, rising above the Klöntalersee, and the ''Bächistock'' at 2914 m to the southwest. The best known peak is the ''Vrenelisgärtli'' at 2904 m.  (...)","distance":"0.1869","title":"Glärnisch","wikipediaUrl":"en.wikipedia.org/wiki/Gl%C3%A4rnisch","elevation":0,"countryCode":"CH","lng":8.99861111111111,"feature":"mountain","lang":"en","lat":46.99861111111111,"population":0},
//	 {"summary":"Oberblegisee is a lake in the Canton of Glarus, Switzerland. It is located at an elevation of 1422 m, above Luchsingen. Its surface area is 0.17 km².  (...)","distance":"2.3589","title":"Oberblegisee","wikipediaUrl":"en.wikipedia.org/wiki/Oberblegisee","elevation":0,"countryCode":"CH","lng":9.013333333333334,"feature":"waterbody","lang":"en","lat":46.98083333333334,"population":0},
//	 {"summary":"Klöntalersee is a natural lake in the Canton of Glarus, Switzerland. Since 1908, it is used as a reservoir for electricity production. The construction of a dam led to a substantial increase of its volume.  (...)","distance":"3.2011","title":"Klöntalersee","wikipediaUrl":"en.wikipedia.org/wiki/Kl%C3%B6ntalersee","elevation":0,"countryCode":"CH","lng":8.980555555555556,"feature":"waterbody","thumbnailImg":"http://www.geonames.org/img/wikipedia/2000/thumb-1904-100.jpg","lang":"en","lat":47.025555555555556,"population":0},
//	 {"summary":"The Canton of Glarus (German: ) is a canton in east central Switzerland. The capital is Glarus. There are 25 municipalities in the canton (July 2006). The population is German speaking and either Protestant or Catholic.  (...)","distance":"5.3851","title":"Canton of Glarus","wikipediaUrl":"en.wikipedia.org/wiki/Canton_of_Glarus","elevation":0,"countryCode":"CH","lng":9.066666666666666,"feature":"adm1st","lang":"en","lat":46.983333333333334,"population":0},
//	 {"summary":"Obersee is a lake on Oberseealp in the Canton of Glarus, Switzerland. Its surface area is 0.24 km².  (...)","distance":"9.7244","title":"Obersee (Glarus)","wikipediaUrl":"en.wikipedia.org/wiki/Obersee_%28Glarus%29","elevation":0,"countryCode":"CH","lng":9.01388888888889,"feature":"waterbody","lang":"en","lat":47.08694444444445,"population":0}
//]}
var listOfWikis = [];
var wikisFound = false;

/**
 * Calls the wikipedia backend php script to retrieve wiki articles for the
 * parameters supplied.
 * 
 * @param selectedLocation -
 *          the location to get wiki articles for
 * @param range -
 *          the range to check for wiki articles
 */
function getArticles(selectedLocation, range) {
	listOfWikis = [];
	query = "feed/wikipedia.php?lat=" + selectedLocation.lat() + "&lng=" + selectedLocation.lng() + "&range=" + range;
	jx.load(query, function(data) {
		processWikiData(data);
	}, "json");
}

/**
 * Processes the wiki data retrieved and converts it to an array.
 * 
 * @param data -
 *          data to process
 */
function processWikiData(data) {
	var shtml = '';
	var articles = data.geonames;
	var status = data.status;
	var output = "";
	var j = 0;
	var point;

	if (articles) {
		$.each(articles, function(index, value) {

			  var cleanedLocation = Math.round(value.lat * 10000) / 10000 + "," + Math.round(value.lng * 10000) / 10000;

				output = "<tr data-latitude=\"" + value.lat + "\" data-longitude=\"" + value.lng	+ "\" data-image=\"/images/wikipedia.png\" data-shadow-image=\"/images/wikipedia-shadow.png\">";
				output += "<td><a target= '_blank' href='http://" + value.wikipediaUrl + "'>" + value.title + "</a>&nbsp;";
				output += value.summary + "<br/>";
				
				output += "<div title=\"Reposition map to " + cleanedLocation + "\" class=\"item-subtext-button inline\" style=\"cursor: pointer;\" onclick=\"useAddressToReposition('" + cleanedLocation + "')\">Center</div>";
				output += "&nbsp;&nbsp;<div title=\"Get directions to " + cleanedLocation + "\" class=\"item-subtext-button inline\" style=\"cursor: pointer;\" onclick=\"getRouteToLocation('" + cleanedLocation + "')\">Go</div>";
				
				output += "</td></tr>";
				listOfWikis[j] = output;
				j++;
		});
	}	
	
	
	if (listOfWikis.length == 0) {
		if (status && (status.message.match("timeout") == "timeout")) {
			listOfWikis[0] = "The lookup service timed out, retry or decrease the search range and retry.";
		} else {
			listOfWikis[0] = "No entries found, try a bigger search area";
		}
		wikisFound = false;
	} else {
		wikisFound = true;
	}
	updateWikiDisplay();
}

/**
 * Triggers the retrieval of the wikipedia information based on
 * 
 * selectedLocation.
 */
function updateWikiLocationInformation() {
	if (!(selectedLocation.lat() == 0 || selectedLocation.lng() == 0)) {
		document.getElementById("wiki_stream").innerHTML = "<img class='spinner' src='images/spinner.gif' alt='...' title='Looking for wikipedia articles'/>";
		getArticles(selectedLocation, document.getElementById("wiki_range").value);
	}
}

/**
 * Loads the wikipedia container with the contents of listOfWikis.
 */
function updateWikiDisplay() {
	var output = "<table>";
	for (i = 0; i < listOfWikis.length; i++) {
		output += listOfWikis[i];
	}
	output += "</table>";
	$("#wiki_stream").html(output);
	$("table tr", "#wiki_stream").hover(function() {
		highlightRow($(this));
	}, function() {
		normalRow($(this));
	});

	updateWikiFooter();
}

/**
 * Updates the footer information at the bottom of the wiki container
 */
function updateWikiFooter() {
	$("#wiki_footer").html(wikisFound?"<center>" + listOfWikis.length + " articles</center>" : "<center>No articles</center>");
}
