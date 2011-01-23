/**
 * Checks / unchecks window options as per current cookie values.
 */
function setConfigOptions() {
	$("label[class=config]").each(function() {
		var value = $.cookie("option_" + $(this).attr("id"));
		if (value == "true") {
			$(this).css("color", "white");
		} else {
			$(this).css("color", "black");
		}
	});
}

/**
 * Sets a particular cookie option when checked
 * 
 * @param control -
 *          control to check/uncheck
 */
function setConfigOption(control) {

	var container = control.id?control.id:control;
	
	var current = $.cookie("option_" + container);
	
	if (current == "true") {
		current = "false";
	} else {
		current = "true";
	}

	$.cookie("option_" + container, current, {
		expires : 365
	});
	
	if (current == "true") {
		$("#" + container).css("color", "white");
		showElement(container + "_container");
		loadContainer(container);
	} else {
		$("#" + container).css("color", "black");
		hideElement(container + "_container");
	}
}
