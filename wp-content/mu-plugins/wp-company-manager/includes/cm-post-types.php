<?php

if ( ! defined('ABSPATH') ) exit;

class CM_Post_Types {

    protected static $_instance;

    public static function instance() {
        if ( ! isset( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    protected function __construct() {
        add_action( 'init', array( $this, 'register_post_types' ) );
        add_action( 'init', array( __CLASS__, 'register_taxonomies'));
        add_action( 'admin_menu', array( __CLASS__, 'add_tax_page' ) );
    }

    public static function register_post_types() {
        $instance = self::instance();
        if ( ! function_exists( 'get_field' ) || ! $location_slug = get_field( 'location_slug', 'option' ) ) {
            $location_slug = 'company/locations';
        }
        $instance->slug = apply_filters( 'wpcm_location_slug', 'company/locations');
        $instance->rep_slug = apply_filters( 'wpcm_rep_slug', 'sales-rep-locations/rep');

        // register location post type
        register_post_type(
            'location',
            array(
                'labels' => array(
                    'name' => 'Locations',
                    'singular_name' => 'Location',
                    'add_new' => 'New Location',
                    'add_new_item' => 'Add New Location',
                    'edit_item' => 'Edit Location',
                    'new_item' => 'New Location',
                    'all_items' => 'Locations',
                    'view_item' => 'View Location',
                    'search_items' => 'Search Locations',
                    'not_found' =>  'No locations found',
                    'not_found_in_trash' => 'No locations found in trash',
                    'parent_item_colon' => '',
                    'menu_name' => 'Company'
                ),
                'public' => true,
                'has_archive' => false,
                'show_ui' => true,
                'show_in_nav_menus' => true,
                'show_in_menu' => true,
                'show_in_rest' => true,
                'supports' => array( 'title', 'editor' ),
                'rewrite' => array(
                   'slug' => $location_slug,
                   'with_front' => false
                ),
                'menu_icon' => 'dashicons-location-alt'
            )
        );
        
        // register our post type

        register_post_type(
            'rep'
            ,array(
                'labels' => array(
                     'name' => 'Representatives'
                    ,'singular_name' => 'Representative'
                    ,'add_new' => 'New Representative'
                    ,'add_new_item' => 'Add New Representative'
                    ,'edit_item' => 'Edit Representative'
                    ,'new_item' => 'New Representative'
                    ,'all_items' => 'Representatives'
                    ,'view_item' => 'View Representative'
                    ,'search_items' => 'Search Representatives'
                    ,'not_found' =>  'No representative found'
                    ,'not_found_in_trash' => 'No representatives found in trash'
                    ,'parent_item_colon' => ''
                    ,'menu_name' => 'Representatives'
                )
                ,'public' => false
                ,'publicly_queryable' => true
                ,'exclude_from_search' => true
                ,'has_archive' => false
                ,'show_ui' => true
                ,'show_in_nav_menus' => false
                ,'show_in_menu' => 'edit.php?post_type=location'
                ,'supports' => array('title','thumbnail')
                ,'rewrite' => array(
                   'slug' => $instance->rep_slug
                   ,'with_front' => false
                   ,'feed'=> false
                   ,'pages'=> true
                   //,'ep_mask'=> EP_COMPANY
                )
            )
        );
        
    }
    
    /**
     * Registers our custom taxonomies
     *
     * @return void
     */

    public static function register_taxonomies()
    {
        $instance = self::instance();
        register_taxonomy(
             'type'
            ,'location'
            ,array(
                'label' => __( 'Type' )
                ,'rewrite' =>  array(
                    'slug' => apply_filters( 'wpcm_type_slug', $instance->slug.'/types' )
                    ,'with_front' => false
                )
                ,'hierarchical' => true
                ,'show_admin_column' => true
            )
        );

        register_taxonomy(
             'service'
            ,'location'
            ,array(
                'label' => __( 'Service' )
                ,'rewrite' =>  array(
                    'slug' => apply_filters( 'wpcm_service_slug', $instance->slug.'/services' )
                    ,'with_front' => false
                )
                ,'hierarchical' => true
                ,'show_admin_column' => true
            )
        );

        register_taxonomy(
             'rep_industry'
            ,'rep'
            ,array(
                'label' => __( 'Rep Industries' )
                ,'rewrite' =>  array(
                    'slug' => apply_filters( 'wpcm_rep_industry_slug', $instance->rep_slug.'/industries' )
                    ,'with_front' => false
                )
                ,'hierarchical' => true
                ,'show_admin_column' => true
            )
        );
    }
    
    public static function add_tax_page()
    {
        add_submenu_page( 'edit.php?post_type=location', 'Rep Industries', 'Rep Industries', 'manage_options', 'edit-tags.php?taxonomy=rep_industry&post_type=rep' );
    }
}

CM_Post_Types::instance();
