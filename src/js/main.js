var user;

// reference to the main map in the map container.
var map;
// reference to the panorama of the streetview.
var panorama;

// stores current location (of type google.maps.LatLng).
var selectedLocation;

// reference to the position marker on the map (of type google.maps.Marker).
var positionMarker;

// reference to the additional marker on the page, used to show twtter/wiki
// locations.
var infoMarker;

// reference to the geocoder for coding / decoding addresses.
var geocoder = new google.maps.Geocoder();

// information on the streetview/map, this information is saved and reloaded
// with the generated short url.
var heading = 0;
var pitch = 0;
var zoom = 12;
var latitude;
var longitude;
var maptype = "roadmap";

// cache to store a history of previously selected locations.
var locationCache = new Array();
// position in locationCache the user is currently at.
var currentSearchPosition = 0;

/**
 * Loads the parameters passed through on the URL into variables. Used to
 * preload a location.
 */
function loadUrlParameters() {

	$.extend( {
		getUrlVars : function() {
			var vars = {};
			var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
				vars[key] = value;
			});
			return vars;
		}
	});

	latitude = parseFloat($.getUrlVars()['lat']);
	if (!latitude) {
		latitude = 999;
	}

	longitude = parseFloat($.getUrlVars()['lng']);
	if (!longitude) {
		longitude = 999;
	}

	heading = parseInt($.getUrlVars()['heading']);
	if (!heading) {
		heading = 0;
	}

	zoom = parseInt($.getUrlVars()['zoom']);
	if (!zoom) {
		zoom = 12;
	}

	pitch = parseInt($.getUrlVars()['pitch']);
	if (!pitch) {
		pitch = 0;
	}

	maptype = $.getUrlVars()['maptype'];
	if (!maptype) {
		maptype = "roadmap";
	}

}

/**
 * This is the entry point for the page. Loads the necessary data, parses the
 * URL for location information and shows loads the page containers.
 */
function load() {

	setConfigOptions();
	
  if (!hasCookieSupport()) {
  	alert("Cookies are used extensively by this website to function, please enable cookie support and reload the website.");
  	return;
  }

	showContainers();

	loadUrlParameters();

	// TODO: Remove when live
	beta();

	updateUrlWindow("");

	if ($.cookie("lastLocation")) {
		loadLastLocation();
	} else {
		selectedLocation = new google.maps.LatLng(latitude, longitude);
		loadMap();
		loadStreetView();
		reloadContainers();
	}
	
	// setup the popup overlay for later use
	$(function() {
		$("a[rel]").overlay( {
			mask : '#C7D9D4',
			effect : 'apple',
			onBeforeLoad : function() {
				// grab wrapper element inside content
			var wrap = this.getOverlay().find(".contentWrap");
			// load the page specified in the trigger
			wrap.load(this.getTrigger().attr("href"));
		}
		});
	});

	$(document).ready(function() {
		$("div.panel").each(function() {
			var control = $(this);
			$(control).Draggable( {
				handle : 'span',
				zIndex : '1000',
				opacity : 0.8,
				autoSize : true,
				onChange : function() {
					$.cookie($(control).attr("id") + "_top", $(control).css("top"), {
						expires : 365
					});
					$.cookie($(control).attr("id") + "_left", $(control).css("left"), {
						expires : 365
					});
				},
				onStop : function() {
					$(control).css("z-index", "9");
				},
				onStart : function() {
				},
				snapDistance : 5,
				grid : 5
			})

			$(control).css("top", ($.cookie($(control).attr("id") + "_top") ? $.cookie($(control).attr("id") + "_top") : 40));
			$(control).css("left", ($.cookie($(control).attr("id") + "_left")) ? $.cookie($(control).attr("id") + "_left") : 20);

			setMessage("Windows can be dragged by clicking on the title and dragging with the mouse");
			});
	});
}

/**
 * Tries to get the users current location using built in geolocation
 * functionality. Reloads the containers after attempt.
 */
function findMe() {
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
			selectedLocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
			setMessage("Repositioning map to best guess of where you are (accuracy not guaranteed)", "info");
			reloadContainers();
		}, function(error) {
			setMessage("Tried to get your location, but there was a problem, sorry", "error");
			selectedLocation = new google.maps.LatLng(0, 0);
			reloadContainers();
		});
	} else if (google.gears) {
		var geo = google.gears.factory.create('beta.geolocation');
		geo.getCurrentPosition(function(position) {
			selectedLocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
			setMessage("Repositioning map to best guess of where you are (accuracy not guaranteed)", "info");
			reloadContainers();
		}, function(error) {
			selectedLocation = new google.maps.LatLng(0, 0);
			setMessage("Tried to get your location, but there was a problem, sorry", "error");
			reloadContainers();
		});
	}
}

