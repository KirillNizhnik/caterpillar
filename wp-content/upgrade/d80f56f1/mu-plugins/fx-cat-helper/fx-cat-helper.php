<?php
/**
 * Plugin Name: FX CAT Helper Tool
 * Plugin URI: https://www.webfx.com
 * Description: Helper tool for debugging and extending the CAT plugin functionality.
 * Version: 1.0.0
 * Author: The WebFX Team
 * Author URI: https://www.webfx.com
 *
 * Text Domain: webfx
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Fx_Cat_Helper {

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * Plugin instance.
	 *
	 * @see  instance()
	 * @type object
	 */
	protected static $instance;

	/**
	 * Dealer code specific to cat dealer
	 *
	 * @var string
	 */
	public $dealer_code = '';

	/**
	 * Language code for XML requests
	 *
	 * @var string
	 */
	public $lang_code = 'en';

	/**
	 * Class IDs specific to CAT plugin settings - overrides must be coded in.
	 *
	 * @var array|string
	 */
	public $accessible_class_ids = [];

	/**
	 * CPC xml shared base for api endpoint.
	 *
	 * @var string
	 */
	public $cpc_xml_base = 'https://cpc.cat.com/api/v2/xml/';

	/**
	 * Instance of main plugin class used later for global function
	 *
	 * @return Fx_Cat_Helper|object
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Initiate the plugin
	 *
	 * @return void
	 */
	protected function __construct() {

		$this->dealer_code          = get_option( 'cat_new_sales_channel_code' );
		$this->accessible_class_ids = get_option( 'cat_new_class_limitation' );
		if ( ! defined( 'FX_CAT_HELPER_PLUGIN_FILE' ) ) {
			define( 'FX_CAT_HELPER_PLUGIN_FILE', __FILE__ );
		}
		$this->includes();

	}

	/**
	 * Check whether a cli command is being invoked to avoid errors.
	 *
	 * @return bool
	 */
	public function is_cli(): bool {
		return class_exists( 'WP_CLI' );
	}

	/**
	 * Includes dependency checks, shared functions, cli commands, and controllers needed for CAT functionality.
	 *
	 * @return void
	 */
	public function includes() {
		include_once plugin_dir_path( FX_CAT_HELPER_PLUGIN_FILE ) . 'includes/helpers/shared-functions.php';
		include_once plugin_dir_path( FX_CAT_HELPER_PLUGIN_FILE ) . 'includes/controllers/import-single-cpc.php';
		include_once plugin_dir_path( FX_CAT_HELPER_PLUGIN_FILE ) . 'includes/controllers/search-cpc-feed.php';
		include_once plugin_dir_path( FX_CAT_HELPER_PLUGIN_FILE ) . 'includes/controllers/search-dsf-feed.php';
		include_once plugin_dir_path( FX_CAT_HELPER_PLUGIN_FILE ) . 'includes/controllers/cpc-xmls.php';
		include_once plugin_dir_path( FX_CAT_HELPER_PLUGIN_FILE ) . 'includes/controllers/plugin-health.php';
		include_once plugin_dir_path( FX_CAT_HELPER_PLUGIN_FILE ) . 'includes/tools-page.php';

		include_once plugin_dir_path( FX_CAT_HELPER_PLUGIN_FILE ) . 'includes/cli/dsf-import.php';
		include_once plugin_dir_path( FX_CAT_HELPER_PLUGIN_FILE ) . 'includes/cli/cpc-import.php';
		include_once plugin_dir_path( FX_CAT_HELPER_PLUGIN_FILE ) . 'includes/cli/register-commands.php';

	}

}

/**
 * Returns main instance of class to avoid use of Globals.
 *
 * @return Fx_Cat_Helper|object
 */
function fx_cat_helper_tools() {
	return Fx_Cat_Helper::instance();
}

//instantiate the plugin
fx_cat_helper_tools();
