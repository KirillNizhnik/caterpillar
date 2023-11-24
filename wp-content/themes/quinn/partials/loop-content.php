<?php
	$thumb_id = get_post_thumbnail_id();

	// if no thumb ID, check for placeholder image (from ACF options page)
	if( empty( $thumb_id ) ) {
		$thumb_id = get_field( 'placeholder_image', 'option' );
	}

	$img_tag 	= fx_get_image_tag( $thumb_id, 'blog-post__img', 'medium' );
	$permalink 	= get_permalink();
	$terms 		= wp_get_object_terms( get_the_ID(), 'category' );
	$content = get_the_content();
    $content = preg_replace("/<img[^>]+\>/i", " ", $content);          
    $content = apply_filters('the_content', $content);
    $content = str_replace(']]>', ']]>', $content);
    $excerpt = wp_trim_words( $content, 20, ' &hellip;' );
?>

<article class="blog-post__item">
	<div class="blog-wrapper row">
		<?php if( !empty( $img_tag ) ): ?>
			<div class="col-sm-3">
				<a href="<?php echo esc_url( $permalink ); ?>">
					<?php echo $img_tag; ?>
				</a>
			</div>
		<?php endif; ?>
		<?php if( !empty( $img_tag ) ): ?>
		<div class="col-sm-9 blog-post__meta">
		<?php else:?>
			<div class="col-sm-12 blog-post__meta">
		<?php endif; ?>
			<h3 class="blog-post__title flush-top">
				<a href="<?php echo esc_url( $permalink ); ?>">
					<?php the_title(); ?>
				</a>
			</h3>
			<div class="blog-date">
				<h5>
					<?php $post_date = get_the_date( 'F j, Y' );
					echo $post_date; ?>
				</h5>
			</div>

			<?php if( !empty( $terms ) ): ?>
				<!-- <div class="blog-post__tags">
					<?php foreach( $terms as $term ): ?>
						<?php echo $term->name; ?>
					<?php endforeach; ?>
				</div> -->
			<?php endif; ?>

			<div class="blog-post__excerpt push-bottom">
				<p><?php echo $excerpt; ?> </p>
			</div>

			<a class="btn blog-post__link btn-tertiary" href="<?php echo esc_url( $permalink ); ?>">Read More</a>
		</div>
	</div>
</article>