/**
 * Loads the Map in it's container based on the current value of
 * selectedLocation. Will also set the zoom and maptype if available and
 * position a marker at the point of selectedLocation. Also adds event listeners
 * to the map to handle clicks and movement.
 */
function loadMap() {

	var myMapOptions = {
		center : selectedLocation,
		streetViewControl : false
	}

	map = new google.maps.Map(document.getElementById("map_canvas"), myMapOptions);

	map.setZoom(zoom);
	map.setMapTypeId(maptype);
	
	var image = "images/pin_map.png";

	positionMarker = new google.maps.Marker( {
		position : selectedLocation,
		map : map,
		icon : image
	});

	google.maps.event.addListener(map, 'click', function(event) {
		selectedLocation = event.latLng;
		reloadContainers();
	});

	google.maps.event.addListener(map, 'zoom_changed', function() {
		zoom = map.getZoom();
	});

	google.maps.event.addListener(map, 'maptypeid_changed', function() {
		maptype = map.getMapTypeId();
	});

}

/**
 * Loads the StreetView container and sets it's listeners.
 */
function loadStreetView() {
	var panoOptions = {
		addressControl : false,
		navigationControlOptions : {
			style : google.maps.NavigationControlStyle.SMALL
		},
		enableCloseButton : false,
		linksControl : true
	};

	panorama = new google.maps.StreetViewPanorama(document.getElementById("streetview_canvas"), panoOptions);

	google.maps.event.addListener(panorama, 'position_changed', function() {
		selectedLocation = panorama.getPosition();
		reloadMarkers();
	});

	google.maps.event.addListener(panorama, 'pov_changed', function() {
		heading = panorama.getPov().heading;
		pitch = panorama.getPov().pitch;
	});

}
/**
 * Clears markers from map and reloads the positionMarker based on
 * selectedLocation.
 */
function reloadMarkers() {
	clearMarkers();
	positionMarker.setPosition(selectedLocation);
	positionMarker.setMap(map);
	map.setCenter(selectedLocation);
}

/**
 * Reloads container information based on selectedLocation
 */
function reloadContainers() {
	if (!map) {
		loadMap();
	}

	if (!panorama) {
		loadStreetView();
	}

	reloadMarkers();
	loadAllContainers();
	reverseCodeLatLng();
	map.setCenter(selectedLocation);
	$("#url").value = "";
	setMessage("", "");
	updateStats();
}

/**
 * Attempts to load all enabled containers.
 */
function loadAllContainers() {
	if (isEnabled("streetview")) {
		loadContainer("streetview");
	}

	if (isEnabled("wiki")) {
		loadContainer("wiki");
	}

	if (isEnabled("twitter")) {
		loadContainer("twitter");
	}

	if (isEnabled("general")) {
		loadContainer("general");
	}

	if (isEnabled("webcam")) {
		loadContainer("webcam");
	}

	if (isEnabled("places")) {
		loadContainer("places");
	}
}

/**
 * Triggers an update/load of a container and its data.
 * 
 * @param name -
 *          the container name to update/load
 */
function loadContainer(name) {

	if (selectedLocation) {
		if (name == "streetview") {
			var streetViewService = new google.maps.StreetViewService();
			streetViewService.getPanoramaByLocation(selectedLocation, 50, processStreetViewData);
		}

		if (name == "wiki") {
			updateWikiLocationInformation();
		}

		if (name == "general") {
			updateWeatherLocationInformation();
			updateTimezoneLocationInformation();
			updateGeneralLocationInformation($("#address").val());
		}

		if (name == "twitter") {
			updateTwitterLocationInformation();
		}

		if (name == "places") {
			updatePlacesLocationInformation();
		}

		if (name == "webcam") {
			currentWebcamPage = 1;
			updateWebcamLocationInformation();
		}
	}
}

/**
 * Displays containers on the page if enabled, hides if disabled.
 */
