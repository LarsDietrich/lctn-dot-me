<div  onmouseover="$(this).css('z-index', '100')" onmouseout="$(this).css('z-index', '10')" id="user_container" class="draggable panel">
	<span>
		<div class="header-left" title="Things you've done.">
			<div id="user-name"></div>
		</div>
		<div class="header-right">
			<img class="window-close" onclick="closeWindow($(this).parent().parent().parent().attr('id'))" src="/images/close.png"/>
		</div>
	</span>
	<div class="detail-padded fixed-height-block-with-title">
<!-- 		<a href="link/link.php" onclick="return popup(this, 'Link Accounts')">Link Accounts</a>
-->
		<div id="user_stream">User Information</div>
	</div>
	<span>
	<div class="footer-text fixed-height-footer">
              <div id="user_footer" class="footer"></div>
	</div>
	</span>
</div>