<div  onmouseover="$(this).css('z-index', '100')" onmouseout="$(this).css('z-index', '10')" id="streetview_container" class="draggable panel">
	<span>
		<div class="header-left" title="Shows the streetview at the current location, streetview is only available in certain locations.">
			Streetview
		</div>
		<div class="header-right">
			<img class="window-close" onclick="closeWindow($(this).parent().parent().parent().attr('id'))" src="/images/close.png"/>
		</div>
	</span>
	<div class="detail-map">
		<div id="streetview_canvas" style="width: 467px; height: 465px"></div>
 	</div>
	<span>
	<div class="footer-text fixed-height-footer"></div>
	</span>

</div>
