<?php

class CM_API_User implements CM_API_Resource_Interface
{
    public function __construct( CM_API_Server $server )
    {
        $this->server = $server;
        $this->routes = $this->register_routes();
        $this->params = $this->server->params[$this->server->method];
    }

    public function register_routes()
    {
        return apply_filters('cm_api_user_routes', array(
            'GET' => array(
                'user/?'           => 'get_user'
                ,'user/location/?' => 'get_location'
                ,'user/rep/?'      => 'get_rep'
            ),
            'POST' => array(
                'user/?'           => 'update_user'
                ,'user/location/?' => 'set_location'
                ,'user/rep/?'      => 'get_rep'
            ),
            'PUT' => array(
                'user/?'           => 'update_user'
            ),
            'DELETE' => array( // not doing any editing so leaving blank.
            ),
        ));
    }

    public function get_user()
    {
        wp_send_json(apply_filters('cm_api_user_response', WPCM()->user));
    }

    public function update_user()
    {
        if(isset($this->params['geo'])){
            WPCM()->user->set_geo_by_geolocation($this->params['geo']);
        }

        wp_send_json(apply_filters('cm_api_user_response', WPCM()->user));
    }


    public function get_location()
    {
        wp_send_json(apply_filters('cm_api_get_user_location_response', WPCM()->user->location()));
    }


    public function set_location()
    {
        WPCM()->user->set_geo_by_address( sanitize_text_field($_POST['address']) );
        wp_send_json(apply_filters('cm_api_post_user_location_response', WPCM()->user->location()));
    }

    public function get_rep()
    {
        wp_send_json(apply_filters('cm_api_get_user_location_response', WPCM()->user->location()));
    }

}