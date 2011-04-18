<div onmouseover="$(this).css('z-index', '100')" onmouseout="$(this).css('z-index', '10')" id="places_container" class="draggable panel">
	<span>
		<div class="header-left" title="Shows places of interest in the area.">
			Places
		</div>
		<div class="header-right">
			<img class="window-close" onclick="closeWindow($(this).parent().parent().parent().attr('id'))" src="/images/close.png"/>
		</div>
	</span>
 	<div class="detail-padded">
<!-- 
			Showing <div class="inline" id="places_category"><b>All Categories</b>. Click on a category in the list to use that category.</div>
-->
			Show me places that contain word or phrase
			<input title="Name of place to search for (or part of the name)"  type="text" name="places_query" id="places_query" value="" onkeypress="if (event.keyCode == 13) { updatePlacesLocationInformation(); }"/> 
			<input class="action-button" type="button" id="filter_places_now" name="filter_places_now" value="Go" onclick="updatePlacesLocationInformation();"/>

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
