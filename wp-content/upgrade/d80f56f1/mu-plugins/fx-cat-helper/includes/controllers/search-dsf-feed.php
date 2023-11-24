<?php namespace Fx_Cat_Helper\Controllers;

/**
 * Gui controller file for searching the cat feed.
 * CLI not yet added because used XMLs don't require an authentication request.
 */

use \WP_Error;
use \Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Fx_Search_Dsf_Feed {

	/**
	 * Class instance.
	 *
	 * @see  instance()
	 * @type object
	 */
	protected static $instance;

	/**
	 * Organize GUI input so it's easier to parse and utilize later.
	 *
	 * @var array
	 */
	public $gui_input = [];

	/**
	 * Organize GUI output so that it can be parsed on the ajax side of things.
	 *
	 * @var array
	 */
	public $gui_output = [];

	/**
	 * USed CAT source XML
	 *
	 * @var false|mixed|void
	 */
	public $used_url;

	/**
	 * Static Singleton Factory Method
	 *
	 * @return Fx_Search_Dsf_Feed|object
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Set up option for used url, start our standard response, and register ajax hooks
	 *
	 * @return void
	 */
	public function __construct() {
		$this->used_url                        = get_option( 'cat_used_feed_url' );
		$this->gui_output['standard_response'] = '';
		add_action( 'wp_ajax_chp_search_used', [ $this, 'chp_search_used' ] );
		add_action( 'wp_ajax_nopriv_chp_search_used', [ $this, 'chp_search_used' ] );

	}

	/**
	 * Main function for a used feed text based search
	 * TODO: refactor and add other search parameters/options
	 * @param $text
	 * @param $feed_url
	 *
	 * @return void
	 */
	public function chp_search_used( $text, $feed_url = '' ) {
		$feed_url     = empty( $feed_url ) ? $this->used_url : $feed_url;
		$xml_response = $this->fetch_dsf_xml( $feed_url );
		if ( is_wp_error( $xml_response ) ) { // xml not set or timeout most likely
			$this->gui_output['standard_response'] = 'The cat source configured in the plugin at: ' . $this->used_url . " did not load for us today. This indicates: request timeout(if so you should try again), the source being down(viewing in browser should show if anything is off), or an issue with the code. Probably can't hurt to try again.";
			echo wp_json_encode( $this->gui_output );
			wp_die();
		}
		$xml_loaded              = simplexml_load_string( $xml_response );
		$xml                     = $xml_loaded;
		$this->gui_input['text'] = ! empty( $_POST['cat_dsf_search'] ) ? trim( $_POST['cat_dsf_search'] ) : $text;

		if ( is_numeric( $this->gui_input['text'] ) ) { //guesstimate id at first
			$nodes = $xml->xpath( '//equipment[@id="' . $this->gui_input['text'] . '"]' );
			if ( empty( $nodes ) ) { //double check for a model name - since they can be just numbers as well - override if a match
				$nodes = $xml->xpath( '//equipment[@model[contains(.,"' . $this->gui_input['text'] . '")]]' );
			}
		} else { //go for serial number if not numeric
			$nodes = $xml->xpath( '//equipment[@serial-number="' . $this->gui_input['text'] . '"]' );
		}
		if ( is_null( $nodes ) ) {
			$product = null;
		} else {
			$product = $nodes[0];
		}

		if ( is_null( $product ) ) {
			$this->send_failure_msg();
		} else {
			$this->send_product_feed_details( $product );
			$this->send_product_site_details( $product );

		}
		echo wp_json_encode( $this->gui_output );
		wp_die();

	}

	/**
	 * Custom request for used feed - set a bit higher request timeout because their sources take a while to load (esp if it has a query string)
	 *  TODO: move to global - refactor with used url
	 *
	 * @param $url
	 *
	 * @return string|WP_Error
	 */
	public function fetch_dsf_xml( $url ) {
		set_time_limit( 900 );
		$xml = false;
		try {
			$response = wp_remote_request( //The auth is only used when calling this function which is only used in the new feeds.
				$url,
				[
					'method' => 'GET',
				]
			);
			$raw      = wp_remote_retrieve_body( $response );
			$xml      = $raw;
		} catch ( Exception $e ) {
			return new WP_Error( 'XML Failed', __( $e ) );
		}
		if ( ! $xml ) {
			return new WP_Error( 'XML Failed', __( 'Unable to load XML file - there may be a syntax error' ) );
		}
		return $xml;
	}

	/**
	 * Simple failure message and ajax closeout for gui
	 *
	 * @return void
	 */
	public function send_failure_msg() {
		$this->gui_output['standard_response'] .= 'No products were found for the search of "' . $this->gui_input['text'] . '" provided. You can double check what products are on the feed here: <a href="' . get_option( 'cat_used_feed_url' ) . ' ">' . get_option( 'cat_used_feed_url' ) . '<a/>';
		echo wp_json_encode( $this->gui_output );
		wp_die();
	}

	/**
	 * Pull all the attributes from the product node on set xml for the gui
	 *
	 * @param $product
	 *
	 * @return void
	 */
	public function send_product_feed_details( $product ) {
		$product_attrs                          = $product->attributes();
		$this->gui_output['standard_response'] .= 'Success! Product has been identified off of the dsf feed at: ' . get_option( 'cat_used_feed_url' );
		$this->gui_output['standard_response'] .= '<br> Here are some of the product details:<br>';
		foreach ( $product_attrs as $attr_key => $attr_value ) {
			$this->gui_output['standard_response'] .= ucfirst( $attr_key ) . ': ' . $attr_value . '<br>';
		}

	}

	/**
	 * Pull all WP specific data from found product if available and return status
	 * TODO: add single product import functionality
	 *
	 * @param $product
	 *
	 * @return void
	 */
	public function send_product_site_details( $product ) {
		$product_attrs = $product->attributes();
		$post_id       = fx_fetch_post_by_equip_id( $product_attrs->id[0] );
		if ( empty( $post_id ) ) {
			$this->gui_output['standard_response'] .= '<br><br>This product has NOT yet been imported to this WP site.';
			$this->gui_output['standard_response'] .= '<br>Import the product by navigating to the importer tab of the WP CAT plugin here: ' . get_site_url() . '/wp-admin/options-general.php?page=cat-settings'; //present option to import
		} else {
			$this->gui_output['standard_response'] .= '<br><br>The database has been searched and this product HAS been imported and exists on this WP site. Here are some details:';
			$this->gui_output['standard_response'] .= '<br>Title: ' . get_the_title( $post_id );
			$this->gui_output['standard_response'] .= '<br>Post ID: ' . $post_id;
			$this->gui_output['standard_response'] .= '<br>Permalink: <a href="' . get_permalink( $post_id ) . '"> ' . get_permalink( $post_id ) . '</a>';
			$this->send_advanced_response( $post_id );
		}
	}

	/**
	 * Pull the full CAT object for an advanced/full response
	 *
	 * @param $product_id
	 *
	 * @return void
	 */
	public function send_advanced_response( $product_id ) {
		global $wp_query;
		$product = CAT()->product( $product_id );
		ob_start();
		var_dump( $product );
		$contents = ob_get_contents();
		ob_end_clean();
		$this->gui_output['advanced_response'] = (string) $contents;
	}
}

Fx_Search_Dsf_Feed::instance();
