<?php if ( get_field('ag_enable_banner_image') ) { ?>
	<section class="masthead-inner masthead-enable-image">
		<div class="masthead-inner-texture-image ">
			<?php
				$enable_image_id = get_field('ag_select_banner_image');
				
				if ($enable_image_id) {
					echo fx_get_image_tag($enable_image_id);
				} else {
					echo fx_get_image_tag( 404 );
				}
			?>
		</div>
	</section>

	<section class="wysiwyg-enable-banner wysiwyg-white-background">
		<div class="container">
			<div class="wysiwyg-content">
				<h1><?php the_title(); ?></h1>
			</div>
		</div>	
	</section>

<?php } else { ?>

	<section class="masthead-inner <?php if( get_field('show_ag_header') == 'yes' ): ?> ag-header <?php endif; ?>">
		<div class="masthead-inner-texture-image ">
			<!-- <img src="../wp-content/themes/quinn/assets/img/masthead-inner-texture-image.jpg" class="img-responsive" alt=""> -->

			<?php 



			if( get_field('show_ag_header') == 'yes' ): 

				$image_id = get_field('ag_header_background');

				if( $image_id ):

						echo fx_get_image_tag($image_id);

					endif; 

				else: ?>
				<?php echo fx_get_image_tag( 404 ); ?>
			<?php endif; ?>
		</div>
		<div class="masthead-inner-overlay">
			<div class="container">
				<?php if ( is_search() ): ?>
					<h3 class="h1">Search Results</h3><?php /* different heading type for SEO benefit */  ?>
				<?php elseif ( is_home() ): ?>
					<h1 class="h1">Blog</h1><?php /* different heading type for SEO benefit */ ?>
				<?php elseif ( is_404() ) : ?>
					<h1>404: Oops! We can't find the page you're looking for.</h1>
				<?php elseif ( is_archive() ) : ?>
					<h1><?php echo get_the_archive_title(); ?></h1>
				<?php else : ?>
					<h1><?php the_title(); ?></h1>
				<?php endif; ?>

				<?php 
					if( function_exists( 'yoast_breadcrumb' ) ) {
						yoast_breadcrumb( '<div class="breadcrumbs hidden-sm-down">', '</div>' ); 
					}; 
				?>
			</div>
		</div>
	</section>
<?php } ?>
