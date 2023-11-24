<?php 

//Registers a custom WP block to output a modal

add_action('acf/init', 'fx_modal_block');
function fx_modal_block() {

    // Check function exists.
    if( function_exists('acf_register_block_type') ) {

        // Register a modal block.
        acf_register_block_type(array(
            'name'              => 'fx_modal',
            'title'             => __('FX Modal'),
            'description'       => __('A custom FX Modal block.'),
            'render_template'   => MY_PLUGIN_PATH . 'templates/modal-template.php',
            'category'          => 'layout',
            'enqueue_style'     => '/wp-content/plugins/fx-modals/assets/css/fx-modals.css',
            'enqueue_script'    => '/wp-content/plugins/fx-modals/assets/js/fx-modals.js'
        ));
        
        // Register a modal block.
        acf_register_block_type(array(
            'name'              => 'fx_modal_button',
            'title'             => __('FX Modal Button'),
            'description'       => __('A custom FX Modal Button block.'),
            'render_template'   => MY_PLUGIN_PATH . 'templates/modal-button-template.php',
            'category'          => 'layout',
        ));
        
    }
}