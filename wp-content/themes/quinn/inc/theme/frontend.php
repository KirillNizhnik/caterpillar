<?php

/**
 * Set Up theme support and functionality
 *
 * @return void
 */
add_action( 'after_setup_theme', 'fx_setup' );
function fx_setup() {
    add_editor_style();
    add_theme_support( 'title-tag' );

    // Theme Images
    add_theme_support( 'post-thumbnails' );
    
    // Image Sizes
    add_image_size( 'masthead', 1920, 600 ); // true hard crops, false proportional

    // HTML5 Support
    add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );
}


/**
 * Register menu functionality, initilize plugin functionality
 *
 * @return void
 */
add_action( 'init', 'fx_init' );
function fx_init() {
    // Register Menu
    register_nav_menus(
        array(
            'footer_menu'  => 'Navigation items for footer navigation.',
            'main_menu' => 'Navigation items for the main menu.'
        )
    );
}


/**
 *  Register sidebars and widgets
 *
 *  @return  void
 */
add_action( 'widgets_init', 'fx_widget_init' );
function fx_widget_init() {
    // Sidebar
    register_sidebar(
        array(
            'name'          => 'Main Sidebar Widgets',
            'id'            => 'sidebar',
            'description'   => 'Widgets for the default sidebar',
            'before_title'  => '<h3>',
            'after_title'   => '</h3>',
            'before_widget' => '<div class="widget %2$s" id="%1$s" >',
            'after_widget'  => '</div>',
        )
    );
}


add_filter( 'wpseo_breadcrumb_single_link_wrapper', 'fx_change_breadcrumb_single_wrapper' );
/* Change Yoast default breadcrumb wrapper to li */
function fx_change_breadcrumb_single_wrapper() {
    return 'li';
}


add_filter( 'wpseo_breadcrumb_single_link_with_sep', 'fx_remove_breadcrumb_single_link_sep', 10, 2 );
/* Remove yoast breadcrumb link separator */
function fx_remove_breadcrumb_single_link_sep( $output ) {
    return str_replace( '|', '', $output );
}


/**
 * Prevents WordPress from natively adding "loading='lazy'" to 
 * media elements. (We'll offload that work to WP Rocket.)
 *
 * @return  bool
 */
add_filter( 'wp_lazy_loading_enabled', '__return_false' );


/**
 * Adds bootstrap .container > .row > .col-xxs-12 wrapper around
 * blocks so non-fx blocks can be styled nicely with padding.
 * Note: use the fx_wrap_block filter to change the default wrapping
 *  behavior of a block.
 */
add_filter( 'render_block', 'fx_wrap_blocks', 10, 2 );
function fx_wrap_blocks( $block_content, $block ) {
    $block_name = $block['blockName'];
    $wrap_block = true;
    $wrap_block = apply_filters('fx_wrap_block', $wrap_block, $block_name);
    
    if ( $wrap_block ) {
        $block_content = '<div class="container"><div class="row"><div class="col-xxs-12">' . $block_content . '</div></div></div>';
    }

    return $block_content;
}

add_filter('fx_wrap_block', 'fx_block_wrap_defaults', 10, 2);
/**
 * Logic to specify whether or not a block should be wrapped with 
 * a bootstrap wrapper. (Note: this is typically for non-fx blocks)
 */
function fx_block_wrap_defaults($wrap_block, $block_name) {
    // Don't wrap empty blocks with no name
    if ( is_null( $block_name ) )
        return false;
    // Don't wrap FX (acf) blocks
    if ( false !== strpos( $block_name, 'acf/' ) )
        return false;
    // Don't wrap button blocks
    if ( 'core/button' == $block_name )
        return false;
    
    // TODO add logic for blocks that should/shouldn't be wrapped in a bootstrap row here
    return $wrap_block;
}


add_action( 'pre_get_posts', 'fx_blogs_by_date', 100, 1);
function fx_blogs_by_date($query) {
    if( is_admin() ) {
		
		return $query;
		
	}
	
//	ob_start();                   
   // var_dump( $query );           
   // $contents = ob_get_contents(); 
   // ob_end_clean();               
   // error_log( $contents, 3, WP_CONTENT_DIR . '/jake.log' ); 
	
	// only modify queries for 'event' post type
	if( isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'post' ) {
		

    		$query->set('orderby', 'date');
			$query->set('order', 'DESC');
		
	}
	// return
	return $query;
} 

/**
 * Disable the emoji's
 */
function disable_emojis() {
 remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
 remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
 remove_action( 'wp_print_styles', 'print_emoji_styles' );
 remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
 remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
 remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
 remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
 add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
 add_filter( 'wp_resource_hints', 'disable_emojis_remove_dns_prefetch', 10, 2 );
}
add_action( 'init', 'disable_emojis' );

/**
 * Filter function used to remove the tinymce emoji plugin.
 * 
 * @param array $plugins 
 * @return array Difference betwen the two arrays
 */
function disable_emojis_tinymce( $plugins ) {
 if ( is_array( $plugins ) ) {
 return array_diff( $plugins, array( 'wpemoji' ) );
 } else {
 return array();
 }
}


/**
 * add class when BAnner image option (true/false) is enable.
 */

function add_acf_body_class($class) {
    $value = get_field('ag_enable_banner_image');
	if ($value==1) {
		$name = 'page-enable-banner-image';
        $class[] = $name;
	}

    return $class;
}
add_filter('body_class', 'add_acf_body_class');



/**
 * Remove emoji CDN hostname from DNS prefetching hints.
 *
 * @param array $urls URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array Difference betwen the two arrays.
 */
function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
 if ( 'dns-prefetch' == $relation_type ) {
 /** This filter is documented in wp-includes/formatting.php */
 $emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );

$urls = array_diff( $urls, array( $emoji_svg_url ) );
 }

return $urls;
}

// add_filter( 'wpcf7_validate_tel', 'fx_phone_validation', 10, 2 );
  
function fx_phone_validation( $result, $tag ) {
    $name = $tag->name;

    if ( 'tel' == $tag->type && ( isset( $_POST[$name] ) && ! empty( $_POST[$name] ) ) ) {
    
        $tel_number = preg_replace( "/[^0-9]/", "", $name );
  
        if ( strlen( $tel_number ) < 10 ) {
            $result->invalidate( $tag, "Invalid phone number." );
        }
    }
  
    return $result;
}

