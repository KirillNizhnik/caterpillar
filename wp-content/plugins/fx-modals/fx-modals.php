<?php
/**
 * Plugin Name: FX Modals
 * Plugin URI: https://www.webfx.com
 * Description: Easily create customizable modals
 * Version: 0.1
 * Author: Collin Starr
 * Author URI: https://www.webfx.com
 *
 * Text Domain: webfx
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MY_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

final class FX_Modals {

	public static $plugin_path = null;
	public static $plugin_url  = null;
	protected static $instance = null;

	/**
	 * Static Singleton Factory Method
	 *
	 * @return self returns a single instance of our class instance
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			$class_name     = __CLASS__;
			self::$instance = new $class_name();
		}
		return self::$instance;
	}

	/**
	 * Initializes plugin variables and sets up WordPress hooks/actions.
	 *
	 * @return void
	 */
	protected function __construct() {
		$full_file_path    = realpath( __FILE__ );
		self::$plugin_path = dirname( $full_file_path );
		self::$plugin_url  = plugin_dir_url( $full_file_path );

		$this->includes();

	}

	/**
	 * Grab any files we might need later
	 *
	 * @return void
	 */
	protected function includes() {
        include_once self::$plugin_path . '/includes/custom_post_type.php';
        include_once self::$plugin_path . '/includes/modal_shortcode.php';
        include_once self::$plugin_path . '/includes/modal_block.php';
    }
}
function FX_Modals() {
	return FX_Modals::instance();
}

FX_Modals();
