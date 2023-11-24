<?php


add_shortcode( 'fx_load_more_pagination', 'fx_load_more_pagination_shortcode' );
function fx_load_more_pagination_shortcode() {
	ob_start();

	?>

	<div class="load-more js-load-more">
		<div class="load-more__counter js-load-more-counter"></div>
		<progress class="load-more__progress js-load-more-progress"></progress>
		<button class="btn load-more__btn js-load-more-btn">Load more</button>
	</div>

	<?php // for users not using JS or crawlers/bots ?>
	<noscript>
		<?php if( function_exists( 'wp_pagenavi' ) ): ?>
			<?php wp_pagenavi(); ?>
		<?php endif; ?>
	</noscript>


	<?php

	$output = ob_get_clean();
	return $output;
}