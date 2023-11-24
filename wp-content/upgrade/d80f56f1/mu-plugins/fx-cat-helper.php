<?php
/**
 * Plugin Name: WP CAT Helper Tool
 * Plugin URI: https://www.webfx.com
 * Description: Helper tool for debugging and extending the CAT plugin functionality.
 * Version: 0.1
 * Author: The WebFX Team
 * Author URI: https://www.webfx.com
 *
 * Text Domain: webfx
 */


$plugin_index = __DIR__ . '/fx-cat-helper/fx-cat-helper.php';
if ( is_file( $plugin_index ) ) {
	include $plugin_index;
}
