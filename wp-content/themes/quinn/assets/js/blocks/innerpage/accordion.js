var FX = ( function( FX, $ ) {


	$( () => {
		FX.Accordion.init()
	})


	FX.Accordion = {
		init() {
			//$(".accordion-item:nth-child(2)").addClass('open');
			//$(".accordion-item:nth-child(2) .accordion-content").show();
			$('.accordion-title').append( '<span class="accordion-title-toggle"></span>');
			$(".accordion-title").click(function() {
			  $(this).parent().toggleClass('open');
			  $(this).next().slideToggle();
			  $('.accordion-item').not($(this).parent()).removeClass('open');
			  $('.accordion-content').not($(this).next()).slideUp();
			}); 
		},

	}

	

	return FX

} ( FX || {}, jQuery ) )