var FX = ( function( FX, $ ) {


	$( () => {
		FX.HomeIndustrySlider.init()
	})


	FX.HomeIndustrySlider = {
		$slider: null,

		init() {
			this.$slider = $('.home-industry-list-slider')

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
                autoplaySpeed: 5000,
				infinite: false
            });
		}
	}



	return FX

} ( FX || {}, jQuery ) )
