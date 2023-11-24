<?php namespace Fx_Cat_Helper\cli;

use Cat\Controllers\Importers\New_Feed;
use WP_CLI;

/**
 * CLI Tool for importing and monitoring the cpc feed.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Fx_Cpc_Cli_Import {

	/**
	 * New feed instance
	 *
	 * @var mixed|string
	 */
	public $new_feed = '';

	/**
	 * Set new feed instance and add success msg hooked into import completion.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action(
			'cat_after_new_class_import',
			[
				$this,
				'cpc_import_completed_message',
			]
		); //hook added wp cat v 2.5

		$this->new_feed = New_Feed::instance();
	}

	/**
	 * Simple success msg specific to command.
	 *
	 * @return void
	 */
	public static function cpc_import_completed_message() {
		WP_CLI::success( 'This CPC import appears to have finish successfully finished. No typical errors encountered.' );
	}

	/**
	 * Import Trigger all classes or specific class
	 *
	 * @param $args
	 * @param $assoc_args
	 *
	 * @return void
	 */
	public function import_feed( $args, $assoc_args ) {
		//args unused for now - maybe customize later
		$valid_classes_whole = CAT()->available_classes;
		$valid_classes       = array_keys( $valid_classes_whole );

		fx_cpc_dependency_checker();

		if ( isset( $assoc_args['class'] ) && in_array( $assoc_args['class'], $valid_classes, true ) ) { //single import specified
			$class = $assoc_args['class'];
			if ( ! in_array( $class, fx_cat_helper_tools()->accessible_class_ids, true ) ) {
				WP_CLI::warning( "Class specified is not selected in the CAT's plugin config. " );
				WP_CLI::confirm( 'Are you sure you want to import this class?', $assoc_args );
			}
			WP_CLI::log( 'Importing class ' . $class );
			$this->trigger_cpc_class_import( $class );
		} else { //run through selected classes import
			foreach ( fx_cat_helper_tools()->accessible_class_ids as $class ) {
				WP_CLI::log( 'Importing class ' . $class );
				$this->trigger_cpc_class_import( $class );
			}
		}
	}

	/**
	 * Trigger actual import
	 *
	 * @param $class_id
	 *
	 * @return void
	 */
	public function trigger_cpc_class_import( $class_id ) {
		try {
			$this->new_feed->import( $class_id );
		} catch ( Exception $exc ) {
			WP_CLI::error( $exc );
		}
	}
}
