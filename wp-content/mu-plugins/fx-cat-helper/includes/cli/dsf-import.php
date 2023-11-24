<?php namespace Fx_Cat_Helper\cli;

/**
 * CLI Tool for importing and monitoring the dsf feed.
 */

use Cat\Controllers\Importers\Used_Feed;
use WP_CLI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Fx_Dsf_Cli_Import {

	/**
	 * Add success msg hooked into import completion.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action(
			'cat_after_used_feed_import',
			[
				$this,
				'dsf_import_completed_message',
			]
		); //hook added wp cat v 2.5
	}

	/**
	 * Simple success msg specific to command.
	 *
	 * @return void
	 */
	public static function dsf_import_completed_message() {
		WP_CLI::success( 'DSF Import appears to have finish successfully finished. No typical errors encountered.' );
	}

	/**
	 * Simple invoke on used feed import. Leave args for now in case improved later.
	 *
	 * @param $args
	 * @param $assoc_args
	 *
	 * @return void
	 */
	public function import_feed( $args, $assoc_args ) {
		//args and $assoc args maybe for customize later
		fx_dsf_dependency_checker();
		$used_feed = Used_Feed::instance();
		WP_CLI::log( 'Importing used dsf feed...' );
		try {
			$used_feed->import();
		} catch ( Exception $exc ) {
			WP_CLI::error( $exc );
		}
	}
}
