<?php
add_shortcode( 'closest-location', 'wpcm_closest_location_shortcode' );
function wpcm_closest_location_shortcode( $atts ) {

    $atts = shortcode_atts( array(
        'title' => '',
        'prompt' => 'Show Closest Location'
    ), $atts );

    // Load WPCM JS scripts
    CM_Template::$add_scripts = true;

    the_widget( 'CM_Widget_Closest_Location', array( 'title' => $atts['title'], 'prompt' => $atts['prompt'] ) );
}
