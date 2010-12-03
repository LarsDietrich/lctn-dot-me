// Current containers supported

var nextAvailableContainer;
var prevAvailableContainer;

// reference to the main map
var map;
// reference to the panorama of the streetview
var panorama;

// stores current location (google.maps.LatLng)
var selectedLocation;

// reference to the position marker on the map (google.maps.Marker)
var positionMarker;

// reference to the streetview service for looking up details
var streetViewService = new google.maps.StreetViewService();

// reference to the geocode for coding / decoding addresses
var geocoder = new google.maps.Geocoder();

// information on the streetview/map POV
var heading = 0;
var pitch = 0;
var zoom = 12;
var latitude;
var longitude;
var maptype = "roadmap";

var myMarker;

function loadUrlParameters() {

	latitude = $.getUrlVars()['lat'];
	if (!latitude) {
		latitude = 999;
	}

	longitude = $.getUrlVars()['lng'];
	if (!longitude) {
		longitude = 999;
	}

	heading = $.getUrlVars()['heading'];
	if (!heading) {
		heading = 0;
	}

	zoom = $.getUrlVars()['zoom'];
	if (!zoom) {
		zoom = 12;
	}

	maptype = $.getUrlVars()['maptype'];
	if (!maptype) {
		maptype = "roadmap";
	}

}

// load the necessary data, parse command line for location
// information and show map
function load() {

	$('#map_container').Draggable( {
		zIndex : 1000,
		handle : 'span'
	});

	loadUrlParameters();

	if (isEnabled("beta")) {
		beta();
	}

	updateUrlWindow("");

	if (latitude == 999 || longitude == 999) {
		findMe();
	} else {
		selectedLocation = new google.maps.LatLng(latitude, longitude);
		showMap();
		repositionMarker();
	}

	if (isEnabled("popup")) {
		$("[title]").tooltip( {
			effect : "slide"
		});
	}

	$(function() {

		// if the function argument is given to overlay, it is
		// assumed to be the onBeforeLoad event listener.

		$("a[rel]").overlay( {
			mask : '#C7D9D4',
			effect : 'apple',

			onBeforeLoad : function() {
				// grap wrapper element inside content
			var wrap = this.getOverlay().find(".contentWrap");
			// load the page specified in the trigger
			wrap.load(this.getTrigger().attr("href"));

		}
		});
	});

}

// 
// Tries to get the users current location using build in
// geolocation functionality.
//
function findMe() {
	if (navigator.geolocation) {
		navigator.geolocation
				.getCurrentPosition(
						function(position) {
							selectedLocation = new google.maps.LatLng(
									position.coords.latitude,
									position.coords.longitude);
							setMessage(
									"Repositioning map to best guess of where you are (accuracy not guaranteed)",
									"info");
							repositionMarker();
						},
						function(error) {
							setMessage(
									"Tried to get your location, but there was a problem, sorry",
									"error");
							selectedLocation = new google.maps.LatLng(0, 0);
							repositionMarker();
						});
	} else if (google.gears) {
		var geo = google.gears.factory.create('beta.geolocation');
		geo
				.getCurrentPosition(
						function(position) {
							selectedLocation = new google.maps.LatLng(
									position.coords.latitude,
									position.coords.longitude);
							setMessage(
									"Repositioning map to best guess of where you are (accuracy not guaranteed)",
									"info");
							repositionMarker();
						},
						function(error) {
							selectedLocation = new google.maps.LatLng(0, 0);
							setMessage(
									"Tried to get your location, but there was a problem, sorry",
									"error");
							repositionMarker();
						});
	}
}

//
// Loads the Google Map
//
function showMap() {

	var myOptions = {
		zoom : zoom,
		center : selectedLocation,
		mapTypeId : maptype,
		streetViewControl : false
	}

	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

	positionMarker = new google.maps.Marker( {
		position : selectedLocation,
		map : map,
		title : "You are here"
	});

	var panoOptions = {
		addressControlOptions : {
			position : google.maps.ControlPosition.BOTTOM,
			style : {
				"fontWeight" : "bold",
				"backgroundColor" : "#191970",
				"color" : "#A9203E"
			}
		},
		navigationControlOptions : {
			style : google.maps.NavigationControlStyle.SMALL
		},
		enableCloseButton : false,
		linksControl : true
	};

	panorama = new google.maps.StreetViewPanorama(document
			.getElementById("streetview"), panoOptions);

	setupListeners();

}

