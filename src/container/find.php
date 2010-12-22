<div  onmouseover="$(this).css('z-index', '1000')" onmouseout="$(this).css('z-index', '100')" id="find_container" class="draggable panel">
<span>
	<div class="header">
		Find
	</div>
</span>
	<div class="detail">
		&nbsp;
		<img class="cacheimage" src="images/previous.png" onclick="previousSearch()"/>
		&nbsp;
		<img class="cacheimage" src="images/next.png" onclick="nextSearch()"/>
		<input title="Enter an address or place name to search for (eg. Eiffel Tower or 22 1st Avenue) then click Find or press Enter" type="text" class="title" name="address" id="address" value="" onkeypress="if (event.keyCode == 13) { locateAndRefresh(true);}"/>
		<input class="large button" type="button" name="find" value="Find" onclick="locateAndRefresh(true);"/>
	</div>
	<span>
	<div class="footer-clear"></div>
	</span>
</div>