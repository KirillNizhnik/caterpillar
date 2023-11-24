<?php

/*
Plugin Name: Quinn Popup Manager
Version: 0.1
Description: plugin used to create popups in the site. 
Author: WebFX
Author URI: http://webfx.com
Plugin URI: http://webfx.com
Text Domain: quinn-popup-manager
*/

include_once 'config.inc.php';

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'Quinn_Popup_Manager' ) ) :

final class Quinn_Popup_Manager {

	protected $version = '0.1';

	protected static $_instance = null;

	public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
		
		$this->setup();
		
    }

    public function setup() {

    	$this->includes();

    }

    public function includes() {
		
		include_once 'includes/quinn-popup-post-type.php';
        include_once 'includes/quinn-popup-shortcode.php';

    }

}

add_action('plugins_loaded', array( 'Quinn_Popup_Manager', 'instance' ) );

endif;