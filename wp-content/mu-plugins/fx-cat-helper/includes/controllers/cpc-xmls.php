<?php namespace Fx_Cat_Helper\Controllers;

/**
 * Controller file for cloning CPC/New feed xmls - used in cli command and wp gui
 */

use Cat\Models\CPC_Class;
use Exception;
use WP_Error;
use WP_CLI;
use function WP_CLI\Utils\make_progress_bar;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Fx_Clone_Cpc_Xmls {

	/**
	 * Plugin instance.
	 *
	 * @see  instance()
	 * @type object
	 */
	protected static $instance;

	/**
	 * For setting a definitive wp uploads location
	 *
	 * @var array|string
	 */
	public $uploads_dir = '';

	/**
	 * First level of cat data to localize.
	 *
	 * @var string
	 */
	public static $home_dir = '';

	/**
	 * Directory for storing class xml copies
	 *
	 * @var string
	 */
	public static $class_dir = '';

	/**
	 * Directory for storing family xml copies
	 *
	 * @var string
	 */
	public static $families_dir = '';

	/**
	 * Directory for storing product xml copies
	 *
	 * @var string
	 */
	public static $product_dir = '';

	/**
	 * HTML file home if chose
	 *
	 * @var string
	 */
	public static $feed_display_dir = '';


	/**
	 * URL for handy linking later.
	 *
	 * @var string
	 */
	public $uploads_url = '';

	/**
	 * WP Progress bar for setting and unsetting conditionally.
	 *
	 * @var object
	 */
	public $progress_bar;

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
	 * Run dependency check and set data needed.
	 *
	 * @return void
	 */
	public function __construct() {
		fx_cpc_dependency_checker(); //run dependency check first

		$this->uploads_dir      = wp_upload_dir(); //set our paths and other necessary data
		self::$home_dir         = $this->uploads_dir['basedir'] . '/local-cat-data';
		self::$class_dir        = self::$home_dir . '/class';
		self::$families_dir     = self::$home_dir . '/family';
		self::$product_dir      = self::$home_dir . '/product';
		self::$feed_display_dir = $this->uploads_dir['basedir'] . '/local-cat-display/hierarchy';
		$this->uploads_url      = content_url() . '/uploads/local-cat-data/';

		add_action( 'wp_ajax_clone_new_xmls', [ $this, 'clone_new_xmls' ] ); //setup ajax calls
		add_action( 'wp_ajax_nopriv_clone_new_xmls', [ $this, 'clone_new_xmls' ] );
		add_action( 'clone_cpc_feed_xmls', [ $this, 'clone_new_xmls' ] ); //setup cron call
	}

	/**
	 * Static Singleton Factory Method
	 *
	 * @return Fx_Clone_Cpc_Xmls|object
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * From user input, send off decisions for cloning cpc data based on cli, cron or ajax
	 *
	 * @param $args
	 * @param $assoc_args
	 *
	 * @return void
	 */
	public function clone_new_xmls( $args = '', $assoc_args = '' ) {
		if ( isset( $assoc_args['cleanup'] ) || ( isset( $_POST['user-action'] ) && $_POST['user-action'] === 'delete' ) ) { //wp fx_cat clone_cpc --cleanup
			$this->cleanup( $this->uploads_dir['basedir'] . '/local-cat-data/' );
			if ( fx_cat_helper_tools()->is_cli() ) {
				return;
			} else {
				echo wp_json_encode( $this->gui_output );
				wp_die();
			}
		}
		update_option( 'fx_cpc_xmls_cloned_status', 'In Progress' ); //update our option here for user tracking since the process can be slower
		if ( fx_cat_helper_tools()->is_cli() ) {
			WP_CLI::log( 'Localizing XMLs... ' );
		}
		$this->create_xml_home(); //create homes for xml files
		$this->init_html_display_file(); //create the html for writing to if not already available

		if ( isset( $assoc_args['class'] ) && in_array( $assoc_args['class'], fx_cat_helper_tools()->accessible_class_ids, true ) ) {
			$this->fetch_xmls( $assoc_args['class'] ); //straight for the process if they specified a class
		} else { //otherwise, loop through our classes
			if ( fx_cat_helper_tools()->is_cli() ) {
				$this->progress_bar = make_progress_bar( 'Processing Classes', count( fx_cat_helper_tools()->accessible_class_ids ) );
			}
			foreach ( fx_cat_helper_tools()->accessible_class_ids as $class ) {
				$this->fetch_xmls( $class );
				if ( fx_cat_helper_tools()->is_cli() ) {
					$this->progress_bar->tick();
				}
			}
			if ( fx_cat_helper_tools()->is_cli() ) {
				$this->progress_bar->finish();
			}
		}
		if ( fx_cat_helper_tools()->is_cli() ) {
			$this->get_cli_success_msg();
		}
		update_option( 'fx_cpc_xmls_cloned_time', current_time( 'mysql' ) ); //update option for time synced
		update_option( 'fx_cpc_xmls_cloned_status', 'Complete' ); //and update with a message showing the process didn't error out trying to complete

	}


	/**
	 * Handy cleanup functionality for removing what was localized.
	 *
	 * @return void
	 */
	public function cleanup( $dir_path ) {
		if ( is_dir( $dir_path ) ) {
			if ( substr( $dir_path, strlen( $dir_path ) - 1, 1 ) !== '/' ) {
				$dir_path .= '/';
			}
			$files = glob( $dir_path . '*', GLOB_MARK );
			foreach ( $files as $file ) {
				if ( is_dir( $file ) ) {
					self::cleanup( $file );
				} else {
					if ( fx_cat_helper_tools()->is_cli() ) {
						WP_CLI::success( $file . ' has been removed.' );
					}
					unlink( $file );
				}
			}
			rmdir( $dir_path );
		} else {
			if ( fx_cat_helper_tools()->is_cli() ) {
				WP_CLI::warning( 'Nothing to clean up. Have a nice day!' );
			} else {
				$this->gui_output['delete'] = 'Nothing to clean up. Have a nice day!';
			}
		}

		$this->init_html_display_file(); //setup our html file once again if needed

		file_put_contents( self::$feed_display_dir . '/feed-list.html', '<br>No XMLs currently cloned to this WP site. <br>', FILE_APPEND ); //set this up with a message so the GUI doesn't look as awkward
		update_option( 'fx_cpc_xmls_cloned_status', 'Empty' );
		$this->gui_output['delete'] = "The XMLs have been purged from this site's uploads directory.";
	}

	/**
	 * Simple directory setter for later
	 *
	 * @return void
	 */
	public function create_xml_home() {
		if ( ! file_exists( self::$home_dir ) ) {
			wp_mkdir_p( self::$home_dir );
		}
		if ( ! file_exists( self::$class_dir ) ) {
			wp_mkdir_p( self::$class_dir );
		}
		if ( ! file_exists( self::$families_dir ) ) {
			wp_mkdir_p( self::$families_dir );
		}
		if ( ! file_exists( self::$product_dir ) ) {
			wp_mkdir_p( self::$product_dir );
		}
	}

	/**
	 * Simple directory and html file setter for later usage
	 *
	 * @return void
	 */
	public function init_html_display_file() {
		if ( ! file_exists( self::$feed_display_dir ) ) {
			wp_mkdir_p( self::$feed_display_dir );
		}
		file_put_contents( self::$feed_display_dir . '/feed-list.html', '<h1>CAT FEED XML Listing:</h1><br/><br/>' );
	}

	/**
	 * Start the process, run localize functions
	 *
	 * @param string $class_id
	 *
	 * @return void
	 */
	public function fetch_xmls( $class_id = '' ) {
		$class = $this->class_tree( $class_id );
		$this->families( $class );
		$this->products( $class );
	}

	/**
	 * Set the cpc class xml in local data and set up the class data for other functions to parse
	 *
	 * @param $class_id
	 *
	 * @return array|string|WP_Error
	 */
	public function class_tree( $class_id ) {
		$url = fx_cat_helper_tools()->cpc_xml_base . fx_cat_helper_tools()->dealer_code . '/' . $class_id . 'tree_' . fx_cat_helper_tools()->lang_code . '.xml'; // add _nc before .xml to show non-current equipment as well - leaving this to be custom per dev as it is not popular.
		$xml = fx_get_cpc_xml( $url );
		if ( is_wp_error( $xml ) ) {
			return $xml;
		}
		$this->localize_this_xml( $xml, 'class', fx_cat_helper_tools()->dealer_code . '_' . $class_id . '_tree_' . fx_cat_helper_tools()->lang_code . '.xml' ); //save it
		$loaded_xml       = simplexml_load_string( $xml ); //ride off plugin usage and cpc class for easier iteration of family and products later.
		$class            = new CPC_Class( $loaded_xml->product_group );
		$class->ID        = $class_id;
		$class->post_type = CAT()->get_class_post_type_relation( $class_id );
		if ( fx_cat_helper_tools()->is_cli() ) {
			WP_CLI::log( 'Processing complete for ' . $class_id . ' class tree' );
		}
		$saved_url = $this->uploads_url . 'class/' . fx_cat_helper_tools()->dealer_code . '_' . $class_id . '_tree_' . fx_cat_helper_tools()->lang_code . '.xml';
		file_put_contents( self::$feed_display_dir . '/feed-list.html', '<h3>Class:</h3><br><a target="_blank" href="' . $saved_url . '">' . $saved_url . '</a>', FILE_APPEND );

		return $class;
	}

	/**
	 * Simple function to store xml data accordingly
	 *
	 * @param $xml
	 * @param $designation
	 * @param $name
	 *
	 * @return void
	 */
	public static function localize_this_xml( $xml, $designation, $name ) {

		if ( $designation === 'class' ) {
			file_put_contents( self::$class_dir . '/' . $name, $xml );
		} elseif ( $designation === 'family' ) {
			file_put_contents( self::$families_dir . '/' . $name, $xml );
		} elseif ( $designation === 'product' ) {
			file_put_contents( self::$product_dir . '/' . $name, $xml );
		} else {
			if ( fx_cat_helper_tools()->is_cli() ) {
				WP_CLI::error( "Something went wrong leading up to the localization of the files. At this point if you haven't seen an error leading up to this message, there is likely either a permissions issue for writing to the uploads file or the contents of the xml files are being fetch improperly. " );
			}
			return;
		}
	}

	/**
	 * Loop through family xml files
	 *
	 * @param $class
	 *
	 * @return void
	 */
	public function families( $class ) {
		file_put_contents( self::$feed_display_dir . '/feed-list.html', '<br><h3>Families:</h3>', FILE_APPEND );
		if ( fx_cat_helper_tools()->is_cli() ) {
			$progress = make_progress_bar( 'Processing Families... ', count( $class->families ) );
		}
		foreach ( $class->families as $f ) {
			$this->family( $f, $class->ID, $class->post_type );
			if ( fx_cat_helper_tools()->is_cli() ) {
				$progress->tick();
			}
		}
		if ( fx_cat_helper_tools()->is_cli() ) {
			$progress->finish();
			WP_CLI::log( 'Processing complete for ' . $class->ID . ' tree families.' );
			WP_CLI::log( 'Now processing products...' );
		}
	}

	/**
	 * Localize single family XML file
	 *
	 * @param $f
	 * @param $class_id
	 * @param $post_type
	 * @param $rental
	 *
	 * @return WP_Error|void
	 */
	public function family( $f, $class_id, $post_type, $rental = false ) {
		$url = fx_cat_helper_tools()->cpc_xml_base . fx_cat_helper_tools()->lang_code . '/' . $f['id'] . '/' . $f['id'] . '_' . fx_cat_helper_tools()->lang_code . '.xml';
		$xml = fx_get_cpc_xml( $url );
		if ( is_wp_error( $xml ) ) {
			return $xml;
		}
		$this->localize_this_xml( $xml, 'family', $class_id . '-' . $f['id'] . '-' . $post_type . '.xml' ); //save it
		$saved_url = $this->uploads_url . 'family/' . $class_id . '-' . $f['id'] . '-' . $post_type . '.xml';
		file_put_contents( self::$feed_display_dir . '/feed-list.html', '<a target="_blank" href="' . $saved_url . '">' . $saved_url . '</a><br>', FILE_APPEND );

	}

	/**
	 * Loop through products from accepted class data
	 *
	 * @param $class
	 *
	 * @return void
	 */
	public function products( $class ) {
		file_put_contents( self::$feed_display_dir . '/feed-list.html', '<br><h3>Products:</h3>', FILE_APPEND ); //just title and space
		if ( fx_cat_helper_tools()->is_cli() ) {
			$progress = make_progress_bar( 'Processing Products... ', count( $class->products ) );
		}
		foreach ( $class->products as $p ) {
			$this->product( $p, $class->post_type );
			if ( fx_cat_helper_tools()->is_cli() ) {
				$progress->tick();
			}
		}

		if ( fx_cat_helper_tools()->is_cli() ) {
			$progress->finish();
			WP_CLI::log( 'Processing complete for products from ' . $class->ID . ' tree.' );
			WP_CLI::log( 'Now finalizing procedure...' );
		}
	}

	/**
	 * Localize single product xml file
	 *
	 * @param $p
	 * @param $post_type
	 * @param $rental
	 *
	 * @return WP_Error|void
	 */
	public function product( $p, $post_type, $rental = false ) {
		$url = fx_cat_helper_tools()->cpc_xml_base . fx_cat_helper_tools()->lang_code . '/' . $p['subfamily_id'] . '/' . $p['id'] . '_' . fx_cat_helper_tools()->lang_code . '.xml';
		$xml = fx_get_cpc_xml( $url );
		if ( is_wp_error( $xml ) ) {
			return new WP_Error( '<pre>' . print_r( $xml, true ) . '</pre>' );
		}
		$this->localize_this_xml( $xml, 'product', $p['subfamily_id'] . '-' . $p['id'] . '-' . $post_type . '.xml' ); //save it
		$saved_url = $this->uploads_url . 'product/' . $p['subfamily_id'] . '-' . $p['id'] . '-' . $post_type . '.xml';
		file_put_contents( self::$feed_display_dir . '/feed-list.html', '<a target="_blank" href="' . $saved_url . '">' . $saved_url . '</a><br>', FILE_APPEND );

	}

	/**
	 * Simple success cli msg specific to command.
	 *
	 * @return void
	 */
	public function get_cli_success_msg() {
		WP_CLI::success( 'The feed has been localized successfully. No typical errors have been encountered.' );
		WP_CLI::log( 'All urls have been saved to an html file at: ' . get_site_url() . '/wp-content/uploads/local-cat-display/hierarchy/feed-list.html for your convenience.' );
	}

	/**
	 * Set up a cron job to clone the new feed xmls if a user enables this in the gui.
	 * This is a workaround for now until a background processor is written so that the user doesn't have a very long ajax/browser function to wait on.
	 *
	 * @return false|int
	 */
	public function maybe_update_cron() {
		$scheduled = wp_next_scheduled( 'clone_cpc_feed_xmls' );
		$changed   = false;
		if ( empty( $_POST['enable_cron'] ) && empty( $_POST['disable_cron'] ) ) {
			return $scheduled;
		}
		if ( ! $scheduled && ! empty( $_POST['enable_cron'] ) ) { // Enable cron if requested and not already scheduled
			wp_schedule_event( time(), 'daily', 'clone_cpc_feed_xmls' );
			$changed = true;
		}
		if ( $scheduled && ! empty( $_POST['disable_cron'] ) ) { // Disable cron if requested and not already disabled
			wp_unschedule_event( $scheduled, 'clone_cpc_feed_xmls' );
			$changed = true;
		}
		// Refresh to confirm next scheduled
		if ( $changed ) {
			$scheduled = wp_next_scheduled( 'clone_cpc_feed_xmls' );
		}
		return $scheduled;
	}
}

Fx_Clone_Cpc_Xmls::instance();
