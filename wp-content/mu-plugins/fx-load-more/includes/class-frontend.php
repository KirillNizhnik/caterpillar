<?php


namespace FX_Load_More;

defined( 'ABSPATH' ) || exit;

class Frontend
{
    /**
     * Constructor
     * 
     * @return  void
     */	
	public function __construct()
	{
        $this->include();		
		$this->add_wp_hooks();
	}



    /**
     * Include required files
     * 
     * @todo    separate includes conditionally by frontend/admin
     *
     * @return  void
     */
    private function include(): void
    {
        require_once( FX_Load_More()->plugin_path_inc . 'shortcodes/fx-load-more-pagination.php' );
    }



	/**
	 * Hook into WordPress
	 *
	 * @return 	void
	 */
	public function add_wp_hooks()
	{
		add_action( 'wp_enqueue_scripts', [ $this, 'maybe_enqueue_assets' ] );
	}


	/**
	 * Enqueues assets for specific pages
	 * 
	 * @todo 	update to use FX_Assets
	 *
	 * @return 	void
	 */
	public function maybe_enqueue_assets(): void
	{
		wp_register_script(
			'FXLM',
			FX_Load_More()->plugin_url . 'src/app.js',
			[ 'jquery' ],
			filemtime( FX_Load_More()->plugin_path . 'src/app.js' ),
			true
		);

		// only enqueue on blog, archive pages, and search page
		if( is_home() || is_archive() || is_search() ) {
			wp_enqueue_script( 'FXLM' );

			$addon_data = [
				'post_type'			=> get_post_type() ?: '',
				'post_count'		=> $this->get_total_post_count(),
				'posts_per_page'	=> get_option( 'posts_per_page' ),
				'rest_url' 			=> get_rest_url( null, FX_Load_More()->endpoint::NAMESPACE ),
			];

			if( is_tax() || is_category() ) {
				$term = get_queried_object();

				$addon_data['post_taxonomy'] 	= $term->taxonomy;
				$addon_data['post_term_id']		= $term->term_id;
			}

			wp_localize_script( 'FXLM', 'FXLM', $addon_data ); 

		} else {
			wp_localize_script( 
				'FXLM', 
				'FXLM', 
				[
					'rest_url' => get_rest_url( null, FX_Load_More()->endpoint::NAMESPACE ),
				] 
			); 
		}
	}



	/**
	 * Get total post count for current query
	 * 
	 * @todo 	update to use optional $post_type argument to get count for specific post type
	 *
	 * @return 	mixed 	If identified post type, then total post count; otherwise, null
	 */
	private function get_total_post_count()
	{
		$count = null;

		if( is_home() ) {
			$count = wp_count_posts( 'post' )->publish;

		} elseif( is_post_type_archive() ) {
			$post_type = get_post_type();
			$count = wp_count_posts( $post_type )->publish;

		} elseif( is_tax() || is_category() ) {
			$count = get_queried_object()->count;
		}

		return $count;
	}

}