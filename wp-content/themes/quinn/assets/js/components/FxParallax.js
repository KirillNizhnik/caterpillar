/**
 * FxParallax
 * Parallax effect for images
 * @type {Object}
 */
( function( $ ) {

    /* Initialize */
    $( function() {
		new FxParallax()
	});

    FxParallax = class {

        constructor() {
            this.bind();
            this.scrollFrame = null;
        }

        bind() {
            $(window).on( 'scroll', this.handleScroll.bind(this) );
        }
        
        handleScroll(e) {
            
            // avoid constantly running function(s) on resize
			if( this.scrollFrame !== null )
				cancelAnimationFrame( this.scrollFrame );
            
            this.scrollFrame = window.requestAnimationFrame( this.scroll )
            
        }

        scroll() {
            $( '.js-parallax' ).each( function() {
                var $this   = $( this ),
                    speed   = $this.data( 'speed' ) || 6,
                    yPos    = -( $( window ).scrollTop() / speed ),
                    coords  = 'center  '+ yPos + 'px';
                    
                $this.css( { objectPosition: coords } ); /* based on parallax using an object-fit <img> */
            });
        }
    };

	
})( jQuery );