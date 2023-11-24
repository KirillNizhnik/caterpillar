<?php namespace Fx_Cat_Helper\Controllers;

use Cat\Controllers\Importers\New_Feed;
use Cat\Models\CPC_Class;
use WP_CLI;

/**
 *     CLI Tool for importing single cpc product - used in tandum with cpc search in gui
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Fx_Single_Cpc_Import {

	/**
	 * Public placeholder for when xml is identified.
	 *
	 * @var string
	 */
	public $target_class_xml = '';

	/**
	 * Public placeholder for storing target class id.
	 *
	 * @var int
	 */
	public $target_class_id = 0;

	/**
	 * Public placeholder for cpc id of product.
	 *
	 * @var int
	 */
	public $target_cpc_id = 0;

	/**
	 * Public placeholder on target product post type.
	 *
	 * @var string
	 */
	public $target_post_type = '';

	/**
	 * Class instance.
	 *
	 * @see  instance()
	 * @type object
	 */
	protected static $instance;

	/**
	 * Blank for now - ideas from notes
	 *
	 * @return void
	 */
	public function __construct() {}

	/**
	 * Easy instance return for reusability.
	 *
	 * @return object
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Main import function for process - start here for re-using for import.
	 *
	 * @param $product_id
	 * @param $class_id
	 *
	 * @return void
	 */
	public function attempt_to_import( $product_id, $class_id = '' ) {
		$response = get_cpc_class_xml_for_text_match( $product_id, $class_id ); //utilize global helper
		if ( ! empty( $response ) ) { //set up details
			$loaded_xml             = simplexml_load_string( $response );
			$this->target_class_xml = $response;
			$this->target_cpc_id    = $product_id;
			$this->target_class_id  = (string) $loaded_xml->product_group->attributes()->id[0];
		}

		if ( ! empty( $this->target_class_xml ) ) { //now decide where to send it to
			if ( fx_cat_helper_tools()->is_cli() ) {
				WP_CLI::log( 'Parsing xml file for details on product to confirm ... ' );
			}
			$this->process_class(); // link to actual import next
		} else {
			if ( fx_cat_helper_tools()->is_cli() ) { //only need to account for cli response as of now
				WP_CLI::warning( 'No text matches found for cpc id. Please ensure the correct cpc id is indicated or that the appropriate classes have been setup. To locally search for your id, run wp fx_localize_cat_urls ' );
			}
		}
	}

	/**
	 * Process class, instantiate class object reference for later usage, and send off for import
	 *
	 * @return void
	 */
	public function process_class() {
		$full_xml               = simplexml_load_string( $this->target_class_xml );
		$class                  = new CPC_Class( $full_xml->product_group );
		$class->ID              = $this->target_class_id;
		$class->post_type       = CAT()->get_class_post_type_relation( $this->target_class_id );
		$this->target_post_type = CAT()->get_class_post_type_relation( $this->target_class_id );
		if ( fx_cat_helper_tools()->is_cli() ) {
			WP_CLI::log( 'Breaking down family levels of xml ... ' );
		}
		$product_object = $this->product_exists( $class );
		if ( ! empty( $product_object ) ) {
			if ( fx_cat_helper_tools()->is_cli() ) {
				WP_CLI::log( WP_CLI::colorize( '%GProduct ID has a match with a product from the feed!%n ' ) );
				WP_CLI::log( 'Initiating import process ...' );
			}
			$new_feed = New_Feed::instance();
			$this->populate_single_product_families( $product_object, $class, $new_feed );
			if ( fx_cat_helper_tools()->is_cli() ) {
				WP_CLI::log( 'Families have been found and set! Importing post now.' );
			}
			$this->import_product( $product_object, $class->post_type, $new_feed );
		} else {
			if ( fx_cat_helper_tools()->is_cli() ) {
				WP_CLI::error( 'Matching product ID not found. Please ensure proper CPC Id is given.' );
			}
		}
	}

	/**
	 * Find matching cpc id from class xml, if none found return empty
	 *
	 * @param $class
	 *
	 * @return mixed|string
	 */
	public function product_exists( $class ) {
		foreach ( $class->products as $p ) {
			if ( $p['id'] == $this->target_cpc_id ) {
				return $p;
			} else {
				continue;
			}
		}
		return '';
	}

	/**
	 * Process the targets families and set them in pre-defined instance of cpc import
	 *
	 * @param $product
	 * @param $class
	 * @param $feed_instance
	 *
	 * @return void
	 */
	public function populate_single_product_families( $product, $class, $feed_instance ) {
		$sub_fam_id = $product['subfamily_id'];
		$fam_id     = $product['family_id'];
		foreach ( $class->families as $f ) {
			if ( $f['id'] === $sub_fam_id ) {
				if ( fx_cat_helper_tools()->is_cli() ) {
					WP_CLI::log( WP_CLI::colorize( '%GSubfamily found!%n Processing now... ' ) );
				}
				$feed_instance->family( $f, $this->target_class_id, $this->target_post_type, false );
			}
			if ( $f['id'] === $fam_id ) {
				if ( fx_cat_helper_tools()->is_cli() ) {
					WP_CLI::log( WP_CLI::colorize( '%GMain family found!%n Processing now... ' ) );
				}
					$feed_instance->family( $f, $this->target_class_id, $this->target_post_type, false );
			}
		}
	}

	/**
	 * Action of the import and return permalink for user convenience
	 *
	 * @param $product
	 * @param $post_type
	 * @param $feed_instance
	 *
	 * @return void
	 */
	public function import_product( $product, $post_type, $feed_instance ) {
		$feed_instance->product( $product, $post_type );
		if ( fx_cat_helper_tools()->is_cli() ) {
			WP_CLI::success( 'Product has been imported!' );
		}
		$post_id = fx_fetch_post_by_equip_id( $product['id'] );
		if ( fx_cat_helper_tools()->is_cli() ) {
			WP_CLI::success( 'Here are some details on the post/product:' );

			WP_CLI::log( 'Title: ' . get_the_title( $post_id ) );
			WP_CLI::log( 'ID: ' . $post_id );
			WP_CLI::log( 'Permalink: ' . get_permalink( $post_id ) );
			cat_cli_goodbye_msg();
		}
	}
}
Fx_Single_Cpc_Import::instance(); //insantiate the class
