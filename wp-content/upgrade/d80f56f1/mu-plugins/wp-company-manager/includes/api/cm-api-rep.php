<?php

class CM_API_Rep implements CM_API_Resource_Interface
{
    public function __construct( CM_API_Server $server )
    {
        $this->server = $server;
        $this->routes = $this->register_routes();
        $this->params = $this->server->params[$this->server->method];
    }

    public function register_routes()
    {
        return apply_filters('cm_api_rep_routes', array(
            'GET' => array(
                'rep/?' => 'get_reps'
            ),
            'POST' => array(
                'rep/?' => 'get_reps'
            ),
            'PUT' => array( // not doing any editing so leaving blank.
            ),
            'DELETE' => array( // not doing any editing so leaving blank.
            ),
        ));
    }


    public function get_reps()
    {
        global $wpdb;

        $this->params['post_type'] = 'rep';

        if ( ! isset( $this->params['posts_per_page'] ) ) {
            $this->params['posts_per_page'] = -1;
        }

        $representatives = array();
        $zip_searched = (! empty( $this->params['zipcode'] ) ) ? $this->params['zipcode'] : '';

        // locations
        $query = new WP_Query(array(
            'post_type'       => 'rep'
            ,'posts_per_page' => -1
            ,'orderby' => 'title'
            ,'order' => 'ASC'
            ,'meta_query' => array(
                array(
                    'key' => 'zipcode'
                    ,'value' => $zip_searched,
                    'compare' => 'LIKE',
                )
            )
        ));


        while ( $query->have_posts() ) {

            $query->the_post();
            $representatives[] = WPCM()->rep( get_the_ID() );

        }

        wp_reset_postdata();

        wp_send_json( apply_filters( 'cm_api_get_reps_response', $representatives ) );
    }
}
