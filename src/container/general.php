<div onmouseover="$(this).css('z-index', '1000')" onmouseout="$(this).css('z-index', '100')" id="general_container" class="draggable panel">
	<span>
		<div class="header" title="Shows general information about the location.">
			General
		</div>
    </span>
	<div class="detail-padded fixed-height-block">
		<div class="general-text inline" id="location_stream"></div>
		<div class="general-text inline" id="timezone_stream"></div>
		<br/><br/>
		<div id="weather_stream"></div>
	</div>
	<span>
	<div class="footer-text fixed-height-footer"></div>
	</span>
</div>
