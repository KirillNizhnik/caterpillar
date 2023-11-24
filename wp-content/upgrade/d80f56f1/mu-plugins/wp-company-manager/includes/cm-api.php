<?php

if ( ! defined('ABSPATH') ) exit;

class CM_API {

	protected static $_instance;

    public static function instance() {
        if ( ! isset( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

	protected function __construct() {
		/**
		 * Locations Endpoint
		 */
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

    public function register_routes() {
        register_rest_route(
            'wpcm-locations/v1',
            '/view',
            array(
                'methods'	=> 'GET',
                'callback'	=> array( $this, 'get_locations' ),
                'permission_callback' => '__return_true',
            )
        );
        register_rest_route(
            'wpcm-locations/v1',
            '/closest',
            array(
                'methods'	=> 'GET',
                'callback'	=> array( $this, 'get_closest' ),
                'permission_callback' => '__return_true',
                'args'		=> array(
                    'latitude'	=> array( 'required' => true, 'type' => 'string' ),
                    'longitude'	=> array( 'required' => true, 'type' => 'string' ),
                )
            )
        );
    }

	/**
	 * Query Locations, Filter As Needed
	 */
	public function get_locations( WP_REST_Request $request ) {

		// Possible vars
		$location_id = $request->get_param( 'location_id' );
		$zipcode     = $request->get_param( 'zipcode' );
		$distance    = $request->get_param( 'distance' );
		$imahuman    = $request->get_param( 'imahuman' );
		
		
		
		$tax_terms_chosen = "";
		$tax_query = "";
		$tax_query_term = $request->get_param( 'tax_term' );
		$new_possibles = array('cylinder-service', 'cylinders', 'engine-block-repair', 'engine-blocks', 'ever-pac', 'hyd-repair', 'hydraulic-repair', 'lift', 'machines', 'machining', 'on-highway', 'parts', 'portable-welding-line-boring', 'portable-welding-and-line-boring', 'power', 'power-systems', 'sales');
		$used_possibles = array('cylinder-service', 'cylinders', 'engine-block-repair', 'engine-blocks', 'ever-pac', 'hyd-repair', 'hydraulic-repair', 'lift', 'machines', 'machining', 'on-highway', 'parts', 'portable-welding-line-boring', 'portable-welding-and-line-boring', 'power', 'power-systems', 'sales');;
		$rental_possibles = array('rentals');
		if(!empty($tax_query_term )) {
		    if($tax_query_term == "new"){
		        $tax_terms_chosen = $new_possibles;
		    } elseif($tax_query_term == "used") {
		        $tax_terms_chosen =  $used_possibles;
		    } elseif($tax_query_term == "rental") {
		        $tax_terms_chosen =  $rental_possibles;
		    } else {
		        $tax_terms_chosen = $tax_query_term;
		    }
		    $tax_query =  array(
            array(
                'taxonomy' => 'service'
                ,'field'    => 'slug'
                ,'terms'    => $tax_query_term
                //,'include_children' => true
                ,'operator'         => 'IN'
            ));
		}
		

		// Location query
		$locations = array();
		$args      = array(
			'post_type'      => 'location',
			'posts_per_page' => -1,
			'tax_query' => $tax_query,
			'orderby' => 'title',
            'order'   => 'ASC',
		);


		// Return empty array if zipcode validation fails
		if( CM_LIMIT_SEARCH && ! valid_postal_code( $zipcode ) && isset( $zipcode ) && !isset($tax_query)) {	

			return $locations;		
		}
        
		// Exit early if captcha doesn't validate
		if( (!empty( $imahuman ) && $imahuman !== '16749697') && isset( $zipcode ) ) {

			return $locations;
		}
        
		if ( $location_id ) {
            // Only query for single location
            $args['p'] = $location_id;
        }
		
		// Just to be safe, get geo info of user search outside of location loop
		if ( ! empty( $zipcode ) ) {
			$results = get_geo_info_address($zipcode);
		}
		
		$q = new WP_Query( $args );

		if ( $q->have_posts() ) {
			while ( $q->have_posts() ) {
				$q->the_post();
				$location = new CM_Location( get_the_ID() );
				if ( isset( $results ) ) {
					// Calculate distance from zipcode
					$location->distance = distance( $results->lat, $results->lng, $location->lat, $location->lng );
				}
				$locations[] = $location;
			}
		}
		if ( ! empty( $distance ) && ! empty( $zipcode ) ) {
			// Return closest location by zipcode
			$zipcode_results = array_filter( $locations, function( $location ) use( $distance )  {
			    return $location->distance <= $distance;
			});
			return ( array_values( $zipcode_results ) );
		}
		return ( $locations );
	}

	public function get_closest( WP_REST_Request $request ) {

		$latitude    = $request->get_param( 'latitude' );
		$longitude   = $request->get_param( 'longitude' );

        if ( empty ( $latitude ) || empty( $longitude ) ) {
            return false;
        }

		// Location query
		$locations = array();
		$args      = array(
			'post_type'      => 'location',
			'posts_per_page' => -1
		);

		$q = new WP_Query( $args );
		if ( $q->have_posts() ) {
			while ( $q->have_posts() ) {
				$q->the_post();
				$location = new CM_Location( get_the_ID() );
				if ( ! empty( $latitude ) && ! empty( $longitude ) ) {
					// Calculate distance from user
					$location->distance = distance( $latitude, $longitude, $location->lat, $location->lng );
				}
				$locations[] = $location;
			}
		}

        // Sort by distance and return the first one
        usort( $locations, function( $a, $b ) {
            return ( $a->distance == $b->distance ) ? 0 : ( ( $a->distance > $b->distance ) ? 1 : -1 );
        });

        return array_shift( $locations );
	}

	public function get_location_from_geo( $geo ) {
	    return $geo->geometry->location;
	}
}

CM_API::instance();
