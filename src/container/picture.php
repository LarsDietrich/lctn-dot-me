<div onmouseover="$(this).css('z-index', '100')" onmouseout="$(this).css('z-index', '1')" id="picture_container" class="draggable panel">
	<span>
		<div class="header-left" title="Shows pictures in the area.">
			Photos
		</div>
		<div class="header-right">
			<img class="window-close" onclick="closeWindow($(this).parent().parent().parent().attr('id'))" src="/images/close.png"/>
		</div>
	</span>
 	<div class="detail-padded">
		<center>
			Show me all the public photos for <input title="How big of an area would you like to search for photos?" class="short-text" type="text" name="picture_range" id="picture_range" value="5" onkeypress="if (parseFloat($('#picture_range').val()) > 32) {$('#picture_range').val('32')}; if (event.keyCode == 13) { updatePictureLocationInformation(); }"/> km(s)
			<input class="action-button" type="button" id="filter_now" name="filter_now" value="Go" onclick="updatePictureLocationInformation();"/>
		</center>
	</div>
	<div class="detail-padded fixed-height-block-with-title">
		<div id="picture_stream"></div>
	</div>
	<span>
	<div class="footer-text fixed-height-footer">
    	<div id="picture_footer"></div>
	</div>
	</span>
</div>
