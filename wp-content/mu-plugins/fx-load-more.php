<?php

/**
 * 	Plugin Name:	FX Load More
 * 	Plugin URI: 	https://www.webfx.com
 * 	Description:	Adds "load more" functionality to blog and search results
 * 	Version: 		1.0.0
 * 	Author: 		The WebFX Team
 * 	Author URI: 	https://www.webfx.com
 * 	Text Domain: 	webfx
 */

$plugin_index = __DIR__ . '/fx-load-more/index.php';
if( is_file( $plugin_index ) ) {
	require_once( $plugin_index );
}