function showContainers() {

	$("#find_container").css("display", "inline");
	$("#share_container").css("display", "inline");
	$("#map_container").css("display", "inline");

	if (isEnabled("streetview")) {
		$("#streetview_container").css("display", "inline");
	} else {
		$("#streetview_container").css("display", "none");
	}

	if (isEnabled("places")) {
		$("#places_container").css("display", "inline");
	} else {
		$("#places_container").css("display", "none");
	}

	if (isEnabled("wiki")) {
		$("#wiki_container").css("display", "inline");
	} else {
		$("#wiki_container").css("display", "none");
	}

	if (isEnabled("twitter")) {
		$("#twitter_container").css("display", "inline");
	} else {
		$("#twitter_container").css("display", "none");
	}

	if (isEnabled("general")) {
		$("#general_container").css("display", "inline");
	} else {
		$("#general_container").css("display", "none");
	}

	if (isEnabled("webcam")) {
		$("#webcam_container").css("display", "inline");
	} else {
		$("#webcam_container").css("display", "none");
	}

}

/**
 * Adds an element to the location search cache (locationCache)
 * 
 * @param location -
 *          a string value of the location (eg. Johannesburg)
 */
function addToCache(location) {
	locationCache[locationCache.length] = location;
	currentSearchPosition = locationCache.length == 0 ? 0 : locationCache.length - 1;
}

/**
 * Triggers a reload of the next item in the locationCache based on the
 * currentSearchPosition. Traverses the cache in a forward direction.
 */
function nextSearch() {
	if ((currentSearchPosition + 1) < locationCache.length) {
		currentSearchPosition++;
		$("#address").val(locationCache[currentSearchPosition]);
		locateAndRefresh(false);
	}
}

/**
 * Triggers a reload of the previous item in the locationCache based on the
 * currentSearchPosition. Traverses the cache in a backward direction.
 */
function previousSearch() {
	if (!(currentSearchPosition == 0)) {
		currentSearchPosition--;
		$("#address").val(locationCache[currentSearchPosition]);
		locateAndRefresh(false);
	}
}

/**
 * Clears all markers from the map.
 */
function clearMarkers() {
	if (positionMarker) {
		positionMarker.setMap(null);
	}
	if (infoMarker) {
		infoMarker.setMap(null);
	}
}

/**
 * Tracks click statistics by calling stats.php
 */
function updateStats() {
	jx.load("stats.php?do=stat&lat=" + selectedLocation.lat() + "&lng=" + selectedLocation.lng(), function(data) {
	});
}

/**
 * Try find street view data, load appropriate panorama panel using
 * selectedLocation. If the selectedLocation does not have a streetview
 * associated with it, will attempt to find a streetview within a specified
 * distance and reposition the map to that point.
 */
function processStreetViewData(data, status) {
	if (status == google.maps.StreetViewStatus.OK) {
		var markerPanoID = data.location.pano;
		panorama.setPano(markerPanoID);
		panorama.setPov( {
			heading : heading,
			pitch : pitch,
			zoom : 1
		});
		// positionMarker.setMap(null);
		// selectedLocation = data.location.latLng;
		// positionMarker.setPosition(selectedLocation);
		// positionMarker.setMap(map);
		panorama.setVisible(true);
	} else {
		setMessage("Streetview not available at this location.", "notice");
		panorama.setVisible(false);
	}
}

/**
 * Sets the message are in the upper right area
 * 
 * @param message -
 *          message to send, send blank to clear.
 */
function setMessage(message) {
	if (message == "") {
		document.getElementById("message").innerHTML = "";
	} else {
		document.getElementById('message').innerHTML = message;
	}
}

/**
 * Geocode the current "address" in the address field, set the selectedLocation
 * to it and reposition the map and reload all data.
 * 
 * @param addToCache -
 *          whether this position should be added to the cache or not.
 */
function locateAndRefresh(putInCache) {
	var address = document.getElementById("address").value;
	setMessage("");
	geocoder.geocode( {
		'address' : address
	}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			selectedLocation = results[0].geometry.location;
			reloadContainers();
			if (putInCache) {
				addToCache($("#address").val());
			}
		} else {
			setMessage("Not able to locate that place, please try something else.", "info");
		}
	});
}

/**
 * Use the supplied address to locateAndRefresh()
 */
function useAddressToReposition(address) {
	$("#address").val(address);
	locateAndRefresh(true);
}

/**
 * Helper method to reposition based on coordinates where coords are swapped around (long then lat).
 */
