<?php namespace Fx_Cat_Helper\Controllers;

/**
 * Controller file for plugin health gui - mostly hooks
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Fx_Cat_Helper_Plugin_Health {

	/**
	 * Class instance.
	 *
	 * @see  instance()
	 * @type object
	 */
	protected static $instance;

	/**
	 * Adds hooks for ajax and time stamp updates
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'cat_before_new_class_import', [ $this, 'update_new_import_start_time' ] );
		add_action( 'cat_after_new_class_import', [ $this, 'update_new_import_end_time' ] );
		add_action( 'cat_before_used_feed_import', [ $this, 'update_used_import_start_time' ] );
		add_action( 'cat_after_used_feed_import', [ $this, 'update_used_import_end_time' ] );
		add_action( 'wp_ajax_test_new_cpc_authentication', [ $this, 'test_new_cpc_authentication' ] );
		add_action( 'wp_ajax_test_new_cpc_authentication', [ $this, 'test_new_cpc_authentication' ] );
	}

	/**
	 * Static Singleton Factory Method
	 *
	 * @return Fx_Cat_Helper_Plugin_Health|object
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Sets start time stamp of new import process - based on id from hook in CAT plugin
	 *
	 * @param $class_id
	 *
	 * @return void
	 */
	public function update_new_import_start_time( $class_id ) {
		update_option( 'fx_new_' . $class_id . '_import_start_time', current_time( 'mysql' ) );
	}

	/**
	 * Sets end time stamp of new import process - based on id from hook in CAT plugin
	 *
	 * @param $class_id
	 *
	 * @return void
	 */
	public function update_new_import_end_time( $class_id ) {
		update_option( 'fx_new_' . $class_id . '_import_end_time', current_time( 'mysql' ) );
	}

	/**
	 * Sets start time stamp of used import process
	 *
	 * @return void
	 */
	public function update_used_import_start_time() {
		update_option( 'fx_used_import_start_time', current_time( 'mysql' ) );
	}

	/**
	 * Sets end time stamp of used import process
	 *
	 * @return void
	 */
	public function update_used_import_end_time() {
		update_option( 'fx_used_import_end_time', current_time( 'mysql' ) );
	}

	/**
	 * GUI Ajax call for testing if the site credentials correctly can connect to the new/cpc feed
	 *
	 * @return void
	 */
	public function test_new_cpc_authentication() {
		$class_id = array_key_first( CAT()->available_classes );
		$url      = fx_cat_helper_tools()->cpc_xml_base . fx_cat_helper_tools()->dealer_code . '/' . $class_id . 'tree_' . fx_cat_helper_tools()->lang_code . '.xml';
		$xml      = simplexml_load_string( fx_get_cpc_xml( $url ) );
		if ( isset( $xml->context->error->description ) || ! is_null( $xml->context->error->description ) ) {  //first check if credentials were bad
			echo wp_json_encode(
				'<i style="color:red;">Credentials denied!</i> The configured credentials were not able to pass authentication.<br>
	         Please ensure these are the ones sent from CAT:<br>' . CAT()->fetch_sales_channel_code_user() . ':' . CAT()->fetch_api_auth_secret_key() .
				'<br>Then recongifured in the <a target="_blank" href="' . get_site_url() . '/wp-admin/options-general.php?page=cat-settings&tab=feeds"> plugin settings</a> as needed.'
			);
		} elseif ( isset( $xml->product_group ) ) { //do a quick text match if they are good
			echo wp_json_encode( '<i style="color:green;">Success!</i> The configured credentials were able to make a request to the cpc feed.<br>' );
		} else { //then default to code error if neither matches up
			echo wp_json_encode(
				'Something went wrong trying to make this request. Sometimes the CAT feed urls can time out, so it is suggested to try again if the response seemed delay.<br>
	         If this happens multiple times, please contact your development team for a further investigation.'
			);
		}
		wp_die();
	}
}
Fx_Cat_Helper_Plugin_Health::instance();
