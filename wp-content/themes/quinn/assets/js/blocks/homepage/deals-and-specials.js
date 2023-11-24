var FX = ( function( FX, $ ) {


	$( () => {
		FX.HomeDeals.init()
	})


	FX.HomeDeals = {
		$slider: null,

		init() {
			this.$slider = $('.home-deals-slider')

			if( this.$slider.length ) {
				this.applySlick()
			}
		},

		applySlick() {
            this.$slider.slick( {
                dots: false,
                autoplay: false,
                arrows: true,
                fade: true,
                infinite: false,

                //adaptiveHeight: true
            });
		}
	}



	return FX

} ( FX || {}, jQuery ) )
