/**
 * Checks / unchecks window options as per current cookie values.
 */
function setConfigOptions() {
	$("span[class=config2]").each(function() {
		var value = $.cookie("option_" + $(this).attr("id"));
		if (value == "true") {
			$(this).css("color", "#B0D730");
		} else {
			$(this).css("color", "#FFF");
		}
		$(this).hover(function() {$(this).css("color", "#B0D730")}, function() {$(this).css("color", "#FFF");});
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
		$("#" + container).css("color", "#B0D730");
		showElement(container + "_container");
		loadContainer(container);
	} else {
		$("#" + container).css("color", "#FFF");
		hideElement(container + "_container");
	}
}
