<?php


namespace FX_Load_More;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;

defined( 'ABSPATH' ) || exit;

class Endpoint
{
	const NAMESPACE = 'fx/load-more/v1';


    /**
     * Constructor
     * 
     * @return  void
     */	
	public function __construct()
	{
		$this->add_wp_hooks();
	}


	/**
	 * Hook into WordPress
	 *
	 * @return 	void
	 */
	private function add_wp_hooks(): void
	{
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}


	/**
	 * Register API REST routes for public usage
	 *
	 * @return 	void
	 */
	public function register_routes(): void
	{
		register_rest_route(
			self::NAMESPACE,
			'/get-posts',
			[
				'methods'				=> WP_REST_Server::READABLE,
				'callback'				=> [ $this, 'request_get_posts' ],
				'permission_callback'	=> '__return_true',
				'args'					=> [
					'page'			=> [
						'default'			=> 1,
						'sanitize_callback'	=> 'absint',
						'type'				=> [ 'integer', 'string' ],
					],
					'search'		=> [
						'default'			=> '',
						'sanitize_callback'	=> 'sanitize_text_field',
						'type'				=> 'string',
					],
					'post_type'		=> [
						'default'			=> 'post',
						'type'				=> 'string',
						'validate_callback'	=> [ $this, 'validate_post_type' ],
					],
					'posts_per_page' => [
						'default'			=> 10,
						'sanitize_callback'	=> 'absint',
						'type'				=> [ 'integer', 'string' ],
					],
					'taxonomy' => [
						'default'			=> null,
						'sanitize_callback'	=> 'sanitize_text_field',
						'type'				=> 'string',
					],
					'term_id' => [
						'default'			=> null,
						'sanitize_callback'	=> 'absint',
						'type'				=> [ 'integer', 'string' ],
					],										
				]
			]
		);
	}


	/**
	 * Handle requests for additional posts to display
	 *
	 * @param	WP_REST_Request	$request 	Request
	 * @return 	WP_REST_Response
	 */
	public function request_get_posts( WP_REST_Request $request )
	{
		$page 			= $request->get_param( 'page');
		$post_type 		= $request->get_param( 'post_type' );
		$search_string 	= $request->get_param( 'search' );
		$posts_per_page	= $request->get_param( 'posts_per_page' );
		$taxonomy 		= $request->get_param( 'taxonomy' );
		$term_id 		= $request->get_param( 'term_id' );

		$response = FX_Load_More()->api->get_posts( $page, $post_type, $search_string, $posts_per_page, $taxonomy, $term_id );

		return rest_ensure_response( $response );
	}


	/**
	 * Sanitizes search string
	 *
	 * @param	string	$search Search string
	 * @return 	string 			Sanitized search string
	 */
	public static function sanitize_input( string $search ): string
	{
		return sanitize_text_field( $search );
	}


	/**
	 * Ensures requested post type is a valid post type
	 *
	 * @param	string	$post_type	Post type
	 * @return 	string 				Sanitized post type
	 */
	public static function validate_post_type( string $post_type ): bool
	{
		$args = [
			'public' => true
		];

		$post_types = get_post_types( $args, 'names' );
		$post_types = array_values( $post_types );

		return in_array( $post_type, $post_types );
	}

}