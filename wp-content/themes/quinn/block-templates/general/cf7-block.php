<section class="inner-contact">
	<!-- <div class="container">  made it have a double container-->
		<div class="inner-contact-wrapper">
			<div class="container section-margins">
			    <?php
			        $form = get_field('cf7_shortcode');

			        if( !empty( $form ) ) {
			            echo apply_shortcodes( $form );
			        }
			    ?>
			</div>
		</div>
	<!-- </div> -->
</section>
