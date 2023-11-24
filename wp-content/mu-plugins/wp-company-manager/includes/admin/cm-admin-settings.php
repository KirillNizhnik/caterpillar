<?php

if ( ! defined('ABSPATH') ) exit;

class CM_Admin_Settings {
 
    public $location_slug;

    protected static $_instance; 

    public static function instance() {
        if ( ! isset( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        if ( function_exists( 'acf_add_options_sub_page' ) ) {
            add_action( 'admin_menu', array( $this, 'settings_page' ), 98 );
        }
		add_action( 'acf/save_post', array( $this, 'before_acf_save' ), 1 );
		add_action( 'acf/save_post', array( $this, 'after_acf_save' ), 20 );
		//add_action( 'acf/save_post', array( $this, 'update_geocoding_data' ), 30 ); 

        // If a rewrite rule flush has been scheduled (see after_acf_save function), flush those rules
        if ( get_transient( 'wpcm_flush_rules' ) ) {
            add_action( 'init', 'flush_rewrite_rules' );
            delete_transient( 'wpcm_flush_rules' );
        }
    }

    public function settings_page() {
		acf_add_options_sub_page(array(
			'page_title'  => 'Company Manager Settings',
			'menu_title'  => 'Settings',
			'parent_slug' => 'edit.php?post_type=location'
		));
		$this->settings_page_fields();
    }

    public function before_acf_save( $post_id ) {
        if ( ! isset( $_POST['acf'] ) || ! isset( $_POST['_acf_post_id'] ) || ( $_POST['_acf_post_id'] !== 'options' ) ) {
            return;
        }
        $this->location_slug = get_option( 'options_location_slug' );
    }

    public function after_acf_save( $post_id ) {

        if ( ! isset( $_POST['acf'] ) || ! isset( $_POST['_acf_post_id'] ) || ( $_POST['_acf_post_id'] !== 'options' ) ) {
            return;
        }

        // If location slug has changed, flush rewrite rules on next pageload
        if ( $this->location_slug !== get_option( 'options_location_slug' ) ) {
            set_transient( 'wpcm_flush_rules', true );
        }
	}
	
	public function update_geocoding_data( $post_id ) {

        if ( ! isset( $_POST['acf'] ) || ! isset( $_POST['_acf_post_id'] ) || ( $_POST['_acf_post_id'] !== 'options' ) ) {
            return;
        }

		// Query and loop through all locations in order to regenerate geocoding data
		$args = array(
			'post_type' => 'location',
			'posts_per_page' => -1
		);

		$locations = new WP_Query( $args );
		if( $locations->have_posts() ): while( $locations->have_posts() ): $locations->the_post(); 
			$pid = get_the_ID();
	        $location_field = get_field( 'newlatlng' );
            // we can get the lat/lng straight from ACF field
            update_post_meta( $pid, 'address', $location_field[ 'address' ] );
            update_post_meta( $pid, 'state', $location_field[ 'state' ] );
            update_post_meta( $pid, 'postal_code', $location_field[ 'post_code' ] );
            update_post_meta( $pid, 'city', $location_field[ 'city' ] );
            update_post_meta( $pid, 'lat', $location_field[ 'lat' ] );
            update_post_meta( $pid, 'lng', $location_field[ 'lng' ] );

		endwhile; endif;
	}

    public function settings_page_fields() {

		acf_add_local_field_group(array(
			'key' => 'group_5b522a1516ffb',
			'title' => 'Company Manager Settings',
			'fields' => array(
				array(
					'key' => 'field_5b522a2dc447e',
					'label' => 'Maps API Key',
					'name' => 'maps_api_key',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
				array(
					'key' => 'field_5b522a5ac447f',
					'label' => 'Geocoding API Key',
					'name' => 'geocoding_api_key',
					'type' => 'text',
					'instructions' => 'If left blank, will default to the value of the Maps API Key.',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
				array(
					'key' => 'field_5b522a796919b',
					'label' => 'Location Slug',
					'name' => 'location_slug',
					'type' => 'text',
					'instructions' => 'This is the URL slug that will appear as part of individual location URLs - e.g. https://www.example.com/<strong>[location slug]</strong>/zanesville-oh/<br>If blank, this will default to "company/locations".',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => 'company/locations',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
				array(
					'key' => 'field_5ca38e1406df4',
					'label' => 'Limit search to zipcode?',
					'name' => 'cm_limit_search',
					'type' => 'true_false',
					'instructions' => 'Check this to limit the location search to only search by zipcode',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => 1,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
				array(
					'key' => 'field_wpcmlocaldb',
					'label' => 'Enable local db saving of searches?',
					'name' => 'cm_enable_search_save',
					'type' => 'true_false',
					'instructions' => 'Check this to enable local db saving of searches',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => 1,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'options_page',
						'operator' => '==',
						'value' => 'acf-options-settings',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => 1,
			'description' => '',
		));
    }
}

CM_Admin_Settings::instance();
