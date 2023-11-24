/*
* This code is only used on the view machines functionality and should only be loaded in for that usage
*/

(function($){
		$(function(){
		    
		    $(".newbtn,.usedbtn").on('click', function(e) {
		        //button setting active logic
                var cat = $(this).attr('value');
                //console.log(this);
                $(".newbtn,.usedbtn").removeClass('active-button');

                    $(this).toggleClass('active-button');

                $('.filter-categ').attr('value',cat);
                $('#categ').attr('value', '');
                
                //swapout category logic 
                $('div.active-cat-dropdown').hide();
                $('div.active-cat-dropdown').toggleClass('active-cat-dropdown');
                if(cat == "cat_new_machine_family") { $('.just-new-dropdown').show().toggleClass('active-cat-dropdown'); }
                if(cat == "cat_used_machine_family") { $('.just-used-dropdown').show().toggleClass('active-cat-dropdown'); }
               // if(cat == "cat_new_allied_family") { $('.just-rental-dropdown').show().toggleClass('active-cat-dropdown'); }

                
            });
		    
			setTimeout(function() {
			    
              // var $pageLoc = document.location.pathname;
              //  var $tarLoc = /used-equipment/;
               // var $result = $tarLoc.test($pageLoc);

               // if($result !== true) {
                    if(window.location.href.indexOf('new') > -1 || window.location.href.indexOf('new-equipment') > -1 || window.location.href.indexOf('equipment/machines') > -1 ) {
                        $('.newbtn').click();
                        $('.filter-categ-wrap').attr("href", window.location.origin + "/equipment/machines/new/");
                    } else if (window.location.href.indexOf('used') > -1) {
                        $('.usedbtn').click();
                         $('.filter-categ-wrap').attr("href", window.location.origin + "/used-equipment/machinery/");
                    }
                //} else {
                    
                   
               // }
            }, 500); 

            
            $("#categ, #categ-new, #categ-used").on('change', function(e) {
                e.preventDefault();
                var cat_value = $(this).val();
                //console.log(cat_value);

                $("select#categ option").removeAttr("selected");

                $('.filter-categ-wrap').attr("href",cat_value);

            });
        
            
		});
	})(jQuery);