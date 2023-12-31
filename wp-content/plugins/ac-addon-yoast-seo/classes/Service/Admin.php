<?php

namespace ACA\YoastSeo\Service;

use AC\Asset\Location\Absolute;
use AC\Registrable;
use ACA\YoastSeo\Asset;

final class Admin implements Registrable {

	/**
	 * @var Absolute
	 */
	private $location;

	public function __construct( Absolute $location ) {
		$this->location = $location;
	}

	public function register() {
		add_action( 'ac/admin_scripts', [ $this, 'admin_scripts' ] );
	}

	public function admin_scripts() {
		$script = new Asset\Script\Admin( 'aca-yoast-admin', $this->location );
		$script->enqueue();
	}

}