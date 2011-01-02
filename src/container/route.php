<div onmouseover="$(this).css('z-index', '1000')" onmouseout="$(this).css('z-index', '100')" id="route_container" class="draggable panel">
	<span>
		<div class="header-left" title="Route information">
			Route
		</div>
		<div class="header-right">
			<img class="window-close" onclick="closeWindow($(this).parent().parent().parent().attr('id'))" src="/images/close.png"/>
		</div>
	</span>
 	<div class="detail-padded">
		<center>
			From <input title="Where to route from" type="text" name="route_from" id="route_from" onkeypress="if (event.keyCode == 13) { updateRouteInformation(); }"/>
			<input type="button" id="route_now" name="route_now" value="Go" onclick="updateRouteInformation();"/>
		</center>
	</div>
	<div class="detail-padded fixed-height-block-with-title">
		<div id="route_stream"></div>
	</div>
	<span>
	<div class="footer-text fixed-height-footer">
              <div id="route_footer" class="footer"></div>
	</div>
	</span>
</div>
