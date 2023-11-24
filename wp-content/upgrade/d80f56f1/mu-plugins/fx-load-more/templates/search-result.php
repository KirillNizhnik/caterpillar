<?php
	$title 		= apply_filters( 'the_title', get_the_title() );
	$excerpt 	= apply_filters( 'the_excerpt', get_the_excerpt() );

	// highlight search query if in text
	$query 		= $args['query'];
	$marked 	= sprintf( '<mark class="search-highlighted">%s</mark>', esc_html( $query ) );
	$title 		= str_ireplace( $query, $marked, $title );
	$excerpt 	= str_ireplace( $query, $marked, $excerpt );
?>


<a class="search-result" href="<?php the_permalink(); ?>">
	<div class="search-result__img-container">
		<?php echo fx_get_image_tag( get_post_thumbnail_id(), 'search-result__img', 'thumbnail' ); ?>
	</div>
	<h2 class="search-result__title"><?php echo $title; ?></h2>
	<div class="search-result__excerpt">
		<?php echo $excerpt; ?>
	</div>
	<span class="btn btn--primary search-result__link">Read more</span>
</a>