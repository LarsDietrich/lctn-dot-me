<div  onmouseover="$(this).css('z-index', '1000')" onmouseout="$(this).css('z-index', '100')" id="wiki_container" class="draggable panel">
	<span>
		<div class="header" title="Shows all wikipedia articles in the area.">
			Wikipedia
		</div>
	</span>
 	<div class="detail-padded">
		<center>
			Find me articles within <input title="How big an area would you like to see wikipedia articles for?" class="short-text" type="text" name="wiki_range" id="wiki_range" value="1" onkeypress="if (event.keyCode == 13) { updateWikiLocationInformation(); }"/> km
			<input type="button" id="filter_now" name="filter_now" value="Go" onclick="updateWikiLocationInformation();"/>								
		</center>
	</div>
	<div class="detail-padded fixed-height-block-with-title">
		<center>
			<div id="wiki_stream">No entries found, try a bigger search area</div>
		</center>
	</div>
	<span>
	<div class="footer-text fixed-height-footer">
              <div id="wiki_footer"></div>
	</div>
	</span>
</div>
