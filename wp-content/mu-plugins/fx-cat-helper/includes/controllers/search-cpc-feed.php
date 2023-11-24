<?php namespace Fx_Cat_Helper\Controllers;

/**
 * CLI Tool  and gui controller file for searching the cat feed.
 */

use WP_CLI;
use Fx_Cat_Helper\Controllers\Fx_Single_Cpc_Import;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Fx_Search_Cpc_Feed {

	/**
	 * Class instance.
	 *
	 * @see  instance()
	 * @type object
	 */
	protected static $instance;

	/**
	 * Static Singleton Factory Method
	 *
	 * @return Fx_Search_Cpc_Feed|object
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Set placeholder for target xml we'll tap into later
	 *
	 * @var string
	 */
	public $target_xml = '';

	/**
	 * Public placeholder for cpc id in question
	 *
	 * @var int
	 */
	public $target_id = 0;

	/**
	 * Public placeholder for cpc product name being searched.
	 *
	 * @var string
	 */
	public $target_name = '';

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
	 * Register AJAX call hooks
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'wp_ajax_get_xml_for_text_match', [ $this, 'get_xml_for_text_match' ] );
		add_action( 'wp_ajax_nopriv_get_xml_for_text_match', [ $this, 'get_xml_for_text_match' ] );
	}

	/**
	 * Main control function for finding a new cpc product based on text match
	 *
	 * @param $text
	 *
	 * @return void
	 */
	public function get_xml_for_text_match( $text = '' ) {
		$text = ! empty( $_POST['cat_cpc_search'] ) ? trim( $_POST['cat_cpc_search'] ) : $text;
		if ( empty( $text ) && ! fx_cat_helper_tools()->is_cli() ) {
			echo wp_json_encode( 'Please input a product name or ID to get started.' );
			wp_die();
		}
		$should_import                         = isset( $_POST['cat_import_if_found'] ) && $_POST['cat_import_if_found'] == 1 ? true : false;
		$this->gui_input                       = [
			'text'          => $text,
			'should_import' => $should_import,
		];
		$this->gui_output['standard_response'] = '';
		if ( is_numeric( $text ) ) {
			$this->target_id = $text;
		} else {
			$this->target_name = $text;
		}

		$this->target_xml = get_cpc_class_xml_for_text_match( $text ); //narrow down the xml

		if ( ! empty( $this->target_xml ) ) {
			if ( fx_cat_helper_tools()->is_cli() ) {
				WP_CLI::log( 'Parsing xml file for details ... ' );
			}
			$this->search_class( $this->target_xml ); // start process if we have a match
		} else { //nothing is looking like a match, exit early
			$this->not_found_msg();
		}
		exit;
	}

	/**
	 * Simple GUI or CLI return for text match not found
	 *
	 * @return void
	 */
	public function not_found_msg() {
		if ( fx_cat_helper_tools()->is_cli() ) {
			WP_CLI::warning( 'No text matches found. Please ensure the correct cpc id or product title is indicated or that the appropriate classes have been configured in the CAT plugin settings. ' );
		}
		echo wp_json_encode( 'No text matches found. Please ensure the correct cpc id or product title is indicated or that the appropriate classes have been configured in the CAT plugin settings. ' );
		wp_die();
	}


	/**
	 * Load XML to be parsed for family data
	 *
	 * Todo: refactoring
	 *
	 * @param $xml
	 * @param $id
	 *
	 * @return void
	 */
	public function search_class( $xml ) {
		$full_xml = simplexml_load_string( $xml );

		$xml = $full_xml->product_group;
		if ( ! empty( $this->target_id ) ) {
			$nodes        = $xml->xpath( '//product[@id="' . $this->target_id . '"]' );
			$product      = $nodes[0] ?? $nodes;
			$double_check = $this->final_product_checker( $product, (int) $this->target_id, $xml ); //run one last check - because some models can just be numbers
			if ( is_object( $double_check ) ) {
				$this->fetch_cpc_product_details( $double_check, (int) $double_check->attributes()->id[0] ); //original check was fine
			} elseif ( $double_check === true ) {
				$this->fetch_cpc_product_details( $product, (int) $this->target_id ); //needed to change to display name
			} else {
				$this->not_found_msg(); //input was invalid
			}
		}
		if ( ! empty( $this->target_name ) ) {
			$nodes   = $xml->xpath( '//product[nondisplayname[contains(.,"' . $this->target_name . '")]]' );
			$product = $nodes[0];
			$this->fetch_cpc_product_details( $product, (int) $product->attributes()->id[0] );
		}
	}

	/**
	 * Double check on the id search. Some model names can just be numbers so make sure xpath returned expect object.
	 * Returns attempt of nondisplaynamematch if not object. Returns false if neither worked.
	 *
	 * @param $product
	 * @param $id
	 * @param $xml
	 *
	 * @return bool|mixed
	 */
	public function final_product_checker( $product, $id, $xml ) {
		if ( ! isset( $product->nondisplayname ) ) {
			$nodes       = $xml->xpath( '//product[nondisplayname[contains(.,"' . $id . '")]]' );
			$new_product = $nodes[0];
			if ( isset( $new_product->nondisplayname ) ) {
				return $new_product;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}


	/**
	 * Fetch url for cpc product and return some highlight info
	 *
	 * @param $product
	 * @param $id
	 *
	 * @return void
	 */
	public function fetch_cpc_product_details( $product, $id ) {

		if ( fx_cat_helper_tools()->is_cli() ) {
			WP_CLI::log( 'Here is what I was able to dig up on your product from the xml feed in the form of a grand var dump: ' );
			var_dump( $product );
			WP_CLI::confirm( 'Would you like me to check if this product has been imported and exists on this site?' );
			$this->maybe_import_product( $id );
		} else {
			$this->gui_output['standard_response'] .= 'Product with an ID of ' . $id . ' has been found on the CPC feed.<br> Here are the product details from the feed:';
			$this->gui_output['standard_response'] .= '<br><br>Non-display name: ' . $product->nondisplayname;
			$this->gui_output['standard_response'] .= '<br>Last Modified: ' . $product->last_modified;
			$this->gui_output['standard_response'] .= '<br>Status: ' . $product->status;
			$this->maybe_import_product( $id );
		}

	}

	/**
	 * See if product exists as post in current wp database
	 *
	 * @param $product_id
	 *
	 * @return void
	 */
	public function maybe_import_product( $product_id ) {
		$post_id = fx_fetch_post_by_equip_id( $product_id );

		if ( empty( $post_id ) ) { //no post found
			if ( fx_cat_helper_tools()->is_cli() ) {
				WP_CLI::warning( 'No posts found in the database with this matching equipment id. ' );
				WP_CLI::confirm( 'Would you like me to import the product in question?' ); //give cli the option - not default for testing purposes
				$import_instance = Fx_Single_Cpc_Import::instance();
				$import_instance->attempt_to_import( $product_id, '' );
			} else {
				if ( $this->gui_input['should_import'] ) { //gui no post found
					$import_instance = Fx_Single_Cpc_Import::instance();
					$import_instance->attempt_to_import( $product_id, '' );
					$post_id = fx_fetch_post_by_equip_id( $product_id );
					$this->append_post_details_to_response( $post_id );

					echo wp_json_encode( $this->gui_output );
					wp_die();
				} else { //gui no post found and import not selected
					$this->gui_output['standard_response'] .= '<br><br>This product has NOT yet been imported to this WP site.';
					echo wp_json_encode( $this->gui_output );
					wp_die();
				}
			}
		} else { //product was already on site
			if ( fx_cat_helper_tools()->is_cli() ) {
				WP_CLI::success( 'A post with this matching equipment id has been found on this wp site. Here are some details on the post/product:' );
				WP_CLI::log( 'Title: ' . get_the_title( $post_id ) );
				WP_CLI::log( 'Post ID: ' . $post_id );
				WP_CLI::log( 'Permalink: ' . get_permalink( $post_id ) );
				cat_cli_goodbye_msg();
			} else { //set basic and advanced details for gui
				$this->append_post_details_to_response( $post_id );
				global $wp_query;
				$product = CAT()->product( $post_id );
				ob_start();
				var_dump( $product );
				$contents = ob_get_contents();
				ob_end_clean();
				$this->gui_output['advanced_response'] = (string) $contents; //string it so it comes out in a "dump"
				echo wp_json_encode( $this->gui_output );
				wp_die();
			}
		}
	}

	/**
	 * Re-usable post details set for gui
	 *
	 * @param $post_id
	 *
	 * @return void
	 */
	public function append_post_details_to_response( $post_id ) {
		$this->gui_output['standard_response'] .= '<br><br>The database has been searched and this product HAS been imported and exists on  this WP site. Here are some details:';
		$this->gui_output['standard_response'] .= '<br>Title: ' . get_the_title( $post_id );
		$this->gui_output['standard_response'] .= '<br>Post ID: ' . $post_id;
		$this->gui_output['standard_response'] .= '<br>Permalink: <a href="' . get_permalink( $post_id ) . '"> ' . get_permalink( $post_id ) . '</a>';
	}
}
Fx_Search_Cpc_Feed::instance();
