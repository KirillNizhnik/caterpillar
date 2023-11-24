<?php
add_shortcode( 'locations-map', 'wpcm_locations_map_shortcode' );
function wpcm_locations_map_shortcode( $atts ) {

    // Load WPCM JS scripts
    CM_Template::$add_scripts = true;

    // Include template
    ob_start();
    wpcm_template( 'locations-map' );
    return ob_get_clean();
}
