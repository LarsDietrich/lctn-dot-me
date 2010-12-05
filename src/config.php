<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

	<head>
	    <title>lctn.me | A Location Portal - Find it, Share it</title>
	    
	    <script type="text/javascript" src="js/jquery-cookie.js"> </script>
	    
	    <script type="text/javascript">
	    	$("input[class=config]").each(function () {
				var value = $.cookie("option_" + $(this).attr("id"));
				if (value == "true" || value == null) {
					$(this).attr("checked",true);
				} else {
					$(this).attr("checked",false);
				}

	    	});

	    	function setOption(control) {
	    		$.cookie("option_" + control.id, control.checked);
	    	}
	    </script>
	    
	</head>
	<body>
		<div class="config-header">Turn on or off various portal features. Check the box next to the feature to enable it, uncheck to disable. A refresh is required to show the update(s).</div>
		<br/>
		<table>
			<tr class="config-row">
				<td width=20%">
					<label for="beta">Beta Warning</label>
				</td>
				<td width=10%">
					<input id="beta" type="checkbox" class="config" onclick="setOption(this);"></input>
				</td>
				<td>
					The beta warning screen on first load
				</td>
			</tr>
			<tr class="config-row">
				<td width=20%">
					<label for="streetview">Streetview</label>
				</td>
				<td width="10%">
					<input id="streetview" type="checkbox" class="config" onclick="setOption(this);"></input>
				</td>
				<td>
					The panel showing the streetview of the current location (if available)
				</td>
			</tr>
			<tr class="config-row">
				<td>
					<label for="twitter">Twitter</label>
				</td>
				<td>
					<input id="twitter" type="checkbox" class="config" onclick="setOption(this);"></input>
				</td>
				<td>
					The panel showing tweets around the current location.
				</td>
			</tr>
			<tr class="config-row">
				<td>
					<label for="general">General</label>
				</td>
				<td>
					<input id="general" type="checkbox" class="config" onclick="setOption(this);"></input>
				</td>
				<td>
					The panel containing general information.
				</td>
			</tr>
			<tr class="config-row">
				<td>
					<label for="wiki">Wikipedia</label>
				</td>
				<td>
					<input id="wiki" type="checkbox" class="config" onclick="setOption(this);"></input>
				</td>
				<td>
					The panel showing wikipedia articles around the location.
				</td>
			</tr>
		</table>
	</body>
</html>