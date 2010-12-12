<div  onmouseover="$(this).css('z-index', '1000')" onmouseout="$(this).css('z-index', '100')" id="find_container" class="draggable panel">
<span>
	<div class="header">
		Find
	</div>
</span>
	<div class="detail">
		&nbsp;
		<img class="cacheimage" src="images/arrow-left.png" onclick="previousSearch()"/>
		&nbsp;
		<img class="cacheimage" src="images/arrow-right.png" onclick="nextSearch()"/>
		<input title="Enter an address or place name to search for (eg. Eiffel Tower or 10 Downing Street, London) then click Find or press Enter" type="text" class="title" name="address" id="address" value="" onkeypress="if (event.keyCode == 13) { locationFromAddr();}"/>
		<input class="large button" type="button" name="find" value="Find" onclick="locationFromAddr();"/>
	</div>
	<span>
	<div class="footer-clear"></div>
	</span>
</div>