function useAddressToRepositionLngLat(address) {
	addr = address.split(",");
	useAddressToReposition(addr[1] + "," + addr[0]);
}

/**
 * Geocode using the currenct selectedLocation value. Used to detemine the
 * physical address of the location. Also triggers an update of the "general"
 * information container (timezone, weather, lat/lng).
 */
function reverseCodeLatLng() {
	geocoder.geocode( {
		'latLng' : selectedLocation
	}, function(results, status) {
		document.getElementById("timezone_stream").innerHTML = "";
		output = "";
		var address = "";
		if (status == google.maps.GeocoderStatus.OK) {
			if (results.length > 0) {
				address = results[0].formatted_address;
				$("#address").val(address);
				if (isEnabled("general")) {
					updateTimezoneLocationInformation();
				}
			} else {
				setMessage("No addresses were found at this location.", "info");
			}
		} else {
			setMessage("Unable to determine address from current location", "error");
		}

		if (isEnabled("general")) {
			updateGeneralLocationInformation($("#address").val());
		}
		$.cookie("lastLocation", $("#address").val(), { expires : 365 });
	});
}

/**
 * Builds the long URL to be shortened and saved to the database. Calls
 * shrink.php to do the shortening and saving. Updates the "short Url" box.
 */
function shortenUrl() {
	root = "http://" + top.location.host + "/";
	longurl = root + "?lat=" + selectedLocation.lat() + "&lng=" + selectedLocation.lng() + "&heading=" + heading + "&pitch=" + pitch + "&zoom=" + zoom
			+ "&maptype=" + maptype;
	shorturl = "";
	jx.load("shrink.php?shorturl=" + shorturl + "&url=" + escape(longurl), function(data) {
		document.getElementById("url").value = root + data;
		updateUrlWindow(root + data);
	});
}

/**
 * Updates the link url values of the social icons to the supplied link so that
 * clicking on them will trigger the relevant social add function correctly.
 * 
 * @param link -
 *          the short url to pass to the social zone.
 */
function updateUrlWindow(link) {
	var output = "";

	output += "<a href=\"http://twitter.com/home/?status=";
	output += link + "\"";
	output += " target=\"_blank\"><img class='social-button' src=\"images/twitter.png\" title=\"Tweet this link to the world.\" alt=\"Twitter\"></img></a>";

	output += "<a href=\"http://www.facebook.com/sharer.php?u=";
	output += link + "\"";
	output += " target=\"_blank\"><img class='social-button' src=\"images/facebook.png\" title=\"Share the link with your Facebook friends.\" alt=\"Facebook\"></img></a>";

	output += "<a href=\"http://del.icio.us/post?url=";
	output += link + "\"";
	output += " target=\"_blank\"><img class='social-button' src=\"images/delicious.png\" title=\"\Add the link to your Del.icio.us account.\" alt=\"Del.icio.us\"></img></a>";

	output += "<a href=\"mailto:?subject=";
	output += link + "\"";
	output += "><img class='social-button' src=\"images/email.png\" title=\"Email the link to a friend.\" alt=\"Send by Email\"></img></a>";

	document.getElementById("url-window").innerHTML = output;
}

/**
 * Updates the location information in the General container.
 * 
 * @param address -
 *          string value of the current address.
 */
function updateGeneralLocationInformation(address) {
	var output = "You are positioned at <b>" + Math.round(selectedLocation.lng() * 10000) / 10000 + "</b> longitude and <b>"
			+ Math.round(selectedLocation.lat() * 10000) / 10000 + "</b> latitude";
	if (!(address == "")) {
		output += ", which is also known as <b>" + address + "</b>";
	}
	output += ".";
	document.getElementById("location_stream").innerHTML = output;
}

/**
 * Displays the BETA page
 */
function beta() {
	var thediv = document.getElementById('displaybox');
	if (thediv.style.display == "none") {
		thediv.style.display = "";
		thediv.innerHTML = "<span class='displaybox-large'/>BETA</span><br/><span class='displaybox-normal'>This site is still under development, feel free to use it but expect some issues. I take no responsibility for the stability and accuracy of data being displayed.<br/><br/>Please report any issues using the Contact link at the top right of the page.<br/><br/>Thank you for trying out the site.</span><br/><br/><span class='displaybox-normal'/>(click anywhere to close)</span>";
	} else {
		thediv.style.display = "none";
		thediv.innerHTML = '';
	}
	return false;
}

