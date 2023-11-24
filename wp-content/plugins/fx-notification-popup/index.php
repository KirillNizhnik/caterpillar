<?php

/**
 * Plugin Name: FX Notification Popup
 * Plugin URI:  https://webfx.com
 * Description: Display a notification popup on all pages
 * Version:     2.0.1
 * Author:      The WebFX Team
 * Author URI:  https://webfx.com
 * Text Domain: webfx
 */


class FX_Notification_Popup 
{
    protected static $instance = null;
	
	public $plugin_path     = null;
    public $plugin_path_inc = null;
    public $plugin_url      = null;



    /**
     * Singleton instance
     *
     * @return  self
     */
	public static function instance() 
    {
		if( null === self::$instance ) {
			self::$instance = new self();
        }

        return self::$instance;
	}



    public function __construct() 
    {
        $this->define();
        $this->add_wp_hooks(); 
    }
    
    

    public function define()
    {
        $this->plugin_path 		= plugin_dir_path( __FILE__ );
        $this->plugin_url 		= plugin_dir_url( __FILE__ );
        $this->plugin_path_inc 	= sprintf( '%sincludes/', $this->plugin_path );
    }



    function add_wp_hooks() 
    {
        add_action( 'init',                     array( $this, 'init' ) );
        add_action( 'wp_enqueue_scripts',       array( $this, 'register_assets' ) );
        add_action( 'wp_footer',                array( $this, 'maybe_add_shortcode' ) );
        add_action( 'acf/save_post',            array( $this, 'prev_save_options' ), 10, 1 );
    }



    public function init() 
    {
        $this->add_acf_setting_page();

        require_once( $this->plugin_path_inc . 'acf-fields.php' );
		require_once( $this->plugin_path_inc . 'shortcode.php' );        
    }



    public function register_assets() 
    {
        wp_register_script( 
            'notification_popup', 
            $this->plugin_url . '/assets/js/app.js', 
            [ 'jquery' ], 
            filemtime( $this->plugin_path . '/assets/js/app.js' ),
            true
        );

        wp_localize_script(
            'notification_popup', 
            'FXNB', 
            [
                'fx_notification_popup_time' => get_option( 'fx_notification_popup_lastupdate' ) ?? ''
            ]
        );

        wp_register_style( 
            'notification_popup', 
            $this->plugin_url . '/assets/css/app.css', 
            null, 
            filemtime( $this->plugin_path . '/assets/css/app.css' )
        );        
    }



    public function add_acf_setting_page() 
    {
        if ( function_exists( 'acf_add_options_page' ) ) {
            acf_add_options_page(
                array(
					'page_title' => 'Notification Popup',
					'menu_title' => 'Notification Popup',
					'menu_slug'  => 'fx-notification-popup',
					'capability' => 'edit_posts',
                    'redirect'   => false,
                    'icon_url'   => 'dashicons-schedule',
                )
            );
        }
    }



    public function maybe_add_shortcode()
    {
        if( true === get_field( 'toggle_notification_popup', 'option' ) ) {
            echo apply_shortcodes( '[fx_notification_popup]' );
        }
    }



    /**
     * Save the last time the information was updated to refresh the cookie
     */
    public function prev_save_options() 
    {
        if ( isset( $_POST['_acf_screen'] ) && 'options' === $_POST['_acf_screen'] ) {
            update_option( 'fx_notification_popup_lastupdate', time() );
        }
    }

}

FX_Notification_Popup::instance();
