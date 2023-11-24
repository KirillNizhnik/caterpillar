<?php

namespace FX_Load_More;
use SWP_Query;
use WP_Query;

defined( 'ABSPATH' ) || exit;

class Search
{
	/**
	 * @todo — add additional post types to include in search as needed
	 */
	public $searchable_post_types = [
		'page'		=> 'Pages',
		'post'		=> 'Posts',
		'product'	=> 'Products',
		'cat_new_machine' => 'New Machines',
		'cat_new_attachment' => 'New Attachments',
		'cat_new_power' => 'New Powers',
		'cat_new_allied' => 'New Allieds',
		'cat_industry' => 'Industries',
		'cat_used_machine' => 'Used Machines',
		'location' => 'Our Locations',
		'deals-specials' => 'Deals and Specials'
	];


	public $posts_per_page;



    /**
     * Constructor
     * 
     * @return  void
     */	
	public function __construct()
	{
		$this->posts_per_page = get_option( 'posts_per_page' );

		$this->add_wp_hooks();
	}


	/**
	 * Hook into WordPress
	 *
	 * @return 	void
	 */
	private function add_wp_hooks(): void
	{
		add_action( 'init', [ $this, 'check_post_types' ], 20 );
	}


	/**
	 * Ensure that searchable post types actually exist. This avoids DB database errors when searching
	 *
	 * @return 	void
	 */
	public function check_post_types()
	{
		$all_post_types = get_post_types();

		foreach( $this->searchable_post_types as $post_type_key => $pretty_name ) {
			if( !in_array( $post_type_key, $all_post_types ) ) {
				unset( $this->searchable_post_types[ $post_type_key ] );
			}
		}
	}


	/**
	 * Get tabs for switching between search results
	 *
	 * @param 	string 	$search_query 	User search string
	 * @param 	int 	$paged 			Page of search results
	 * 
	 * @return 	array 	Contains array of tab titles, tab post types, and post count
	 */
	public function get_tabbed_results( string $search_query = '', int $paged = 1 )
	{
		// prep results
		$results = [];
		foreach( $this->searchable_post_types as $post_type_key => $tab_title ) {
			$result = [
				'post_type_key'	=> $post_type_key,
				'tab_title'		=> $tab_title,
				'tab_count'		=> 0,
				'posts'			=> []
			];

			$results[ $post_type_key ] = $result;
		}

		$args = [
			'post_type'			=> array_keys( $this->searchable_post_types ),
			'posts_per_page'	=> -1,
			's'					=> $search_query,
		];
		$query = new SWP_Query( $args );
		foreach( $query->posts as $post ) {

			// add post to results based on post type
			$results[ $post->post_type ]['posts'][] = $post;
		}

		// clean up results by getting post count and extracting target page of posts
		foreach( $results as $post_type_key => &$data ) {
			$posts = $data['posts'];

			// set post count (will appear in tab title)
			$data['tab_count'] = count( $data['posts'] );

			// extract specific page of posts
			$posts_per_page = $this->posts_per_page;
			$paged_posts = array_splice( 
				$posts, 
				( $paged - 1 ) * $posts_per_page,
				$posts_per_page
			);

			$data['posts'] = $paged_posts;
		}

		return $results;
	}

}
