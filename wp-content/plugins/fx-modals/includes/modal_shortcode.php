<?php

//Registers the shortcode for Modals

function prefix_register_resources() {
	wp_register_script("modal-script", "/wp-content/plugins/fx-modals/assets/js/fx-modals.js");
	wp_register_style("modal-style", "/wp-content/plugins/fx-modals/assets/css/fx-modals.css");
}
add_action( 'init', 'prefix_register_resources' );

function fx_modal_shortcode($atts) {
    $default = array(
        'id' => 1,
    );
    $attributes = shortcode_atts($default, $atts);
    $args = array(
        'p'         => $attributes['id'],
        'post_type' => 'fx_modal'
    );
    $modal = new WP_Query($args);
    if ( $modal->have_posts() ) :
        ob_start();
        while ( $modal->have_posts() ) : 
            $modal->the_post();
            $id = get_the_ID();
            ?>
            <div class="fx-modal-overlay">
                <div class="fx-modal-container" id="fx-modal-<?php echo $id; ?>">
                    <a class="fx-close-modal-button">X</a>
                    <div class="fx-modal-content"><?php the_field('modal_content', $id); ?></div>
                </div>
            </div> 
        <?php
        endwhile;
        wp_enqueue_script("modal-script");
	    wp_enqueue_style("modal-style");
        return ob_get_clean();
    endif;
    return '';
}   
add_shortcode('fx_modal', 'fx_modal_shortcode');


function add_fx_modals_column($columns) {
    return array_merge($columns, array('shortcode' => __('Shortcode')));
}
add_filter( 'manage_fx_modal_posts_columns', 'add_fx_modals_column' );

function fx_modals_custom_column( $column ) {
    global $post;
        if( $post->post_type == 'fx_modal' ){
            switch ( $column ) {
                case 'shortcode':
                	echo '[fx_modal id=' . $post->ID . ']';
                    break;
            }
    }
}
add_action( 'manage_fx_modal_posts_custom_column', 'fx_modals_custom_column' );