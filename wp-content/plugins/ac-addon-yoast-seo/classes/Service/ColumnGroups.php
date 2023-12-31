<?php

namespace ACA\YoastSeo\Service;

use AC;
use AC\Registrable;

final class ColumnGroups implements Registrable {

	public function register() {
		add_action( 'ac/column_groups', [ $this, 'register_column_groups' ] );
	}

	public function register_column_groups( AC\Groups $groups ) {
		$groups->register_group( 'yoast-seo', 'Yoast SEO', 25 );
	}

}