// current version of web app
var version = "0.0.10";

// logged in user id
var user;

// reference to the main map in the map container.
var map;

// reference to the panorama of the streetview.
var panorama;

// stores current location (of type google.maps.LatLng).
var selectedLocation ;

// reference to the position marker on the map (of type google.maps.Marker).
var positionMarker;

// reference to the additional marker on the page, used to show twtter/wiki/webcam etc
// locations.
var infoMarker;

var infowindow = new google.maps.InfoWindow();

// reference to the geocoder for coding / decoding addresses.
var geocoder = new google.maps.Geocoder();

// information on the streetview/map, this information is saved and reloaded
// with the generated short url.
var heading = 0;
var pitch = 0;
var zoom = 12;
var latitude = 0;
var longitude = 0;
var maptype = "roadmap";

// cache to store a history of previously selected locations.
var locationCache = new Array();
// position in locationCache the user is currently at.
var currentSearchPosition = 0;

var shortUrlLoad = false;

function load() {
	$.extend( {
		getUrlVars : function() {
			var vars = {};
			var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
				vars[key] = value;
			});
			return vars;
		}
	});

	if ($.getUrlVars()['url']) {
		var url = $.getUrlVars()['url'];
	}	else {
		var url = "";
	}
	
	jx.load("decode.php?url=" + url, function(data) { loadUrlParameters(data); });

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
		
		$("#message_container").css("top", "0px" );
		$("#message_container").css("left", '-500px');


//		$.extend($.support, {
//      touch: "ontouchend" in document
//		});
//		
//		//
//		//Hook up touch events
//		//
//		$.fn.addTouch = function() {
//		      if ($.support.touch) {
//		              this.each(function(i,el){
//		                      el.addEventListener("touchstart", iPadTouchHandler, false);
//		                      el.addEventListener("touchmove", iPadTouchHandler, false);
//		                      el.addEventListener("touchend", iPadTouchHandler, false);
//		                      el.addEventListener("touchcancel", iPadTouchHandler, false);
//		              });
//		      }
//		};
//		
//		var lastTap = null;   
		
		
		$("div.panel").each(function() {
			var control = $(this);

//			$(control).draggable();
//			$(control).addTouch();
			
			$(control).Draggable( {
				handle : 'span',
				zIndex : '1000',
				opacity : 1.0,
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
				snapDistance : 10,
				grid : 10,
				containment : 'document'
			})

			$(control).css("top", ($.cookie($(control).attr("id") + "_top") ? $.cookie($(control).attr("id") + "_top") : 40));
			$(control).css("left", ($.cookie($(control).attr("id") + "_left")) ? $.cookie($(control).attr("id") + "_left") : 20);

			
		});

		
		$("#startup").overlay({
			top: 200,
			mask: {
				color: '#fff',
				loadSpeed: 200,
				opacity: 0.8
			},
			closeOnClick: false,
			load: true
		});

		// position the tooltip popups
		$(".config").each(function() {
			var control = $(this);
			$(control).tooltip({ position: "bottom center", opacity: 0.9});
		});
		$("#address").tooltip({ position: "bottom center", opacity: 0.9});

	});
}

/**
 * Decodes and loads the parameters passed through on the URL into variables.
 * Used to preload a location.
 */
function loadUrlParameters(encodedString) {

	var data = JSON.parse(Base64.decode(encodedString));

	// map
	var map = data.map;
	latitude = map.lat?parseFloat(map.lat):0;
	longitude = map.lng?parseFloat(map.lng):0;
	maptype = map.type?map.type:"roadmap";
	zoom = map.zoom?parseInt(map.zoom):12;

	if (!((latitude == 0.0) && (longitude == 0.0))) {
		selectedLocation =  new google.maps.LatLng(latitude, longitude);
		shortUrlLoad = true;
	}
	
	//streetview
	if (data.sv) {
		if (!isEnabled("streetview")) {
			setConfigOption("streetview");
		}
		heading = data.sv.heading?parseInt(data.sv.heading):0;
		pitch = data.sv.pitch?parseInt(data.sv.pitch):0;
	}

	//twitter
	if (data.tw) {
		if (!isEnabled("twitter")) {
			setConfigOption("twitter");
			
			
		}
		$("#tweet_range").val(data.tw.range);
		$("#tweet_filter").val(data.tw.filter);
	}

	//webcam
	if (data.wc) {
		if (!isEnabled("webcam")) {
			setConfigOption("webcam");
		}
		$("#webcam_range").val(data.wc.range);
	}

	//pictures
	if (data.picture) {
		if (!isEnabled("picture")) {
			setConfigOption("picture");
		}
		$("#picture_range").val(data.picture.range);
	}

	//wiki
	if (data.wiki) {
		if (!isEnabled("wiki")) {
			setConfigOption("wiki");
		}
		$("#wiki_range").val(data.wiki.range);
	}

	//directions
	if (data.route) {
		if (!isEnabled("route")) {
			setConfigOption("route");
		}
		$("#route_from").val(data.route.from);
	}

	start();
}

