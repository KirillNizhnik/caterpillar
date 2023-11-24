<section class="home-cta">
        <div class="home-cta-background">
            <div class="hidden-sm-down" >
            	<?php 
				$image = get_field('desktop_-_image');
				$size = 'full'; // (thumbnail, medium, large, full or custom size)
				if( $image ) {
				    echo fx_get_image_tag( $image, $size );
				}?>
            </div>
            <div class="hidden-xxs-down hidden-md-up" >
            	<?php 
				$image = get_field('tablet_-_image');
				$size = 'full'; // (thumbnail, medium, large, full or custom size)
				if( $image ) {
				    echo fx_get_image_tag( $image, $size );
				}?>
            </div>
            <div class="hidden-sm-up" >
            	<?php 
				$image = get_field('mobile_-_image');
				$size = 'full'; // (thumbnail, medium, large, full or custom size)
				if( $image ) {
				    echo fx_get_image_tag( $image, $size );
				}?>
            </div>
        </div>
        <div class="home-cta-overlay">
            <div class="container">
                <div class="home-cta-wrapper">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="home-cta-content">
                                <h4><?php the_field('cta_heading'); ?></h4>
                                <p><?php the_field('cta_text'); ?></p>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="home-cta-find-location">
                                <h5>FIND A LOCATION</h5>
                               <?php echo do_shortcode("[search-by-zip]"); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>