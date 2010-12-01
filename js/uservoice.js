var uservoiceOptions = {
	/* required */
	key : 'lctn',
	host : 'lctn.uservoice.com',
	forum : '86707',
	showTab : true,
	/* optional */
	alignment : 'left',
	background_color : '#73654C',
	text_color : 'white',
	hover_color : '#4E84A6',
	lang : 'en'
};

function _loadUserVoice() {
	var s = document.createElement('script');
	s.setAttribute('type', 'text/javascript');
	s.setAttribute('src', ("https:" == document.location.protocol ? "https://"
			: "http://")
			+ "cdn.uservoice.com/javascripts/widgets/tab.js");
	document.getElementsByTagName('head')[0].appendChild(s);
}

_loadSuper = window.onload;
window.onload = (typeof window.onload != 'function') ? _loadUserVoice
		: function() {
			_loadSuper();
			_loadUserVoice();
		};
