<?php

class CM_Location {

    public $id;
    public $post;
    public $post_type;
    public $url;
    public $title;
    public $address;
    public $state;
    public $postal_code;
    public $services = array();
    public $lat;
    public $lng;
    public $directions;
    public $content;
    public $hours;
    public $phone;
    public $email;
    public $service_list = array();

    public function __construct( $location ) {

        // Location model constructor will accept either a location ID or post object
        $location_id = '';
        if ( is_numeric( $location ) ) {
            $location_id = $location;
        } else if ( $location instanceof \WP_Post ) {
            $location_id = $location->ID;
        }

        if ( ! $location_id ) {
            return false;
        }

        $this->id        = $location_id;
        $this->url       = get_permalink( $this->id );
        $this->title     = get_the_title( $this->id );
        $post_object     = get_post( $this->id );
        $this->post      = $post_object;
        $this->content   = $post_object->post_content;
        $this->post_type = $post_object->post_type;
        $services_raw = get_the_terms($this->id, 'service');
        $services_placeholder = array();
        foreach($services_raw as $service ) {
                                $this->services[] = $service->name;
                                $services_placeholder[] = $service->name;
                            } 
        $this->service_list[] = implode(", ", $services_placeholder);
        $custom = $this->setup_custom_data( get_post_custom( $this->id ) );
        foreach ( $custom as $key => $value ) {
            $this->{ $key } = $value;
        }

        if ( $this->address ) {
            $this->address_unformatted = $this->address;
            $this->address = nl2br( $this->address );
        }

        if ( $this->hours ) {
            $this->hours = nl2br( $this->hours );
        }
        
        $this->directions = $this->directions();
        if(get_field('alternate_get_directions') && !empty(get_field('alternate_get_directions'))){
            $this->directions = get_field('alternate_get_directions', $this->id);
        }
    }

    /**
     * Set Directions URL
     */
    public function directions() {
        if ( ! empty( $this->address ) ) {
            $address = trim( preg_replace( '/\s+/', ' ', str_replace( "\r\n", "\n", $this->address ) ) );
            return 'https://maps.google.com?q=' . urlencode( strip_tags( $address ) );
        } elseif ( ! empty( $this->lat ) && ! empty( $this->lng ) ) {
            return 'https://maps.google.com?q=' . $this->lat . ',' . $this->lng;
        }
        return false;
    }

    /**
     * Set ACF Data
     */
    private function setup_custom_data( $data ) {
        $return = array();
        foreach( $data as $k => $prop ) {
            // Don't include meta prefixed by an underscore
            if ( '_' === substr( $k, 0, 1 ) ) {
                continue;
            }
            $return[ $k ] = maybe_unserialize( array_shift( $prop ) );
        }
        return $return;
    }
}
