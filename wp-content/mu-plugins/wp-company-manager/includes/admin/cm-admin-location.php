<?php

if ( ! defined('ABSPATH') ) exit;

class CM_Admin_Location {
 
    protected static $_instance; 

    public $post_type = 'location';
    public $address;
    public $latlng;

    public static function instance() {
        if ( ! isset( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    public function __construct() {
        add_action( 'edit_form_advanced', array( $this, 'add_nonce' ) );
		add_action( 'acf/save_post', array( $this, 'before_acf_save' ), 1 );
		add_action( 'acf/save_post', array( $this, 'after_acf_save' ), 20 );
    }

    /**
     * Security Field
     */
    public function add_nonce( $post ) {
        if( $post->post_type == $this->post_type ) {
            wp_nonce_field( 'cm_meta', '_cm_wpnonce' );
        }
    }

    /**
     * Store previous address for reference
     */
    public function before_acf_save( $post_id ) {
        if ( ! isset( $_POST['acf'] ) || ! isset( $_POST['post_type'] ) || ( $_POST['post_type'] !== $this->post_type ) ) {
            return;
        }
        $location_field = get_field( 'newlatlng' );
        $latlng = $location_field[ 'lat' ] . ',' . $location_field[ 'lng' ];
        $this->latlng = $latlng;
    }

    /**
     * If address is new or has changed, save geo info
     */
    public function after_acf_save( $post_id ) {
        if ( ! isset( $_POST['acf'] ) || ! isset( $_POST['post_type'] ) || ( $_POST['post_type'] !== $this->post_type ) ) {
            return;
        }

        if ( wp_verify_nonce( $_POST['_cm_wpnonce'], 'cm_meta' ) !== 1
            || ! get_field( 'newlatlng' ) ) {
            return;
        }

        // Only update info if address has changed - this allows for overriding dynamically-generated values if needed
        $location_field = get_field( 'newlatlng' );
        $latlng = $location_field[ 'lat' ] . ',' . $location_field[ 'lng' ];
        if ( $this->latlng !== $latlng ) {
            // we can get the lat/lng straight from ACF field
            update_post_meta( $post_id, 'address', $location_field[ 'address' ] );
            update_post_meta( $post_id, 'state', $location_field[ 'state' ] );
            update_post_meta( $post_id, 'postal_code', $location_field[ 'post_code' ] );
            update_post_meta( $post_id, 'city', $location_field[ 'city' ] );
            update_post_meta( $post_id, 'lat', $location_field[ 'lat' ] );
            update_post_meta( $post_id, 'lng', $location_field[ 'lng' ] );
        }
    }
}

CM_Admin_Location::instance();
