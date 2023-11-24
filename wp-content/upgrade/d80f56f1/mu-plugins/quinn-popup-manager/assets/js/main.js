var quinn_popup;
jQuery(function($) {

	quinn_popup = {
		
		cookie_expiration_days: 365,
		
		init: function() {
			quinn_popup.show_popup();
			quinn_popup.set_close_click_handler();
			quinn_popup.set_read_more_click_handler();
		},
		
		get_cookie_name: function() {
			return $('#quinn-popup-modal').data('popup');
		},
		
		show_popup : function() {
			
			if(!quinn_popup.cookie_exists()) {
			
				$.magnificPopup.open({
				  items: {
					src: '#quinn-popup-modal'
				  },
					type: 'inline',
					preloader: false,
					modal: true
				});
				
				quinn_popup.set_cookie();
				
			}
			
		},
		
		set_close_click_handler: function() {
			
			$('#quinn-popup-modal').find('.mfp-close').click(quinn_popup.set_cookie);
			
		},
		
		set_read_more_click_handler: function() {
			
			$('#quinn-popup-modal').find('.read-more-btn').click(quinn_popup.set_cookie);
			
		},
		
		set_cookie: function() {
			var cookie_name = quinn_popup.get_cookie_name();
			if(!quinn_popup.cookie_exists()) {
				Cookies.set(cookie_name, 'value', { expires: quinn_popup.cookie_expiration_days });
			}
		},
		
		cookie_exists: function() {
			var cookie_name = quinn_popup.get_cookie_name();
			return Cookies.get(cookie_name) === undefined ? false : true;
			
		}	
		
		
	};
	
	quinn_popup.init();

});