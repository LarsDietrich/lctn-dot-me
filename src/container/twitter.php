<div onmouseover="$(this).css('z-index', '1000')" onmouseout="$(this).css('z-index', '100')" id="twitter_container" class="draggable panel">
	<span>
		<div class="header-left" title="Shows tweets in the surrounding area for people that have chosen to share their location.">
			Twitter
		</div>
		<div class="header-right">
			<img class="window-close" onclick="closeWindow($(this).parent().parent().parent().attr('id'))" src="/images/close.png"/>
		</div>
	</span>
 	<div class="detail-padded">
		<center>
			Search for <input title="Enter a search term to filter tweets, comma seperate for multiple terms (eg. party, cool)" type="text" name="filter" id="filter" onkeypress="if (event.keyCode == 13) { updateTwitterLocationInformation(); }"/>
			in <input title="How big an area would you like to search for tweets in?" class="short-text" type="text" name="tweet_range" id="tweet_range" value="1" onkeypress="if (event.keyCode == 13) { updateTwitterLocationInformation(); }"/> km
			<input type="button" id="filter_now" name="filter_now" value="Go" onclick="updateTwitterLocationInformation();"/>
		</center>
	</div>
	<div class="detail-padded fixed-height-block-with-title">
		<div id="tweet_stream">No tweets found, try a bigger search area or search for something different</div>
	</div>
	<span>
	<div class="footer-text fixed-height-footer">
              <div id="twitter_footer"></div>
	</div>
	</span>
</div>