//
// Various listeners to listen for changes on the map(s):
// 
function setupListeners() {
	google.maps.event.addListener(map, 'click', function(event) {
		selectedLocation = event.latLng;
		repositionMarker();
	});

	google.maps.event.addListener(map, 'zoom_changed', function() {
		zoom = map.getZoom();
	});

	google.maps.event.addListener(map, 'maptypeid_changed', function() {
		maptype = map.getMapTypeId();
	});

	google.maps.event.addListener(panorama, 'position_changed', function() {
		selectedLocation = event.latLng;
		repositionMarker();
	});

	google.maps.event.addListener(panorama, 'pov_changed', function() {
		heading = panorama.getPov().heading;
		pitch = panorama.getPov().pitch;
	});
}

//
// Moves the marker to a new location specified by selectedLocation.
// Refreshes screen for anything
// that uses the location (like tweets and streetview)
//
function repositionMarker() {

	if (!map) {
		showMap();
	}
	if (myMarker) {
		myMarker.setMap(null);
	}
	positionMarker.setMap(null);
	positionMarker.setPosition(selectedLocation);
	positionMarker.setMap(map);
	streetViewService
			.getPanoramaByLocation(selectedLocation, 70, processSVData);
	updateWikiLocationInformation();
	updateTwitterLocationInformation();
	updateWeatherLocationInformation();
	reverseCodeLatLng();
	map.setCenter(selectedLocation);
	document.getElementById("url").value = "";
	setMessage("", "");
	updateStats();
}

// Tracks a click on the map for statistics purposes.
function updateStats() {
	jx.load("stats.php?do=stat&lat=" + selectedLocation.lat() + "&lng="
			+ selectedLocation.lng(), function(data) {
	});
}

//
// Try find street view data, load appropriate panorama panel and
// set selectedLocation.
// If the selectedLocation does not have a streetview associated
// with it, will
// attempt to find a streetview within a specified distance and
// reposition the map
// to that point.
//
function processSVData(data, status) {
	if (status == google.maps.StreetViewStatus.OK) {
		var markerPanoID = data.location.pano;
		panorama.setPano(markerPanoID);
		panorama.setPov( {
			heading : heading,
			pitch : pitch,
			zoom : 1
		});
		positionMarker.setMap(null);
		selectedLocation = data.location.latLng;
		positionMarker.setPosition(selectedLocation);
		positionMarker.setMap(map);
		panorama.setVisible(true);
	} else {
		setMessage("Streetview not available at this location.", "notice");
		panorama.setVisible(false);
	}
}

// 
// Sets a message in the upper right message display area
// 
function setMessage(message) {
	if (message == "") {
		document.getElementById("message").innerHTML = "";
	} else {
		document.getElementById('message').innerHTML = message;
	}
}

//
// Sets the selectedLocation based on address in address box
//
function locationFromAddr() {
	var address = document.getElementById("address").value;
	setMessage("", "");
	geocoder
			.geocode(
					{
						'address' : address
					},
					function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							selectedLocation = results[0].geometry.location;
							repositionMarker();
						} else {
							setMessage(
									"Not able to locate that place, please try something else.",
									"info");
						}
					});
}

//
// Reverse geocodes the address, moves the marker to the new
// location
//
function locationFromAddress(address) {
	document.getElementById("address").value = address;
	locationFromAddr();
}

//
// Sets the address box based on current selectedLocation
// coordinates
//
function reverseCodeLatLng() {
	geocoder
			.geocode(
					{
						'latLng' : selectedLocation
					},
					function(results, status) {
						document.getElementById("timezone_stream").innerHTML = "";
						output = "";
						var address = "";
						if (status == google.maps.GeocoderStatus.OK) {
							if (results.length > 0) {
								address = results[0].formatted_address;
								document.getElementById("address").value = address;
								updateTimezoneLocationInformation();
							} else {
								setMessage(
										"No addresses were found at this location.",
										"info");
							}
						} else {
							setMessage(
									"Unable to determine address from current location",
									"error");
						}
						updateGeneralLocationInformation(address);
					});
}

//
// Determine the shortened URL based on the current location, saves
// to DB
//
function shortenUrl() {
	root = "http://" + top.location.host + "/";
	longurl = root + "?lat=" + selectedLocation.lat() + "&lng="
			+ selectedLocation.lng() + "&heading=" + heading + "&pitch="
			+ pitch + "&zoom=" + zoom + "&maptype=" + maptype;
	shorturl = "";
	jx.load("shrink.php?shorturl=" + shorturl + "&url=" + escape(longurl),
			function(data) {
				document.getElementById("url").value = root + data;
				updateUrlWindow(root + data);
			});
}