/**
 * Tries to load an "option" from the sites cookie. If the option is found,
 * returns the value (true/false). If it is NOT found, sets it to false
 * 
 * @param option -
 *          option to check for
 */
function isEnabled(option) {
	var result = false;
	var cookie = $.cookie("option_" + option);

	if (cookie == null) {
		$.cookie("option_" + option, "false");
	}

	if ($.cookie("option_" + option) == "true") {
		result = true;
	}
	return result;
}

///**
// * Toggles the map size between large and normal
// */
//function toggleMapSize() {
//	var max_width = "955px";
//	var normal_width = "470px";
//	var max_height = "600px";
//	var normal_height = "465px";
//
//	if ($("#map_container").css("width") == max_width) {
//		$("#map_container").css("width", normal_width);
//		$("#map_canvas").css("width", normal_width);
//		$("#map_container").css("height", normal_height);
//		$("#map_canvas").css("height", normal_height);
//		google.maps.event.trigger(map, "resize");
//	} else {
//		$("#map_container").css("width", max_width);
//		$("#map_canvas").css("width", max_width);
//		$("#map_container").css("height", max_height);
//		$("#map_canvas").css("height", max_height);
//		google.maps.event.trigger(map, "resize");
//	}
//}
//
///**
// * Toggles the streetview size between large and normal
// */
//function toggleStreetViewSize() {
//	var max_width = "955px";
//	var normal_width = "470px";
//	var max_height = "600px";
//	var normal_height = "465px";
//
//	if ($("#streetview_container").css("width") == max_width) {
//		$("#streetview_container").css("width", normal_width);
//		$("#streetview").css("width", normal_width);
//		$("#streetview_container").css("height", normal_height);
//		$("#streetview").css("height", normal_height);
//		google.maps.event.trigger(map, "resize");
//	} else {
//		$("#streetview_container").css("width", max_width);
//		$("#streetview").css("width", max_width);
//		$("#streetview_container").css("height", max_height);
//		$("#streetview").css("height", max_height);
//		google.maps.event.trigger(map, "resize");
//	}
//}

/**
 * "Hilites" a row in the active container and shows the point on the map. Used
 * by wiki and twitter containers
 * 
 * @param row -
 *          the element "row" to hilite
 * @param location -
 *          the location to put on the map (google.map.LatLng)
 * @param icon -
 *          the icon image to use
 */
function highlightRow(row, lat, lng, icon) {
	$(row).css("background-color", "#AEC2AE");
	var location = new google.maps.LatLng(lat, lng);
	infoMarker = new google.maps.Marker( {
		position : location
	});
	infoMarker.setMap(map);
	infoMarker.setIcon(icon);
	infoMarker.setZIndex(999);
}

/**
 * Helper to call hilight row with longitude and latitude supplied in reverse order.
 */
function highlightLngLatRow(row, lng, lat, icon) {
	highlightRow(row, lat, lng, icon);
}
/**
 * Return the element represented by the row to normal (from hilighted) and
 * clear the map marker.
 * 
 * @param row
 * @return
 */
function normalRow(row) {
	$(row).css("background-color", "transparent");
	infoMarker.setMap(null);
	infoMarker == null;
}

/**
 * "Hides" an element by setting it's display to none.
 * 
 * @param name -
 *          id of element to hide
 */
function hideElement(name) {
	$("#" + name).css("display", "none");
}

/**
 * "Shows" an element by setting it's display to inline.
 * 
 * @param name -
 *          id of element to hide
 */
function showElement(name) {
	$("#" + name).css("display", "inline");
}

/**
 * Check for cookie support by attempting to create a cookie and then retrieve
 * it
 * 
 * @return true/false
 */
function hasCookieSupport() {
	$.cookie("iexist", "yes");
	if (!$.cookie("iexist")) {
		return false;
	} else {
		$.cookie("iexist", "");
		return true;
	}
}

/**
 * "Close" a window by setting the cookie option as disabled and refreshing
 * 
 * @param container -
 *          the control of the container to close
 */
function closeWindow(container) {
	var stuff = container.split("_");
	$.cookie("option_" + stuff[0], false, { expires : 365 });
	hideElement(container);
	setConfigOptions();
}

function loadLastLocation() {
	var lastLocation = $.cookie("lastLocation");
	if (lastLocation) {
		useAddressToReposition(lastLocation);
	}	
}