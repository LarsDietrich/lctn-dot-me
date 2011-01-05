<div style="z-index: 1100; display: inline;" id="user_container" class="draggable panel">
	<span>
		<div class="header-left" title="Things you've done.">
			<div id="user-name"></div>
		</div>
		<div class="header-right">
			<img class="window-close" onclick="closeWindow($(this).parent().parent().parent().attr('id'))" src="/images/close.png"/>
		</div>
	</span>
	<div class="detail-padded fixed-height-block-with-title">
		<div id="user_stream">User Information</div>
	</div>
	<span>
	<div class="footer-text fixed-height-footer">
              <div id="user_footer" class="footer"></div>
	</div>
	</span>
</div>