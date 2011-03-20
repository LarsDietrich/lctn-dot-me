<div onmouseover="$(this).css('z-index', '100')" onmouseout="$(this).css('z-index', '1')" id="flickr_container" class="draggable panel">
	<span>
		<div class="header-left" title="Shows flickr images in the area.">
			Flickr
		</div>
		<div class="header-right">
			<img class="window-close" onclick="closeWindow($(this).parent().parent().parent().attr('id'))" src="/images/close.png"/>
		</div>
	</span>
 	<div class="detail-padded">
		<center>
			Show me all the flickr images for <input title="How big of an area would you like to search for images?" class="short-text" type="text" name="flickr_range" id="flickr_range" value="5" onkeypress="if (parseFloat($('#flickr_range').val()) > 32) {$('#flickr_range').val('32')}; if (event.keyCode == 13) { updateFlickrLocationInformation(); }"/> km(s)
			<input class="action-button" type="button" id="filter_now" name="filter_now" value="Go" onclick="updateFlickrLocationInformation();"/>
		</center>
	</div>
	<div class="detail-padded fixed-height-block-with-title">
		<div id="flickr_stream"></div>
	</div>
	<span>
	<div class="footer-text fixed-height-footer">
    	<div id="flickr_footer"></div>
	</div>
	</span>
</div>
