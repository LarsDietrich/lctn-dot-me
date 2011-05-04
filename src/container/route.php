<div onmouseover="$(this).css('z-index', '100')" onmouseout="$(this).css('z-index', '10')" id="route_container" class="draggable panel">
	<span>
		<div class="header-left" title="Route information">
			Directions
		</div>
		<div class="header-right">
			<img class="window-close" onclick="closeWindow($(this).parent().parent().parent().attr('id'))" src="/images/close.png"/>
		</div>
	</span>
 	<div class="detail-padded">
		<center>
<!-- 
			<input type="button" value="From" class="action-button" id="route_from_to_toggle" name="route_from_to_toggle" onclick='if ($("#route_from_to_toggle").val() == "From") {$("#route_from_to_toggle").val("To") } else {$("#route_from_to_toggle").val("From")};'/> 
-->
			From 
			<input title="Where to get directions from" type="text" name="route_from" id="route_from" onkeypress="if (event.keyCode == 13) { updateRouteInformation(); }"/>
			to 
			<input title="Where to get directions to" type="text" name="route_to" id="route_to" onkeypress="if (event.keyCode == 13) { updateRouteInformation(); }"/>
			<br/><input class="action-button" type="button" id="route_now" name="route_now" value="Go" onclick="updateRouteInformation();"/>
			<input class="action-button" type="button" id="print_route" name="print_route" value="Print" onclick="$('#route_stream').jqprint()"/>
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
