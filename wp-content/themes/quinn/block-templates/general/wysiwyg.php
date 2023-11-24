<section class="wysiwyg section-padding <?php if( is_singular( 'location' ) ) { echo 'wysiwyg-location'; } ?> <?php the_field('background_color'); ?>">
	<div class="container">
		<div class="wysiwyg-content">
			<?php the_field( 'content' ); ?>
		</div>
	</div>
</section>