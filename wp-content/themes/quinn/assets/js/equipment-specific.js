
var FXEquipment = (function(FXEquipment, $) {
    
     /**
     * Doc Ready
     */
    $(function() {
        
        FXEquipment.singleProducts.init();
          
          
         // console.log('this');
          //image changing logic:
          $('.product__thumbnail').each( function() {
              $(this).on('click', function(e) {
                  e.preventDefault();
                  $new_src = $(this).find('img').attr('src');
                  $('.media-browser__preview').find('img').attr('src', $new_src);
              });
          });
          
          
    });



    FXEquipment.singleProducts = {
            init: function() {
                
                

    
              /*  $('.js-image-thumbnail').magnificPopup({
                    type: 'image',
                    gallery: {
                        enabled: true
                    }
                }); */
                
                
                $('.js-video-thumbnail').magnificPopup({
                    type: 'iframe',
                    gallery: {
                        enabled: true
                    },
                    iframe: {
                        patterns: {
                            youtube: {
                              index: 'youtube.com/', // String that detects type of video (in this case YouTube). Simply via url.indexOf(index).
                        
                              id: 'v=', // String that splits URL in a two parts, second part should be %id%
                              // Or null - full URL will be returned
                              // Or a function that should return %id%, for example:
                              // id: function(url) { return 'parsed id'; }
                        
                              src: '//www.youtube.com/embed/%id%?autoplay=1' // URL that will be set as a source for iframe.
                            },
                            youtube2: {
                              index: 'youtu.be/', // String that detects type of video (in this case YouTube). Simply via url.indexOf(index).
                        
                              id: 'be/', // String that splits URL in a two parts, second part should be %id%
                              // Or null - full URL will be returned
                              // Or a function that should return %id%, for example:
                              // id: function(url) { return 'parsed id'; }
                        
                              src: '//www.youtube.com/embed/%id%?autoplay=1' // URL that will be set as a source for iframe.
                            },
                            vimeo: {
                              index: 'vimeo.com/',
                              id: '/',
                              src: '//player.vimeo.com/video/%id%?autoplay=1'
                            },
                        },
                         srcAction: 'iframe_src',
                    }
                }); 
                $('.js-vpt-thumbnail').magnificPopup({
                    type: 'iframe',
                    gallery: {
                        enabled: true
                    }
                });
    
                $('.js-thumbnails-scroller').mCustomScrollbar({
                    axis:"x",
                    theme: "light",
                    //setWidth: 500,
                    //setHeight: 95
                });
    
                $( '.video-start' ).on( 'click', function( event ) {
                    event.preventDefault();
                    $('.js-video-thumbnail').magnificPopup( 'open' );
                });
    
                $(document).on('FX/Tab/Switch', function(event) {
                    $(window).trigger('resize');
                    setTimeout(function(){
                        $('.js-thumbnails-scroller').mCustomScrollbar("update");
                    }, 200);
                });
            }
        };
        
     return FXEquipment;



}(FXEquipment || {}, jQuery));


document.addEventListener('DOMContentLoaded', function (event) {
  const defer = () => {
    try {
      const numberParsed = (originalPrice) => {
        try {
          var floatMatch = originalPrice
            .trim()
            .replace(',', '')
            .match(/\d+\.?\d?/g);
          return floatMatch && parseFloat(floatMatch.join(''));
        } catch (e) {
          if (console) {
            console.log(e);
          }
        }
      };

      const pullDataFromTable = (label) => {
        let field = jQuery(`:contains('${label}')`).closest('span').siblings();
        let result = null;
        if (field.length) {
          if (field[0].outerText !== 'N/A') {
            result = field[0].outerText;
          }
        }
        return result;
      };

      const pullDataFromSpecList = (label) => {
        let field = jQuery(`:contains('${label}')`)
          .closest('b')
          .parent()
          .siblings();

        let result = null;
        if (field.length) {
          if (field[0].outerText !== 'N/A') {
            result = field[0].outerText;
          }
        }
        return result;
      };

      const pullImage = () => {
        let field = jQuery('.product-detail__media-image img').attr('src');

        let result = null;
        if (!!field) {
          result = field;
        }
        return result;
      };

      if (window.jQuery) {
        if (window.jQuery("a:contains('Apply for Financing')").length > 0) {
          let equipmentCondition = 'new';
          let equipmentMake = '';

          //Equipment Location
          let equipmentCity = pullDataFromSpecList('City');
          let equipmentPostalCode = pullDataFromSpecList('Postal Code');
          let equipmentState = pullDataFromSpecList('State');
          let equipmentCountry = pullDataFromSpecList('Country');
          let stockId = pullDataFromTable('Unit Number');
          let equipmentSN = pullDataFromTable('Serial Number');
          let equipmentHours = pullDataFromTable('Hours');
          let equipmentYear = pullDataFromTable('Year');
          let price = pullDataFromTable('Price');
          let equipmentModel = pullDataFromSpecList('Model');
          let equipmentPictureUrl = pullImage();

          if (equipmentYear || equipmentHours) {
            equipmentCondition = 'used';
          }

          const equipmentDescription = jQuery(
            'input:hidden[name=post_title]'
          ).val();

          const button = jQuery("a:contains('Apply for Financing')");

          if (button) {
            let url = `https://app.dcrportal.com/oca/?vendorGUID=fec66ea1-c0c6-e311-90d0-005056a20000`;
            if (equipmentDescription) {
              url += `&equipmentDescription=${equipmentDescription}`;
            }
            if (price) {
              url += `&price=${numberParsed(price)}`;
            }
            if (equipmentPictureUrl) {
              url += `&equipmentPictureUrl=${equipmentPictureUrl}`;
            }
            if (equipmentCondition) {
              url += `&equipmentCondition=${equipmentCondition}`;
            }
            if (equipmentYear) {
              url += `&equipmentYear=${equipmentYear}`;
            }
            if (equipmentModel && equipmentCondition == 'used') {
              url += `&equipmentModel=${equipmentModel}`;
            }
            if (equipmentHours) {
              url += `&equipmentHours=${equipmentHours}`;
            }
            if (equipmentSN) {
              url += `&equipmentSN=${equipmentSN}`;
            }
            if (equipmentCity) {
              url += `&equipmentCity=${equipmentCity}`;
            }
            if (equipmentPostalCode) {
              url += `&equipmentPostalCode=${equipmentPostalCode}`;
            }
            if (equipmentState) {
              url += `&equipmentState=${equipmentState}`;
            }
            if (equipmentCountry) {
              url += `&equipmentCountry=${equipmentCountry}`;
            }
            if (stockId) {
              url += `&stockId=${stockId}`;
            }

            jQuery(button).attr('href', url);
          }
        }
      } else {
        setTimeout(function () {
          defer();
        }, 1000);
      }
    } catch (error) {
      if (console) {
        console.log('::::::::::: DCR Plugin error::::::::::::::');
        console.log(error.message);
        console.log('::::::::::::::::::::::::::::::::::::::::::');
      }
    }
  };

  function injectScriptAndUse() {
    if (typeof window.jQuery == 'undefined') {
      var head = document.getElementsByTagName('head')[0];
      var script = document.createElement('script');
      script.src =
        'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js';

      head.appendChild(script);
    }
  }
  injectScriptAndUse();
  defer(() => {});
});








