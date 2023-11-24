<?php
namespace Cat\Controllers;

class Facet_WP
{
    /**
     * Instance of class
     * @type Facet_WP
     */
    protected static $_instance;

    /**
     * WP hooks/actions.
     * @return void
     */
    protected function __construct( )
    {
        add_action( 'wp_ajax_template_setting', array( $this, 'save_template_settings' ) );
        add_action( 'wp_ajax_nopriv_template_setting', array( $this, 'save_template_settings' ) );
        add_filter( 'facetwp_sort_options', array( $this, 'add_sort_options'), 10, 2 );
    }

    /**
     * Singleton Factory Method
     * @return Facet_WP instance of class
     */
    public static function instance()
    {
        if ( ! isset( self::$_instance) ) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }



    public function save_template_settings()
    {
        CAT()->session['template'] = $_POST['template'];
        exit;
    }


    public function add_sort_options( $options, $params )
    {
         return array(
            'default' => array(
                'label' => __( 'Sort by', 'fwp' ),
                'query_args' => array()
            )
           /* ,'make_asc' => array(
                'label' => __( 'Make (A-Z)', 'fwp' ),
                'query_args' => array(
                    'orderby' => 'meta_value',
                    'meta_key' => 'make',
                    'order' => 'ASC',
                )
            )
            ,'make_desc' => array(
                'label' => __( 'Make (Z-A)', 'fwp' ),
                'query_args' => array(
                    'orderby' => 'meta_value',
                    'meta_key' => 'make',
                    'order' => 'DESC',
                )
            ) */
            ,'model_asc' => array(
                'label' => __( 'Model (A-Z)', 'fwp' ),
                'query_args' => array(
                    'orderby' => 'meta_value',
                    'meta_key' => 'model',
                    'order' => 'ASC',
                )
            )
            ,'model_desc' => array(
                'label' => __( 'Model (Z-A)', 'fwp' ),
                'query_args' => array(
                    'orderby' => 'meta_value',
                    'meta_key' => 'model',
                    'order' => 'DESC',
                )
            )
            ,'year_desc' => array(
                'label' => __( 'Year Newest - Oldest', 'fwp' ),
                'query_args' => array(
                    'orderby' => 'meta_value_num',
                    'meta_key' => 'year',
                    'order' => 'DESC',
                )
            )
            ,'year_asc' => array(
                'label' => __( 'Year Oldest - Newest', 'fwp' ),
                'query_args' => array(
                    'orderby' => 'meta_value_num',
                    'meta_key' => 'year',
                    'order' => 'ASC',
                )
            )
            ,'price_asc' => array(
                'label' => __( 'Price Lowest - Highest', 'fwp' ),
                'query_args' => array(
                    'orderby' => 'meta_value_num',
                    'meta_key' => 'price',
                    'order' => 'ASC',
                )
            )
            ,'price_desc' => array(
                'label' => __( 'Price Highest - Lowest', 'fwp' ),
                'query_args' => array(
                    'orderby' => 'meta_value_num',
                    'meta_key' => 'price',
                    'order' => 'DESC',
                )
            )
        );
    }

}

Facet_WP::instance();
