<?php
/**
 * Plugin Name: WP CAT
 * Plugin URI: http://webpagefx.com
 * Description: Integrates CAT Equipment data
 * Version: 2.4.3
 * Author: WebpageFX
 * Author URI: http://webpagefx.com/
 *
 * Text Domain: catfeed
 *
 * @Package CAT New Feed
 * @category Core
 * @author WebpageFX
 */

$plugin_index = __DIR__ . '/wp-cat/wp-cat.php';
if( is_file( $plugin_index ) ) {
	require_once( $plugin_index );
}

/*
$allposts= get_posts( array('post_type'=>'accordion','numberposts'=>-1) );
foreach ($allposts as $eachpost) {
wp_delete_post( $eachpost->ID, true );
}
*/