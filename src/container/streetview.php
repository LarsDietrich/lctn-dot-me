<div  onmouseover="$(this).css('z-index', '1000')" onmouseout="$(this).css('z-index', '100')" id="streetview_container" class="draggable panel">
	<span>
		<div class="header-left" title="Shows the streetview at the current location, streetview is only available in certain locations.">
			Streetview
		</div>
		<div class="header-right">
			<img onclick="closeWindow($(this).parent().parent().parent().attr('id'))" src="/images/close.png"/>
		</div>
	</span>
	<div class="detail-map">
		<div id="streetview" style="width: 467px; height: 465px"></div>
 	</div>
	<span>
	<div class="footer-text fixed-height-footer"></div>
	</span>

</div>
