<?php

namespace Fx_Cat_Helper\cli;

use WP_CLI;
use Fx_Cat_Helper\Controllers\Fx_Search_Cpc_Feed;
use Fx_Cat_Helper\Controllers\Fx_Single_Cpc_Import;
use Fx_Cat_Helper\cli\Fx_Cpc_Cli_Import;
use Fx_Cat_Helper\cli\Fx_Dsf_Cli_Import;
use Fx_Cat_Helper\Controllers\Fx_Clone_Cpc_Xmls;

/**
 * This file is simply for the sake of registering all CLI functionality under a common command by reitering class instances.
 * This way commands can be listed for the viewer and follow a more linux type CLI pattern under fx_cat when invoking wp.
 * "wp fx_cat" ...
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Fx_Cat_Cli_Registers {

	/**
	 * Class instance.
	 *
	 * @see  instance()
	 * @type object
	 */
	protected static $instance;

	/**
	 * Instance of CLI commands for registering with hook later
	 *
	 * @return Fx_Cat_Cli_Registers|object
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 *  Leave for now - may use later
	 */
	protected function __construct() {}

	/**
	 * CPC search setup
	 *
	 * @param $args
	 * @param $assoc_args
	 *
	 * @return void
	 */
	public function cpc_search( $args, $assoc_args ) {
		if ( empty( $args ) ) {
			WP_CLI::warning( 'No product ID or name specified' );
			WP_CLI::log( 'Use this command to search the CAT New / CPC feed by indicating product name or cpc ID. EG:' );
			WP_CLI::log( '                                                              wp fx_cat cpc_search 1000020943' );
			WP_CLI::log( '                                                              wp fx_cat cpc_search 990K Wheel Loader' );
			return;
		}
		$search_instance = Fx_Search_Cpc_Feed::instance();
		$search_instance->get_xml_for_text_match( $args[0] );
	}

	/**
	 * CPC single import setup
	 *
	 * @param $args
	 * @param $assoc_args
	 *
	 * @return void
	 */
	public function single_cpc_import( $args, $assoc_args ) {
		if ( empty( $args ) ) {
			WP_CLI::warning( 'No product ID or name specified' );
			WP_CLI::log( 'Use this command to import a single cpc product by indicating  cpc ID. EG:' );
			WP_CLI::log( '                                                              wp fx_cat single_cpc_import 1000020943' );
			WP_CLI::log( ' (optional) - specify class with class flag:                    wp fx_cat single_cpc_import 1000020943 --class=406' );

			return;
		}
		$import_instance = Fx_Single_Cpc_Import::instance();
		$import_instance->attempt_to_import( $args[0], isset( $assoc_args['class'] ) ? $assoc_args['class'] : '' ); //user option plus a flag specified class argument if desired
	}

	/**
	 * Full new import setup
	 *
	 * @param $args
	 * @param $assoc_args
	 *
	 * @return void
	 */
	public function cpc_import( $args, $assoc_args ) {
		$import_cpc_instance = new Fx_Cpc_Cli_Import();
		$import_cpc_instance->import_Feed( $args, $assoc_args );
	}

	/**
	 * Full used import setup
	 *
	 * @param $args
	 * @param $assoc_args
	 *
	 * @return void
	 */
	public function dsf_import( $args, $assoc_args ) {
		$import_cpc_instance = new Fx_Dsf_Cli_Import();
		$import_cpc_instance->import_Feed( $args, $assoc_args );
	}

	/**
	 * Clone new feed xmls setup
	 *
	 * @param $args
	 * @param $assoc_args
	 *
	 * @return void
	 */
	public function clone_cpc( $args, $assoc_args ) {
		$clone_cpc_instance = new Fx_Clone_Cpc_Xmls();
		$clone_cpc_instance->clone_new_xmls( $args, $assoc_args );
	}


}

add_action(
	'cli_init',
	function () {
		WP_CLI::add_command( 'fx_cat', Fx_Cat_Cli_Registers::instance() );
	},
	1000
); //hook into cli init for this class
// I don't know why but trying to use this as the second arg in cli init above kept returning "Uncaught TypeError: call_user_func_array(): Argument #1 ($callback) must be a valid callback, function "fx_cat_register_commands" not found or invalid function name" even though it does the same thing...
/*
function fx_cat_register_commands() {
  WP_CLI::add_command( 'fx_cat', Fx_Cat_Cli_Registers::instance() );
}*/
