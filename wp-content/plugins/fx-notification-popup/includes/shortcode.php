<?php

add_shortcode( 'fx_notification_popup', 'fx_notification_popup_shortcode' );
function fx_notification_popup_shortcode() {

	wp_enqueue_script( 'notification_popup' );
	wp_enqueue_style( 'notification_popup' );


	$color_background 	= get_field( 'notification_popup_background_color', 'option' );
	$color_text 		= get_field( 'notification_popup_text_color', 'option' );

	ob_start();

	?>

	<aside 
		class="notification-popup js-notification-popup"
		style="--colorBg: <?php echo esc_attr( $color_background ); ?>; --colorText: <?php echo esc_attr( $color_text ); ?>"
	>

		<div class="notification-popup__content">
			<?php the_field( 'notification_popup_content', 'option' ); ?>
		</div>

		<button class="notification-popup__close js-notification-popup-close" type="button"></button>	
	</aside>

	<?php

	return ob_get_clean();
}