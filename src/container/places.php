<div onmouseover="$(this).css('z-index', '1000')" onmouseout="$(this).css('z-index', '100')" id="places_container" class="draggable panel">
	<span>
		<div class="header-left" title="Shows places of interest in the area.">
			Places
		</div>
		<div class="header-right">
			<img class="window-close" onclick="closeWindow($(this).parent().parent().parent().attr('id'))" src="/images/close.png"/>
		</div>
	</span>
 	<div class="detail-padded">
		<center>
<!-- 
			Search for
			<input title="Name of place to search for (or part of the name)"  type="text" name="places_query" id="places_query" value="" onkeypress="if (event.keyCode == 13) { updatePlacesLocationInformation(); }"/> 
			in 
			<input title="How big an area would you like to search for places?" class="short-text" type="text" name="places_range" id="places_range" value="5" onkeypress="if (event.keyCode == 13) { updatePlacesLocationInformation(); }"/> km, 
-->
			I'm looking for  
			
			<select name="places_category" id="places_category" onchange="updatePlacesLocationInformation();" onkeypress="if (event.keyCode == 13) { updatePlacesLocationInformation(); }">
				<option value="">All Places</option>
<!-- 
				<option value="4bf58dd8d48988d1e0931735">Coffee Shops</option>
-->
			</select>


			<input type="button" id="filter_now" name="filter_now" value="Go" onclick="updatePlacesLocationInformation();"/>
		</center>
	</div>
	<div class="detail-padded fixed-height-block-with-title">
		<div id="places_stream">No places found</div>
	</div>
	<span>
	<div class="footer-text fixed-height-footer">
        <div id="places_footer"></div>
	</div>
	</span>
</div>
