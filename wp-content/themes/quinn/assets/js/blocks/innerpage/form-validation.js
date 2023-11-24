var FX = ( function( FX, $ ) {


	$( () => {
		//FX.validationfx.init();
	});


	FX.validationfx = {
		init: function() {
			 $(".wpcf7[role='form']").first().find('form').validate({
			// $("#cf7-form-11634").validate({
		    // Specify validation rules
		    rules: {
				yourname: "required",
				yourjob: "required",
				yourcompany: "required",
				youraddress: "required",
				yourcity: "required",
				yourmessage: "required",
				yourzip: {
			        required: true,
			        digits: true,
			        minlength: 5,
			        maxlength: 5,
			    },
				phonenumber: {
					required: true,
				},
				youremailaddress: {
					required: true,
					email: true
				},
		    },
		    messages: {
		    	yourname: "Please enter your name",
		    	yourjob: "Please enter your job",
		    	yourcompany: "Please enter your company",
		    	youraddress: "Please enter your address",
		    	yourcity: "Please enter your city",    
		    	yourmessage: "Please enter your comment",
		    	yourzip: {
					required: "Please enter zip code",
					digits: "Please enter valid zip code",
					minlength: "zip code must be 5 digits",
					maxlength: "zip code must be 5 digits",
			    },
		    	phonenumber: {
					required: "Please enter phone number",
				},
				youremailaddress: {
					required: "Please enter email address",
    				email: "Please enter a valid email address.",
				},

		    },
		  
		  });
		}
	};
	return FX

} ( FX || {}, jQuery ) )