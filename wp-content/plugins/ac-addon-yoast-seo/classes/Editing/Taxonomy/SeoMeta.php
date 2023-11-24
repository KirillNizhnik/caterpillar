<?php

namespace ACA\YoastSeo\Editing\Taxonomy;

use AC\Request;
use ACP\Editing;

class SeoMeta implements Editing\Service {

	/**
	 * @var string
	 */
	private $meta_key;

	/**
	 * @var string
	 */
	private $taxonomy;

	/**
	 * @var Editing\View
	 */
	private $view;

	public function __construct( $taxonomy, $meta_key, Editing\View $view = null ) {
		$this->meta_key = $meta_key;
		$this->taxonomy = $taxonomy;
		$this->view = $view ?: new Editing\View\Text();
	}

	public function get_view( $context ) {
		return $this->view;
	}

	public function get_value( $id ) {
		$meta = get_option( 'wpseo_taxonomy_meta' );

		if ( ! is_array( $meta ) ) {
			return false;
		}

		return isset( $meta[ $this->taxonomy ][ $id ][ $this->meta_key ] )
			? $meta[ $this->taxonomy ][ $id ][ $this->meta_key ]
			: false;
	}

	public function update( Request $request ) {
		$meta = get_option( 'wpseo_taxonomy_meta' );
		$id = $request->get( 'id' );

		if ( ! isset( $meta[ $this->taxonomy ] ) ) {
			$meta[ $this->taxonomy ] = [];
		}

		if ( ! isset( $meta[ $this->taxonomy ][ $id ] ) ) {
			$meta[ $this->taxonomy ][ $id ] = [];
		}

		$meta[ $this->taxonomy ][ $id ][ $this->meta_key ] = $request->get( 'value' );

		update_option( 'wpseo_taxonomy_meta', $meta );
	}

}