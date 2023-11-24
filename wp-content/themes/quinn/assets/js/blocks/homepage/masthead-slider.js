var FX = ( function( FX, $ ) {


	$( () => {
		FX.HomepageMasthead.init()
	})


	FX.HomepageMasthead = {
		$slider: null,

		init() {
			this.$slider = $('.masthead-homepage-slider')

			if( this.$slider.length ) {
				this.applySlick()
			}
		},

		applySlick() {
            this.$slider.slick( {
                dots: true,
                autoplay: false,
                arrows: false,
                fade: true,
                autoplay: true,
                autoplaySpeed: 5000,
            });
		}
	}

	

	return FX

} ( FX || {}, jQuery ) )