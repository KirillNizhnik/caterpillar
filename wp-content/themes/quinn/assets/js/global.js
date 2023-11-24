/* ---------------------------------------------------------------------
	Global Js
	Target Browsers: All

	HEADS UP! This script is for general functionality found on ALL pages and not tied to specific components, blocks, or
	plugins.

	If you need to add JS for a specific block or component, create a script file in js/components or js/blocks and
	add your JS there. (Don't forget to enqueue it!)
------------------------------------------------------------------------ */

var FX = ( function( FX, $ ) {

	/**
	 * Doc Ready
	 *
	 * Use a separate $(function() {}) block for each function call
	 *
	 */
	$(function() {
		FX.SlickSlider.init();
        
    });
    

	$( () => {
		FX.General.init(); // For super general or super short scripts
	})

    $( () => {
        FX.ExternalLinks.init(); // Enable by default
	})

    $( () => {
        //FX.Menuu.init();
	});
	
	$( () => {
        FX.customMCFXFields.init();
	})

    $(function() {
		//FX.MobileMenu.init();
		FX.MobileMenuSimple.init();
    });


	$(window).on( 'load', () => {
		FX.BackToTop.init()

// 		setInterval(function() {
//     $('#menu-item-8636').addClass('ubermenu-active'); /* replace #menu-item-200 with the menu item ID your working on */
// }, 1000 );
	})

	FX.customMCFXFields = {
		init() {
	        var self = this;
	        //console.log('test');
	        if($('.wpcf7-form').length > 0) {
	            setTimeout( function() {
	                console.log('cf7 form detected');
	                self.setFormParams();
	            }, 1500);
	        }
	    },
	    setFormParams() {
	        var medium = tracker.visitor.get( 'm' ).length > 0 ? tracker.visitor.get( 'm' ) : 'Medium unknown' ;
	        var referrer = tracker.visitor.get( 'r' ).length > 0 ? tracker.visitor.get( 'r' ) : 'referrer unknown' ;
            var source = tracker.visitor.get( 's' ).length > 0 ? tracker.visitor.get( 's' ) : 'source  unknown' ;
            var lastPageVisit = document.referrer.length > 0 ? document.referrer : 'JS Error retrieving last url - likely off site';
            var fullCookie = FX.customMCFXFields.getCookie('fx_info').replaceAll('%22' ,'"').replaceAll("%2C", ",").replaceAll("null", '"null"');
            var jsonCookie = JSON.parse(fullCookie);
                    
            var siteEntrancePage = jsonCookie.landingPage.length > 0 ? jsonCookie.landingPage : 'Referral URL unable to be captured - likely off site';
                    
            $('form.wpcf7-form').each(function() {
				var html = '<input type="hidden" name="fx-medium" value="'+ medium + '" class="wpcf7-hidden">';
                    html += '<input type="hidden" name="fx-referrer" value="'+ referrer + '" class="wpcf7-hidden">';
                    html += '<input type="hidden" name="fx-source" value="'+ source + '" class="wpcf7-hidden">';
                    html += '<input type="hidden" name="fx-last-page-visit" value="'+ lastPageVisit + '" class="wpcf7-hidden">';
                    html += '<input type="hidden" name="fx-site-entrace-page" value="'+ siteEntrancePage + '" class="wpcf7-hidden">';
                    
				$(this).append(html); 
            });
	        
			FX.customMCFXFields.check_product_category(lastPageVisit, 'catalog');
            FX.customMCFXFields.check_product_category(lastPageVisit, 'category');
	    },
	      
	    getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
        },
	      
	    check_product_category(url, type){
			jQuery.ajax({
				type: 'POST',
				url: FX.ajaxurl,
				dataType: 'json',
				data: {
					action : 'cat_check_url',
					url : url
				},
				success: function(msg){
					if(type == 'catalog'){
 
						$('form.wpcf7-form').each(function() {
							let html = '<input type="hidden" name="fx-product-catalog" value="'+ msg.catalog + '" class="wpcf7-hidden">';

							$(this).append(html);
						});

					}else if(type == 'category'){
						$('form.wpcf7-form').each(function() {
							let html = '<input type="hidden" name="fx-product-category" value="'+ msg.category + '" class="wpcf7-hidden">';

							$(this).append(html);
						});
					}
				}
			})
		}
	};

	/**
	 * Display scroll-to-top after a certain amount of pixels
	 * @type {Object}
	 */
	FX.BackToTop = {
		$btn: null,

		init() {
			this.$btn = $('.back-to-top');

			if( this.$btn.length ) {
				this.bind();
			}
		},

		bind() {
			$(window).on( 'scroll load', this.maybeShowButton.bind( this ) );
			this.$btn.on( 'click', this.scrollToTop );
		},

		maybeShowButton() {
			if( $( window ).scrollTop() > 100 ) { // TODO: Update "100" for how far down page to show button
				this.$btn.removeClass( 'hide' );
			} else {
				this.$btn.addClass( 'hide' );
			}
		},

		scrollToTop() {
			$(window).scrollTop( 0 );
		}
	};



	/**
	 * General functionality — ideal for one-liners or super-duper short code blocks
	 */
	FX.General = {
		init() {
			this.bind();
		},

		bind() {
            /*
			$('.open-popup-link').magnificPopup({
				//delegate: 'a',
				type: 'inline',
				gallery: {
					enabled: true
				}
			}); */

			//newsletter form
		    var url = window.location.href;
			$('input.wpcf7-form-control.wpcf7-hidden').attr("value",url);
			//referrers from request a quote
			var referrer = document.referrer;
			$('.referrer-hidden').attr('value', referrer);
			//referrer title from request a quote if there is no product:
			var button =  $('.intro-content .maxbutton-get-a-free-quote').attr('href');
			var referrerEmpty = $('.masthead-inner-overlay .container h1').text();
			// button = button.attr('href', button + "?yourmachine=");
			newURL = button +"?yourmachine="+referrerEmpty;
			console.log(button);
			$('.intro-content .maxbutton-get-a-free-quote').attr('href', newURL);
		    //refresh facetwp on used families because it gets stuck in the query and we want all results
          if (window.location.href.indexOf("used-equipment") > -1  && !(window.location.href.indexOf("used-equipment/machinery") > -1)) {
            //$('.fx-machine-search-reset').click();
		if ( 'undefined' !== typeof FWP ) {
            		FWP.reset();
		}

          }

            $('#yourreferral').on('change', function(e){
                var value = e.target.value
                if (value === 'Other') {
                    $('#yourreferralname').closest('.col-md-12').show();
                    $('#yourreferralname').val('');
                } else {
                    $('#yourreferralname').closest('.col-md-12').hide();
                    $('#yourreferralname').val(e.target.value);
                }
            });

            $('body :not(script)').contents().filter(function() {
            	return this.nodeType === 3;
            }).replaceWith(function() {
            	return this.nodeValue.replace(/[®]/g, '<sup>$&</sup>');
            });

			// Makes all PDF to open in new tabs
			$('a[href*=".pdf"]').each( e => {
				$(this).attr('target', '_blank');
			});

			// FitVids - responsive videos
			$('body').fitVids();

			// Input on focus remove placeholder
			$('input,textarea').focus( () => {
				$(this).removeAttr('placeholder');
			});

			// nav search toggle
			$('.js-search-toggle').on('click', () => {
				$('.desktop-menu__phone, .js-search-toggle, .desktop-menu__search').toggleClass('js-search-active');
                $('.desktop-menu__search input[name="s"]').focus();
			});

			$(".site-notification").click(function(e){
			    e.preventDefault();
			    if ($(".fixed-div").hasClass("active")){
			        $(".fixed-div").hide();
                } else {
                    $(".fixed-div").show();
                }
				$(".fixed-div").toggleClass("active");
				$('.selected-products').slick('refresh');
				//$('html').toggleClass("overf");
			})

			$(".mobile-search-toggle").click(function(){
				$(this).toggleClass("exit");
				$(".search-mobile").slideToggle(300);
			})

			// WYSIWYG
			//THIS WILL DELETE ALL CONTENT IN WYSIWYGS WITHOUT TEXT - don't uncomment unless going to fix thanks
		//	$('.wysiwyg').filter(function() {
			 // return $.trim($(this).text()) === ''
		//	}).remove()



		 // SPECS AND FEATURES TABS
			$('#tabs-nav-main li:first-child').addClass('active');
			$('.tab-content-main').hide();
			$('.tab-content-main:first').show();

			$('#tabs-nav-main li').click(function(){
			  $('#tabs-nav-main li').removeClass('active');
			  $(this).addClass('active');
			  $('.tab-content-main').hide();

			  var activeTab = $(this).find('a').attr('href');
			  $(activeTab).fadeIn(200);
			  return false;
			});

			// PHOTOS AND VIDEOS TABS
			$('#tabs-nav-thumb li:first-child').addClass('active');
			$('.tab-content-thumb').hide();
			$('.tab-content-thumb:first').show();

			$('#tabs-nav-thumb li').click(function(){
			  $('#tabs-nav-thumb li').removeClass('active');
			  $(this).addClass('active');
			  $('.tab-content-thumb').hide();

			  var activeTab = $(this).find('a').attr('href');
			  $(activeTab).fadeIn(200);
			  return false;
			});


		}
	};


	/**
	 * Mobile menu script for opening/closing menu and sub menus
	 * @type {Object}
	 */
	FX.MobileMenu = {
		init() {
			$('.nav-primary li.menu-item-has-children > a').after('<span class="sub-menu-toggle icon-caret-down"></span>');

			$('.sub-menu-toggle').click( function() {
				var $this = $(this),
					$parent = $this.closest("li"),
					$wrap = $parent.find("> .sub-menu");
				$wrap.toggleClass("js-toggled");
				$this.toggleClass('js-clicked');
				$this.toggleClass("js-toggled");
			});

			$('.ubermenu-responsive-toggle').click( function() {
                $('.page-header .ubermenu-responsive-toggle.close').toggleClass("reveal");
			});

		}
	};


	/**
	 * Slider/Carousel
	 * @type {Object}
	 */
	FX.SlickSlider = {
		init: function () {
			$('.product__thumbnails.images').slick({
                infinite: true,
                slidesToShow: 3,
				slidesToScroll: 1,
                dots: false,
                arrows: true,
                responsive: [

					{
                        breakpoint: 599,
                        settings: {
                            slidesToShow: 1,
							slidesToScroll: 1,
							arrows: true,
                        }
                    },
                     {
                            breakpoint: 'print',
                            settings: "unslick",
                    },
                ]
            }); 
            /* printer helper */
            /*
            window.addEventListener('beforeprint', function() {
                console.log('temp unslick');
              $('.product__thumbnails.images').slick('unslick');
            });
            window.addEventListener('afterprint', function() {
                      $('.product__thumbnails.images').slick({
                        infinite: true,
                        slidesToShow: 3,
        				slidesToScroll: 1,
                        dots: false,
                        arrows: true,
                        responsive: [

        					{
                                breakpoint: 599,
                                settings: {
                                    slidesToShow: 1,
        							slidesToScroll: 1,
        							arrows: true,
                                }
                            },
                             {
                                    breakpoint: 'print',
                                    settings: "unslick",
                                },
                        ]
                    });
            }); */
            /* end printer helper */

            /**/

            $('.selected-products').slick({
                infinite: true,
                slidesToShow: 1,
				slidesToScroll: 1,
                dots: false,
                arrows: true,
				mobileFirst:true,
                responsive: [
                	{
                        breakpoint: 599,
                        settings: {
                            slidesToShow: 2,
							slidesToScroll: 1,
							arrows: true,
                        }
                    },
					{
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 3,
							slidesToScroll: 1,
							arrows: true,
                        }
                    }
                ]
            });

			/* Preloader */
			 $('.js-slider-has-preloader').on('init', function (event, slick) {
			 	$('.js-slider-has-preloader').addClass('js-slider-has-preloader-init');
			 });

		}
	};



	/**
	 * Ubermenu mobile menu toggle hack
	 * @type {Object}
	 */
	FX.Menu = {
		windowWidth: 		0,
		$ubermenu: 			$('#ubermenu-nav-main-33'), // replace with ID of ubermenu element
		$topLevelMenuItems: null,
		$mobileMenuToggle: 	$('.ubermenu-responsive-toggle'),


        init() {
            this.setMenuClasses();
			this.setSubMenuClasses();

			this.$topLevelMenuItems = this.$ubermenu.children('.ubermenu-item-level-0');
			this.bind();
        },

        setMenuClasses() {
            let windowWidth = $( window ).innerWidth();

            // iOS fires resize event on scroll - let's first make sure the window width actually changed
            if ( windowWidth == this.windowWidth ) {
                return;
            }

            this.windowWidth = windowWidth;

            if ( this.windowWidth > 1025 ) {
                 $(".ubermenu-nav > li.ubermenu-has-submenu-mega").hover(
					function () {
					$(this).addClass('ubermenu-active');
					},
					function () {
					$(this).removeClass('ubermenu-active');
					}
			    );
            }

            if ( this.windowWidth < 1025 ) {
                $('.ubermenu-item-has-children').each( () => {
                    $(this).removeClass('ubermenu-has-submenu-drop');
                });

            } else {
                $('.ubermenu-item-has-children').each( () => {
                    $(this).addClass('ubermenu-has-submenu-drop');
                });
            }
        },

		setSubMenuClasses() {
			$('.ubermenu-item-has-children').each( () => {
                $(this).children('a').each( () => {
                    let $this = $(this);
                    $this.children('.ubermenu-sub-indicator').clone().insertAfter( $this).addClass('submenu-toggle hidden-md-up');
                    $this.children('.ubermenu-sub-indicator').addClass('hidden-sm-down');
                });
			});
		},

        bind() {
			$(window).on( 'resize', this.setMenuClasses.bind(this) );

			$('.submenu-toggle').on( 'touchstart click', this.toggleNextLevel );

			this.$topLevelMenuItems.on( 'ubermenuopen', this.handleUbermenuOpen.bind( this ) )
			this.$topLevelMenuItems.on( 'ubermenuclose', this.handleUbermenuClose.bind( this ) )

			// when clicking to open/close mobile menu toggle
			this.$mobileMenuToggle.on( 'ubermenutoggledopen', this.handleUbermenuOpen.bind( this ) )
			this.$mobileMenuToggle.on( 'ubermenutoggledclose', this.handleUbermenuClose.bind( this ) )
		},

		handleUbermenuOpen( e ) {
			const self = this,
				$container = self.$ubermenu.closest('.desktop-menu')

			$(document.body).addClass('menu-is-active')

			$container.addClass('menu-is-active')
			self.$mobileMenuToggle.addClass('menu-is-active');
			$("html").addClass('overf');
		},


		handleUbermenuClose( e ) {
			const self = this,
				$container = self.$ubermenu.closest('.desktop-menu')

			$(document.body).removeClass('menu-is-active')
			$container.removeClass('menu-is-active')
			self.$mobileMenuToggle.removeClass('menu-is-active');
			$("html").removeClass('overf');
		},


		 handleResponsiveToggleClick( e ) {
			const $btn = $(e.currentTarget),
				$menu = $('.desktop-menu').find('.ubermenu-main')

			$btn.toggleClass('menu-is-active', $menu.hasClass('ubermenu-responsive-collapse') )
		},


        toggleNextLevel( e ) {
            let $this = $( this );

			e.preventDefault();

            $this.toggleClass('fa-angle-down').toggleClass('fa-times');
            $this.parent().toggleClass('ubermenu-active');
            if( $this.parent().hasClass('ubermenu-active') ) {
                $this.parent().siblings('.ubermenu-active').removeClass('ubermenu-active').children('.submenu-toggle').addClass('fa-angle-down').removeClass('fa-times');
            }
        }
	}



	/**
	 * Force External Links to open in new window.
	 * @type {Object}
	 */
	FX.ExternalLinks = {
    init: function() {
      var siteUrlBase = FX.siteurl.replace( /^https?:\/\/((w){3})?/, '' );

      $( 'a[href*="//"]:not([href*="quinncompany.com"]):not([href*="www.quinncompany.com"])' )
        .not( '.ignore-external' ) // ignore class for excluding
        .addClass( 'external' )
        .attr( 'target', '_blank' )
        .attr( 'rel', 'noopener' );


    }
  };

	/**
	 * Affix
	 * Fixes sticky items on scroll
	 * @type {Object}
	 */
	FX.Affix = {

		$body: 			null,
		$header: 		null,
		headerHeight: 	null,
		scrollFrame: 	null,
		resizeFrame: 	null,


		init() {
			this.$body 			= $(document.body);
			this.$header 		= $('#page-header');
			this.headerHeight 	= this.$header.outerHeight( true );

			this.bind();
        },


        bind(e) {
			$(window).on( 'scroll', this.handleScroll.bind( this ) );
			$(window).on( 'resize', this.handleResize.bind( this ) );
		},


		handleScroll( e ) {
			var self = this;

			// avoid constantly running intensive function(s) on scroll
			if( null !== self.scrollFrame ) {
				cancelAnimationFrame( self.scrollFrame )
			}

			self.scrollFrame = requestAnimationFrame( self.maybeAffixHeader.bind( self ) )
		},


		handleResize( e ) {
			var self = this;

			// avoid constantly running intensive function(s) on resize
			if( null !== self.resizeFrame ) {
				cancelAnimationFrame( self.resizeFrame )
			}

			self.resizeFrame = requestAnimationFrame( () => {
				self.headerHeight = self.$header.outerHeight( true );
			})
		},


		maybeAffixHeader() {
			var self = this;

			if( 200 < $(window).scrollTop() ) {
				self.$body.css( 'padding-top', self.headerHeight );
				self.$header.addClass('js-scrolled');
			} else {
				self.$body.css( 'padding-top', 0 );
				self.$header.removeClass('js-scrolled');
			}
		}
	};



	/**
	 * FX.SmoothAnchors
	 * Smoothly Scroll to Anchor ID
	 * @type {Object}
	 */
	FX.SmoothAnchors = {
		hash: null,

		init() {
			this.hash = window.location.hash;

			if( '' !== this.hash ) {
				this.scrollToSmooth( this.hash );
			}

			this.bind();
		},

		bind() {
			$( 'a[href^="#"]' ).on( 'click', $.proxy( this.onClick, this ) );
		},

		onClick( e ) {
			e.preventDefault();

			var target = $( e.currentTarget ).attr( 'href' );

			this.scrollToSmooth( target );
		},

		scrollToSmooth( target ) {
			var $target = $( target ),
				headerHeight = 0 // TODO: if using sticky header change to $('#page-header').outerHeight(true)

			$target = ( $target.length ) ? $target : $( this.hash );

			if ( $target.length ) {
				var targetOffset = $target.offset().top - headerHeight;

				$( 'html, body' ).animate({
					scrollTop: targetOffset
				}, 600 );

				return false;
			}
		}
	};


        FX.Menuu = {
        windowWidth: 0,

        init: function() {
            this.setMenuClasses();
            $( window ).on( 'resize', $.proxy( this.setMenuClasses, this ) );
            $( '.ubermenu-item-has-children' ).each( function() {
                //console.log(this);
               // $(this).find('i.ubermenu-sub-indicator').remove();
                $( this ).children( 'a' ).each( function() {
                    let $this = $( this );
                    $this.children( '.ubermenu-sub-indicator' ).clone().insertAfter( $this ).addClass( 'submenu-toggle hidden-md-up' );
                    if(!$this.children('.ubermenu-sub-indicator').length){
                        console.log($(this));
                        //console.log($this + 'doesnt have a baby');
                        var subInd = '<i class="ubermenu-sub-indicator fas fa-angle-down submenu-toggle hidden-md-up"></i>';
                        //$(subInd).insertAfter($(this));
                       // $this.append('<i class="ubermenu-sub-indicator fas fa-angle-down submenu-toggle hidden-md-up"></i>');
                    }
                    $this.children( '.ubermenu-sub-indicator' ).addClass( 'hidden-sm-down' );
                });
            });
            this.bind();
        },

        setMenuClasses: function() {
            let windowWidth = $( window ).innerWidth();
            // iOS fires resize event on scroll - let's first make sure the window width actually changed
            if ( windowWidth == this.windowWidth ) {
                return;
            }
            this.windowWidth = windowWidth;
            if ( this.windowWidth < 1025 ) {
                $( '.ubermenu-item-has-children' ).each( function() {
                   // $( this ).addClass( 'ubermenu-has-submenu-drop' );
                });
            } else {
                $( '.ubermenu-item-has-children' ).each( function() {
                    $( this ).addClass( 'ubermenu-has-submenu-drop' );
                });
            }
        },

        bind: function() {
            $( '.submenu-toggle' ).on( 'touchstart', this.toggleNextLevel );
            $('.ubermenu-item-has-children').on('ubermenuclose', function() {
               // console.log('yep');
                $('.ubermenu-sub-indicator').show();
            });
        },

        toggleNextLevel: function( event ) {
            event.preventDefault();

            $(this).show();
            let $this = $( this );
            $this.toggleClass( 'fa-angle-down' ).toggleClass( 'fa-times' );
            $this.parent().toggleClass( 'ubermenu-active' );
            if ( $this.parent().hasClass( 'ubermenu-active' ) ) {
                $this.parent().siblings( '.ubermenu-active' ).removeClass( 'ubermenu-active' ).children( '.submenu-toggle' ).addClass( 'fa-angle-down' ).removeClass( 'fa-times' );
                $(this).hide();
                //console.log($this[0]);
               // console.log('hi2t');

            } else {
                console.log('hit');

            }
        }
    };

    /**
	 * Mobile menu script for opening/closing menu and sub menus
	 * @type {Object}
	 */
	FX.MobileMenuSimple = {
		$mobileMenu: 	$('#ubermenu-main-2'),
		$mobileToggle: 	$('.ubermenu-responsive-toggle'),


		init() {
			if( this.$mobileMenu.length && this.$mobileToggle.length ) {
			    //hide all sub sub menus
			    $('.ubermenu-submenu .ubermenu-submenu .ubermenu-submenu').hide();
			    //console.log('hit');
				this.maybeAddSubmenuToggles();
				this.bind();
			}
		},


		maybeAddSubmenuToggles() {

			// grab HTML from preexisting indicators for duplicating later
			const $baseIndicator 	= $('.ubermenu-sub-indicator').first(),
				$baseIndicatorClose = $('span.ubermenu-sub-indicator-close').first()

			if( !$baseIndicator.length || !$baseIndicatorClose.length ) {
			    //console.log('not');
				return
			}
           // console.log($baseIndicator);

			// loop through all menu items with submenus and ensure that they have a dropdown icon
			$('.ubermenu-item-has-children').children('.ubermenu-target').each( ( i, el ) => {
				const $target 		= $(el),
					$indicator 		= $target.children('.ubermenu-sub-indicator'),
					$indicatorClose = $target.children('.ubermenu-sub-indicator-close')

                //console.log($indicatorClose);
				// if THIS target doesn't have a dropdown icon, add it
				if( !$indicator.length ) {
					const $indicatorClone = $baseIndicator.clone().addClass('ubermenu-sub-indicator--submenu')

					$target.after( $indicatorClone )
				}

				// if THIS target doesn't have a flipped dropdown icon, add it
				if( !$indicatorClose.length ) {
					const $indicatorCloseClone = $baseIndicatorClose.clone().addClass('ubermenu-sub-indicator-close--submenu')
                    //$indicatorCloseClone = $baseIndicatorClose.clone()
					$target.after( $indicatorCloseClone )
				}
			})
		},


		bind() {
			this.$mobileMenu
				.on( 'ubermenuopen', this.handleUbermenuOpen.bind( this ) )
				.on( 'ubermenuclose', this.handleUbermenuClose.bind( this ) )

			this.$mobileToggle
				.on( 'ubermenutoggledopen', this.handleUbermenuToggleOpen.bind( this ) )
				.on( 'ubermenutoggledclose', this.handleUbermenuToggleClose.bind( this ) )

			$('.ubermenu-sub-indicator--submenu').on( 'click', this.handleIndicatorClick.bind( this ) )
			$('.ubermenu-sub-indicator-close--submenu').on( 'click', this.handleIndicatorCloseClick.bind( this ) )
		},


		handleUbermenuOpen() {
			// placeholder
		},


		handleUbermenuClose() {
			// placeholder
		},


		handleUbermenuToggleOpen() {
			this.$mobileToggle.addClass( 'ubermenu-is-expanded' )
		},


		handleUbermenuToggleClose() {
			this.$mobileToggle.removeClass( 'ubermenu-is-expanded' )
		},


		handleIndicatorClick( e ) {
		   // console.log('open');
		    //console.log($(e.currentTarget).closest('.ubermenu-item-has-children').find('ul.ubermenu-submenu'));
			$(e.currentTarget).closest('.ubermenu-item-has-children').addClass('submenu-is-expanded')
            $(e.currentTarget).closest('.ubermenu-item-has-children').find('ul.ubermenu-submenu').first().show();

		},


		handleIndicatorCloseClick( e ) {
		    //console.log('close');

			$(e.currentTarget).closest('.ubermenu-item-has-children').removeClass('submenu-is-expanded');

		},

	}




	return FX;

} ( FX || {}, jQuery ) );
