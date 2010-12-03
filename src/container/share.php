<div  onmouseover="$(this).css('z-index', '1000')" onmouseout="$(this).css('z-index', '100')" id="share_container" class="draggable panel">
<span>
	<div class="header" title="Share this location with friends, generate the link and click on one of the social icons.">
	Share
	</div>
</span>
	<div class="detail" >
		<center>
			<input class="large button" type="button" name="generate" value="Shorten" onclick="shortenUrl();"/>
			<input title="Click Shorten to generate a short url. The link will point back to this location and open the page as you see it (with live data updated)." type="text" class="url-text" name="url" id="url" value="" readonly="readonly"/>
			<div class="inline" id="url-window"></div>
		</center>
	</div>
	<span>
	<div class="footer-clear"></div>
	</span>
</div>