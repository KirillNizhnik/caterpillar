<?php
add_shortcode( 'search-by-zip', 'wpcm_search_by_zip_shortcode' );
function wpcm_search_by_zip_shortcode( $atts ) {

    $atts = shortcode_atts( array(
        'title' => '',
        'action' => '',
        
    ), $atts );

    // Load WPCM JS scripts
    CM_Template::$add_scripts = true;
    $title = "";
    $page = "529";
    the_widget( 'CM_Widget_Search_By_Zip', array('title' => $title, 'action' => get_permalink( $page )));
}
