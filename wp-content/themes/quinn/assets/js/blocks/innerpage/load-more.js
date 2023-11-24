var FX = ( function( FX, $ ) {


	$( () => {
		//FX.LoadMore.init()
	})


	FX.LoadMore = {
		init() {
			if($(".image-cards-block-load-content:hidden").length < 6) {
		      $("#loadImageCards").text("No More Deals and specials").addClass("noContent");
		    }
			  $(".image-cards-block-load-content").slice(0, 6).show();
			  $("#loadImageCards").on("click", function(e){
			    e.preventDefault();
			    $(".image-cards-block-load-content:hidden").slice(0, 6).slideDown();
			    if($(".image-cards-block-load-content:hidden").length == 0) {
			      $("#loadImageCards").text("No More Deals and specials").addClass("noContent");
			    }
			  });
		}
	}

	

	return FX

} ( FX || {}, jQuery ) )