<?php

add_shortcode( 'locations-list', 'wpcm_locations_list_shortcode' );
function wpcm_locations_list_shortcode( $atts ) {

    // Load WPCM JS scripts
    CM_Template::$add_scripts = true;

    // Get alphabetized locations
    $q = new WP_Query(array(
        'post_type'      => 'location',
        'posts_per_page' => -1,
        'order' => 'ASC',
        'orderby' => 'title'
    ));
    $locations = array();
    while ( $q->have_posts() ) {
        $q->the_post();
        $locations[] = new CM_Location( get_the_id() );
    }
    wp_reset_postdata();

    // Include template
    ob_start();
    wpcm_template( 'locations-list', array( 'locations' => $locations ) );
    return ob_get_clean();
     echo do_shortcode('[facetwp facet="generic_load_more"]'); 
}