/**
 * This is the entry point for the page. Loads the necessary data, parses the
 * URL for location information and shows loads the page containers.
 */
function start(data) {

	setConfigOptions();

  if (!hasCookieSupport()) {
  	alert("Cookies are used extensively by this website to function, please enable cookie support and reload the website.");
  	return;
  }

	updateUrlWindow("");

	if (selectedLocation) {
		useAddressToRepositionLngLat(selectedLocation.lng() + "," + selectedLocation.lat());
	} else {
		if ($.cookie("lastLocation")) {
			loadLastLocation();
		} else {
			loadMap();
		}
	}

	showContainers();

}

/**
 * Tries to get the users current location using built in geolocation
 * functionality. Reloads the containers after attempt.
 */
function findMe() {

	setMessage("Accuracy not guaranteed.");

	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
			var location = position.coords.latitude + "," + position.coords.longitude;
			useAddressToReposition(location);
		}, function(error) {
			setMessage("Unable to find current location. " + error);
		});
	} else if (google.gears) {
		var geo = google.gears.factory.create('beta.geolocation');
		geo.getCurrentPosition(function(position) {
			var location = position.coords.latitude + "," + position.coords.longitude;
			useAddressToReposition(location);
		}, function(error) {
			setMessage("Unable to find current location. " + error);
		});
	} else {
		var location = geoip_latitude() + "," + geoip_longitude();
		useAddressToReposition(location);
	}

}

/**
 * Loads the Map in it's container based on the current value of
 * selectedLocation. Will also set the zoom and maptype if available and
 * position a marker at the point of selectedLocation. Also adds event listeners
 * to the map to handle clicks and movement.
 */
