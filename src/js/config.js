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
	var current = $.cookie("option_" + control.id);

	if (current == "true") {
		current = "false";
	} else {
		current = "true";
	}

	$.cookie("option_" + control.id, current, {
		expires : 365
	});
	
	if (current == "true") {
		$("#" + control.id).css("color", "white");
		showElement(control.id + "_container");
		loadContainer(control.id);
	} else {
		$("#" + control.id).css("color", "black");
		hideElement(control.id + "_container");
	}
}