//
// Updates the icons on the social bar with the current shortened
// link
//
function updateUrlWindow(link) {
	var output = "";

	output += "<a href=\"http://twitter.com/home/?status=";
	output += link + "\"";
	output += " target=\"_blank\"><img class='social-button' src=\"images/twitter.jpg\" title=\"Tweet this link to the world.\" alt=\"Twitter\"></img></a>";

	output += "<a href=\"http://www.facebook.com/sharer.php?u=";
	output += link + "\"";
	output += " target=\"_blank\"><img class='social-button' src=\"images/facebook.jpg\" title=\"Share the link with your Facebook friends.\" alt=\"Facebook\"></img></a>";

	output += "<a href=\"http://del.icio.us/post?url=";
	output += link + "\"";
	output += " target=\"_blank\"><img class='social-button' src=\"images/delicious.jpg\" title=\"\Add the link to your Del.icio.us account.\" alt=\"Del.icio.us\"></img></a>";

	output += "<a href=\"mailto:?subject=";
	output += link + "\"";
	output += "><img class='social-button' src=\"images/email.jpg\" title=\"Email the link to a friend.\" alt=\"Send by Email\"></img></a>";

	document.getElementById("url-window").innerHTML = output;

	if (isEnabled("popup")) {
		$("[title]").tooltip( {
			effect : "slide"
		});
	}

}

function updateGeneralLocationInformation(address) {
	var output = "You are positioned at <b>"
			+ Math.round(selectedLocation.lng() * 10000) / 10000
			+ "</b> longitude and <b>"
			+ Math.round(selectedLocation.lat() * 10000) / 10000
			+ "</b> latitude";
	if (!(address == "")) {
		output += ", which is also known as <b>" + address + "</b>";
	}
	output += ".";
	document.getElementById("location_stream").innerHTML = output;
}

//
// Display the beta page.
//
function beta() {
	var thediv = document.getElementById('displaybox');
	if (thediv.style.display == "none") {
		thediv.style.display = "";
		thediv.innerHTML = "<span class='displaybox-large'/>BETA</span><br/><span class='displaybox-normal'>This site is still under development, feel free to use it but expect some issues. I cannot take responsibility for the stability and accuracy of data being displayed.<br/><br/>Thank you for trying out the site.</span><br/><br/><span class='displaybox-normal'/>(click anywhere to close)</span>";
	} else {
		thediv.style.display = "none";
		thediv.innerHTML = '';
	}
	return false;
}

//
// Queries cookies for option to see if it's set to true. If null,
// assumes as never set
// and sets to true.
//
function isEnabled(option) {
	var result = false;
	var cookie = $.cookie("option_" + option);

	if (cookie == null) {
		$.cookie("option_" + option, "true");
	}

	if ($.cookie("option_" + option) == "true") {
		result = true;
	}
	return result;
}

function toggleMapSize() {
	var max_width = "955px";
	var normal_width = "470px";
	var max_height = "600px";
	var normal_height = "465px";

	if ($("#map_container").css("width") == max_width) {
		$("#map_container").css("width", normal_width);
		$("#map_canvas").css("width", normal_width);
		$("#map_container").css("height", normal_height);
		$("#map_canvas").css("height", normal_height);
		google.maps.event.trigger(map, "resize");
	} else {
		$("#map_container").css("width", max_width);
		$("#map_canvas").css("width", max_width);
		$("#map_container").css("height", max_height);
		$("#map_canvas").css("height", max_height);
		google.maps.event.trigger(map, "resize");
	}

	if (isEnabled("popup")) {
		$("[title]").tooltip( {
			effect : "slide"
		});
	}
}

//function loadContainer(container) {
//	$("#" + container).overlay( {
//
//		// custom top position
//		top : 260,
//
//		// some mask tweaks suitable for facebox-looking dialogs
//		mask : {
//
//			// you might also consider a "transparent" color for the mask
//			color : '#fff',
//
//			// load mask a little faster
//			loadSpeed : 200,
//
//			// very transparent
//			opacity : 0.5
//		},
//		// load it immediately after the construction
//		load : true
//
//	});
//
//}

function highlightRow(row, lat, lng) {
	$(row).css("background-color", "#AFD775");
	var location = new google.maps.LatLng(lat, lng);
	myMarker = new google.maps.Marker( {
		position : location,
		title : ""
	});
	myMarker.setMap(map);
}

function normalRow(row) {
	$(row).css("background-color", "transparent");
	myMarker.setMap(null);
}