function loadMap() {

	if (!selectedLocation) {
		selectedLocation = new google.maps.LatLng(-26.0854, 27.9398);
	}
	
	var myMapOptions = {
		center : selectedLocation,
		streetViewControl : false,
		mapTypeControlOptions : {
			position : google.maps.ControlPosition.BOTTOM_LEFT,
			style : google.maps.MapTypeControlStyle.DROPDOWN_MENU
		},
		navigationControlOptions : {
			style : google.maps.NavigationControlStyle.DEFAULT,
			position : google.maps.ControlPosition.BOTTOM_LEFT
		},
		scaleControl : true
	}

	map = new google.maps.Map(document.getElementById("map_canvas"), myMapOptions);

	map.setZoom(zoom);
	map.setMapTypeId(maptype);

	positionMarker = new google.maps.Marker( {
		position : selectedLocation,
		map : map
	});

	google.maps.event.addListener(positionMarker, 'click', function() {
  	infowindow.setContent($("#address").val());
  	infowindow.open(map, positionMarker);
  });

	google.maps.event.addListener(map, 'click', function(event) {
		loading();
		selectedLocation = event.latLng;
		reloadContainers();
	});

	google.maps.event.addListener(map, 'zoom_changed', function() {
		zoom = map.getZoom();
	});

	google.maps.event.addListener(map, 'maptypeid_changed', function() {
		maptype = map.getMapTypeId();
	});

	google.maps.event.addListener(map, 'tilesloaded', function() {
		loading_end();
	});

	google.maps.event.addListener(map, 'idle', function() {
		loading_end();
	});

	google.maps.event.addListener(map, 'center_changed', function() {
		loading();
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
	infowindow.close();
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
	updateStats();
//	getStatistics();
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

//	if (isEnabled("user")) {
//		loadContainer("user");
//	}

	if (isEnabled("picture")) {
		loadContainer("picture");
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

		if (name == "picture") {
			updatePictureLocationInformation();
		}

		if (name == "webcam") {
			updateWebcamLocationInformation();
		}

//		if (name == "user") {
//			$("#user-name").html("You");
//			updateUserInformation();
//		}

		if (name == "route") {
			$("#route_stream").html("Type in an address and click <b>Go</b> to get directions to <b>" + $("#address").val() + "</b>");
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

	if (isEnabled("picture")) {
		$("#picture_container").css("display", "inline");
	} else {
		$("#picture_container").css("display", "none");
	}

	if (isEnabled("route")) {
		$("#route_container").css("display", "inline");
	} else {
		$("#route_container").css("display", "none");
	}

	if (isEnabled("user")) {
		$("#user_container").css("display", "inline");
	} else {
		$("#user_container").css("display", "none");
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
	jx.load("stats.php?do=stat&lat=" + selectedLocation.lat() + "&lng=" + selectedLocation.lng(), function(data) {});
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
		panorama.setVisible(true);
	} else {
		setMessage("Streetview not available at this location.");
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
		$('#message').html("");
	} else {
		$('#message').html(message);
		$("#message_container").animate({
		  top: "0px",
		  left: "0px",
		  opacity: 1
		}, 500, 'swing', function() {
			$("#message_container").delay(3000).animate({left: "-500px", top: "0px", opacity: '.0'}, 500, 'swing', function() {});
		});
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
	var address = $("#address").val();
	if (isEnabled("route")) {
		$("#route_stream").html("Type in an address and click <b>Go</b> to get directions to <b>" + $("#address").val() + "</b>");
	}
	geocoder.geocode( {
		'address' : address
	}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			if (shortUrlLoad) {
				shortUrlLoad = false; 
			} else {
				selectedLocation = results[0].geometry.location;
			}
			if (putInCache) {
				addToCache($("#address").val());
			}
			reloadContainers();
		} else {
			setMessage("Sorry, could not find it, try search for something else.");
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
 * Helper method to reposition based on coordinates where coords are swapped
 * around (long then lat).
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
		$("#timezone_stream").html("");
		output = "";
		var address = "";
		if (status == google.maps.GeocoderStatus.OK) {
			if (results.length > 0) {
				address = results[0].formatted_address;
				$("#address").val(address);
				if (isEnabled("route")) {
					$("#route_stream").html("Type in an address and click <b>Go</b> to get directions to <b>" + $("#address").val() + "</b>");
				}
				if (isEnabled("general")) {
					updateTimezoneLocationInformation();
				}
			} else {
				setMessage("No addresses were found at this location.");
			}
		} else {
			setMessage("Unable to determine physical address from current location");
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
	var longUrl = '{';
	// map
	longUrl += '"map":';
	longUrl += '{"lat":"' + selectedLocation.lat() + '",';
	longUrl += '"lng":"' + selectedLocation.lng() + '",';
	longUrl += '"zoom":"' + zoom + '",';
	longUrl += '"type":"' + maptype + '"}';
	
	if (isEnabled("streetview")) {
		longUrl += ',"sv":{';
		longUrl += '"heading":"' + heading + '",';
		longUrl += '"pitch":"' + pitch + '"}';
	}

	if (isEnabled("twitter")) {
		longUrl += ',"tw":{';
		longUrl += '"range":"' + $("#tweet_range").val() + '",';
		longUrl += '"filter":"' + $("#tweet_filter").val() +  '"}';
	}

	if (isEnabled("webcam")) {
		longUrl += ',"wc":{';
		longUrl += '"range":"' + $("#webcam_range").val() + '"}';
	}
	
	if (isEnabled("picture")) {
		longUrl += ',"picture":{';
		longUrl += '"range":"' + $("#picture_range").val() + '"}';
	}

	if (isEnabled("route")) {
		longUrl += ',"route":{';
		longUrl += '"from":"' + $("#route_from").val() + '"}';
	}

	if (isEnabled("wiki")) {
		longUrl += ',"wiki":{';
		longUrl += '"range":"' + $("#wiki_range").val() + '"}';
	}

	longUrl += '}';

	var shortUrl = "";
	user = user?user:"Unknown";
	jx.load("shrink.php?url=" + Base64.encode(longUrl) + "&user=" + user, function(data) {
		$("#url").val(root + data);
		updateUrlWindow(root + data);
		$("#share-window").animate({
		  top: "0px",
		  left: "0px",
		  opacity: 1
		}, 500, 'swing', function() {});
	});
}

/**
 * Hides the share bar after a certain amount of time;
 * 
 * @param timeout -
 *          how long to wait before hiding bar (ms)
 */
function hideShareBar(timeout) {
	$("#share-window").animate({left: "-500px", top: "0px", opacity: '.0'}, 500, 'swing', function() {});
}
/**
 * Updates the link url values of the social icons to the supplied link so that
 * clicking on them will trigger the relevant social add function correctly with
 * the shortened link.
 * 
 * @param link -
 *          the short url to pass to the social zone.
 */
function updateUrlWindow(link) {
	var output = "";

	output += "<img class=\"find-navigate\" src=\"/images/close.png\" onclick=\"hideShareBar()\"/>&nbsp;";
	
	output += "<div class=\"share-text\">Share this location (" + link + "): </div>";
	
	output += "<a onclick=\"hideShareBar(1000)\" href=\"http://twitter.com/home/?status=";
	output += link + "\"";
	output += " target=\"_blank\"><img class='social-button' src=\"images/twitter.png\" title=\"Tweet this location to the world.\" alt=\"Twitter\"></img></a>";

	output += "<a onclick=\"hideShareBar(1000)\" href=\"http://www.facebook.com/sharer.php?u=";
	output += link + "\"";
	output += " target=\"_blank\"><img class='social-button' src=\"images/facebook.png\" title=\"Share the location with your Facebook friends.\" alt=\"Facebook\"></img></a>";

	output += "<a onclick=\"hideShareBar(1000)\" href=\"http://del.icio.us/post?url=";
	output += link + "\"";
	output += " target=\"_blank\"><img class='social-button' src=\"images/delicious.png\" title=\"\Add the location to your Del.icio.us bookmarks.\" alt=\"Del.icio.us\"></img></a>";

	output += "<a onclick=\"hideShareBar(1000)\" href=\"mailto:?subject=Have a look at this location&body=";
	output += link + "\"";
	output += "><img class='social-button' src=\"images/email.png\" title=\"Email the location to a friend.\" alt=\"Send by Email\"></img></a>";

	$("#share-window").html(output);
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

///**
// * Display an image fullscreen as an overlay.
// * 
// * @param imageUrl -
// *          url of image to display
// */
//function fullscreenImage(imageUrl) {
//	var popup = $('#displaybox-no-opacity');
//	if (popup.css("display") == "none") {
//		popup.css("display", "");
//		popup.html("<img src=\"" + imageUrl+ "\" class=\"image-fullscreen\"/>");
//		setMessage("Click to close");
//	} else {
//		popup.css("display", "none");
//		popup.html("");
//	}
//	return false;
//}

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

/**
 * "Hilites" a row in the active container and shows the point on the map.
 * 
 * @param row -
 *          the element "row" to hilite, must have data- elements for data set
 *          (data-latitude, data-longitude, data-image, data-shadow-image)
 */
function highlightRow(row) {

	$(row).css("background-color", "#AEC2AE");

	if (infoMarker) {
		infoMarker.setMap(null);
	}
	
	var location = new google.maps.LatLng($(row).attr("data-latitude"), $(row).attr("data-longitude"));

	var image = new google.maps.MarkerImage($(row).attr("data-image"),
      new google.maps.Size(32.0, 32.0),
      new google.maps.Point(0, 0),
      new google.maps.Point(16.0, 16.0)
  );
  
	infoMarker = new google.maps.Marker( {
		position : location,
		icon : image
	});

  if ($(row).attr("data-shadow-image")) {
	  var shadow = new google.maps.MarkerImage($(row).attr("data-shadow-image"),
	      new google.maps.Size(49.0, 32.0),
	      new google.maps.Point(0, 0),
	      new google.maps.Point(16.0, 16.0)
	  );
		infoMarker.setShadow(shadow);
  }

  infoMarker.setMap(map);
	infoMarker.setZIndex(999);

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

/**
 * Zoom to a point on the map
 * 
 * @param lat -
 *          latitude
 * @param lng -
 *          longitude
 * @param zoom -
 *          zoom leve
 */
function zoomToPoint(lat, lng) {
	var point = new google.maps.LatLng(lat, lng);
	map.setCenter(point);
	map.setZoom(17);
}

function loading() {
	$("#loading").css("display","block");
}

function loading_end() {
	$("#loading").css("display", "none");
}

function popup(myLink, windowName) {
	var href;
	if (typeof(myLink) == "string") {
		href = myLink;
	} else {
		href = myLink.href;
	}
	window.open(href, windowName, "width=400,height=200,scrollbars=no,toolbar=no,location=no,menubar=no,diretories=no,status=no,resizeable=no,dependant=yes");
	return false;
}
