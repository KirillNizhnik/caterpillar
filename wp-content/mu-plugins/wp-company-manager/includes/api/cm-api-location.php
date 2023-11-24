<?php

class CM_API_Location implements CM_API_Resource_Interface
{
    public function __construct( CM_API_Server $server )
    {
        $this->server = $server;
        $this->routes = $this->register_routes();
        $this->params = $this->server->params[$this->server->method];
    }

    public function register_routes()
    {
        return apply_filters('cm_api_location_routes', array(
            'GET' => array(
                'location/?'              => 'get_locations'
                ,'location/(\d+)'         => 'get_location'
                ,'location/(\d)/reps'     => 'get_location_reps'
                ,'location/(\d)/services' => 'get_location_reps'
            ),
            'POST' => array(
                'location/?' => 'get_locations'
            ),
            'PUT' => array( // not doing any editing so leaving blank.
            ),
            'DELETE' => array( // not doing any editing so leaving blank.
            ),
        ));
    }


    public function get_locations()
    {
        $orderby_distance  = false;

        $this->params['post_type'] = 'location';

        //$this->params['lat'] = WPCM()->user->lat();
        //$this->params['lng'] = WPCM()->user->lng();


        if( ! isset($this->params['posts_per_page']) ) {
            $this->params['posts_per_page'] = -1;
        }

        if( isset($this->params['orderby']) && $this->params['orderby'] == 'distance' ) {
            unset($this->params['orderby']);
            $orderby_distance  = true;
        }

        $locations = array();

        $q = new WP_Query($this->params);

        if( $q->have_posts() ){

            while( $q->have_posts() ){

                $q->the_post();
                $location = WPCM()->location( get_the_id() );

                if( $orderby_distance ) {
                    $location->set_distance_from_query();

                    if( ! $location->distance ){
                        $location->set_distance_from_user();
                    }
                }

                $locations[] = $location;
            }

            if( isset($this->params['distance']) ) {
                $distance = intval($this->params['distance']);
                $locations = array_filter($locations, function($location) use($distance)  {
                    return $location->distance <= $distance;
                });
            }

            if( $orderby_distance ) {
                usort($locations, function($a, $b) {
                    return $a->distance == $b->distance ? 0 : ( $a->distance > $b->distance ) ? 1 : -1;
                });

                //$locations = array_shift($locations);
            }
        }

        wp_send_json( apply_filters('cm_api_get_locations_response', $locations) );
    }

    public function get_location( $location_id )
    {
        wp_send_json( WPCM()->location( $location_id ) );
    }

    public function get_location_reps( $location_id )
    {
        echo '<pre>'; var_dump('get_location_reps', $location_id); echo '</pre>'; die;
    }

    public function get_location_services( $location_id )
    {
        echo '<pre>'; var_dump('get_location_services', $location_id); echo '</pre>'; die;
    }
}
