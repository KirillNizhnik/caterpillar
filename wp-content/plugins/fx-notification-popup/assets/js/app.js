var FXNP = ( function( FXNP, $ ) {


	$(window).on( 'load', () => {
		FXNP.Popup.init()
	})


	FXNP.Popup = {
		$popup:		null,
		$closeBtn: 	null,
		cookieName: null,
		

		init() {
			this.$popup		= $('.js-notification-popup')
			this.$closeBtn 	= $('.js-notification-popup-close')
			
			if( this.$popup.length && this.$closeBtn.length ) {
				this.getCookieName()
				this.checkForCookie()
			}
		},


		getCookieName() {
			let cookieName = 'fx_notification_popup_1'

			if( undefined !== FXNP.fx_notification_popup_time && FXNP.fx_notification_popup_time.length ) {
				cookieName = `fx_notification_popup_${FXNP.fx_notification_popup_time}`
			}

			this.cookieName = cookieName
		},


		checkForCookie() {
			let cookies = decodeURIComponent( document.cookie ).split( ';' )

			for( let i = 0; i < cookies.length; i++ ) {
				let cookie = cookies[i].trim()

				if( cookie.includes( this.cookieName ) ) {
					this.hidePopup()
					return
				}
			}

			this.showPopup()
			this.bind()
		},


		showPopup() {
			this.$popup.addClass('is-visible')
		},


		hidePopup() {
			this.$popup.removeClass('is-visible')
		},


		bind() {
			this.$closeBtn.on( 'click', this.handleBtnClick.bind( this ) )
		},


		handleBtnClick() {
			this.hidePopup()

			// set cookie to prevent notification popup from showing up again
			const date = new Date()
			date.setTime( date.getTime() + 2592000000 ) // +30 days

			document.cookie = `${this.cookieName}=1;expires=${date.toUTCString()};path=/`
		}
		
	}


	return FXNP

} ( FXNP || {}, jQuery ) )