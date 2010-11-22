<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

	<head>
	    <title>lctn.me | A Location Portal - Find it, Share it</title>
	    
	    <script type="text/javascript" src="js/jquery-cookie.js"> </script>
	    
	    <script type="text/javascript">
	    	$("input[class=config]").each(function () {
				var value = $.cookie("option_" + $(this).attr("id"));
				if (value == "true") {
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
		<div class="config-header">Turn on or off various portal features. Check the box next to the feature to enable it.</div>
		<br/>
		<table>
			<tr class="config-row-even">
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
			<tr class="config-row-odd">
				<td width=20%">
					<label for="streetview">Streetview</label>
				</td>
				<td width="10%">
					<input id="streetview" type="checkbox" class="config" onclick="setOption(this);"></input>
				</td>
				<td>
					The Streetview window
				</td>
			</tr>
			<tr class="config-row-even">
				<td>
					<label for="popup">Tooltips</label>
				</td>
				<td>
					<input id="popup" type="checkbox" class="config" onclick="setOption(this);"></input>
				</td>
				<td>
					The tooltip popup help text (requires a refresh)
				</td>
			</tr>
			<tr class="config-row-odd">
				<td>
					<label for="twitter">Twitter</label>
				</td>
				<td>
					<input id="twitter" type="checkbox" class="config" onclick="setOption(this);"></input>
				</td>
				<td>
					The Twitter window
				</td>
			</tr>
			<tr class="config-row-even">
				<td>
					<label for="weather">Weather</label>
				</td>
				<td>
					<input id="weather" type="checkbox" class="config" onclick="setOption(this);"></input>
				</td>
				<td>
					The weather forecast in the General window (requires a change in location)
				</td>
			</tr>
			<tr class="config-row-odd">
				<td>
					<label for="wiki">Wikipedia</label>
				</td>
				<td>
					<input id="wiki" type="checkbox" class="config" onclick="setOption(this);"></input>
				</td>
				<td>
					The Wikipedia window
				</td>
			</tr>
		</table>
	</body>
</html>