$.extend( {
	getUrlVars : function() {
		var vars = {};
		var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,
				function(m, key, value) {
					vars[key] = value;
				});
		return vars;
	}
});

$(document).ready(function() {

	$("#find_container").Draggable( {
		handle : 'span',
		zIndex : '1000',
		onChange : function() {
			$.cookie("find_container_top", $(this).css("top"));
			$.cookie("find_container_left", $(this).css("left"));
		},
		onStop : function() {
			$(this).css("z-index", "999");
		},
		onStart : function () {
			$("div.panel").each(function() {
				$(this).css("z-index", "100");
			});
		}
	})
	$("#find_container").css("top", $.cookie("find_container_top"));
	$("#find_container").css("left", $.cookie("find_container_left"));
	$("#find_container").css("display", "inline");
	
	$("#share_container").Draggable( {
		handle : 'span',
		zIndex : '1000',
		onChange : function() {
			$.cookie("share_container_top", $(this).css("top"));
			$.cookie("share_container_left", $(this).css("left"));
			},
		onStop : function() {
			$(this).css("z-index", "999");
		},
		onStart : function () {
			$("div.panel").each(function() {
				$(this).css("z-index", "100");
			});
		}
	})
	$("#share_container").css("top", $.cookie("share_container_top"));
	$("#share_container").css("left", $.cookie("share_container_left"));
	$("#share_container").css("display", "inline");

	$("#map_container").Draggable( {
		handle : 'span',
		zIndex : '1000',
		onChange : function() {
			$.cookie("map_container_top", $(this).css("top"));
			$.cookie("map_container_left", $(this).css("left"));
		},
		onStop : function() {
			$(this).css("z-index", "999");
		},
		onStart : function () {
			$("div.panel").each(function() {
				$(this).css("z-index", "100");
			});
		}
	})
	$("#map_container").css("top", $.cookie("map_container_top"));
	$("#map_container").css("left", $.cookie("map_container_left"));
	$("#map_container").css("display", "inline");

	$("#twitter_container").Draggable( {
		handle : 'span',
		zIndex : '1000',
		onChange : function() {
			$.cookie("twitter_container_top", $(this).css("top"));
			$.cookie("twitter_container_left", $(this).css("left"));
		},
		onStop : function() {
			$(this).css("z-index", "999");
		},
		onStart : function () {
			$("div.panel").each(function() {
				$(this).css("z-index", "100");
			});
		}
	})
	$("#twitter_container").css("top", $.cookie("twitter_container_top"));
	$("#twitter_container").css("left", $.cookie("twitter_container_left"));
	$("#twitter_container").css("display", "inline");

	$("#wiki_container").Draggable( {
		handle : 'span',
		zIndex : '1000',
		onChange : function() {
			$.cookie("wiki_container_top", $(this).css("top"));
			$.cookie("wiki_container_left", $(this).css("left"));
		},
		onStop : function() {
			$(this).css("z-index", "999");
		},
		onStart : function () {
			$("div.panel").each(function() {
				$(this).css("z-index", "100");
			});
		}
	})
	$("#wiki_container").css("top", $.cookie("wiki_container_top"));
	$("#wiki_container").css("left", $.cookie("wiki_container_left"));
	$("#wiki_container").css("display", "inline");

	$("#general_container").Draggable( {
		handle : 'span',
		zIndex : '1000',
		onChange : function() {
			$.cookie("general_container_top", $(this).css("top"));
			$.cookie("general_container_left", $(this).css("left"));
		},
		onStop : function() {
			$(this).css("z-index", "999");
		},
		onStart : function () {
			$("div.panel").each(function() {
				$(this).css("z-index", "100");
			});
		}
	})
	$("#general_container").css("top", $.cookie("general_container_top"));
	$("#general_container").css("left", $.cookie("general_container_left"));
	$("#general_container").css("display", "inline");

	$("#streetview_container").Draggable( {
		handle : 'span',
		zIndex : '1000',
		onChange : function() {
			$.cookie("streetview_container_top", $(this).css("top"));
			$.cookie("streetview_container_left", $(this).css("left"));
		},
		onStop : function() {
			$(this).css("z-index", "999");
		},
		onStart : function () {
			$("div.panel").each(function() {
				$(this).css("z-index", "100");
			});
		}
	})
	$("#streetview_container").css("top", $.cookie("streetview_container_top"));
	$("#streetview_container").css("left", $.cookie("streetview_container_left"));
	$("#streetview_container").css("display", "inline");

});
