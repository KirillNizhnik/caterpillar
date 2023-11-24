<?php
	$thumb_id = get_post_thumbnail_id();

	// if no thumb ID, check for placeholder image (from ACF options page)
	if( empty( $thumb_id ) ) {
		$thumb_id = get_field( 'placeholder_image', 'option' );
	}

	$img_tag 	= fx_get_image_tag( $thumb_id, 'blog-post__img', 'medium' );
	$permalink 	= get_permalink();
	$terms 		= wp_get_object_terms( get_the_ID(), 'category' );
	$excerpt 	= wp_trim_words( get_the_excerpt(), 20, ' &hellip;' );
?>

<div class="col-xxs-12 col-xs-6 col-md-4">
	<article class="blog-post__item">

		<?php if( !empty( $img_tag ) ): ?>
			<a class="blog-post__img-container show" href="<?php echo esc_url( $permalink ); ?>">
				<?php echo $img_tag; ?>
			</a>
		<?php endif; ?>

		<div class="blog-post__meta">	
			<?php if( !empty( $terms ) ): ?>
				<div class="blog-post__tags">
					<?php foreach( $terms as $term ): ?>
						<a class="blog-post__tag" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo $term->name; ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<h3 class="blog-post__title">
				<a class="blog-post__title__link" href="<?php echo esc_url( $permalink ); ?>"><?php the_title(); ?></a>
			</h3>

			<div class="blog-post__excerpt push-bottom"><?php echo $excerpt; ?></div>

			<a class="blog-post__link btn-tertiary" href="<?php echo esc_url( $permalink ); ?>">Read More</a>
		</div>
		
	</article>
</div>
