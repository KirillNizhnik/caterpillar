<?php
/**
 * Plugin Name: FX Enforce CF7 HTML IDs
 * Plugin URI: https://www.webfx.com
 * Description: Edits the CF7 shortcode to automatically include a unique html_id attribute so they will be consistent. 
 * Might be helpful with MCFX.
 * Version: 1.1
 * Author: The WebFX Team
 * Author URI: https://www.webfx.com
 **/
 
 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'FX_Enforce_Html_ID' ) ) :

final class FX_Enforce_Html_ID {

    public $version = '1.0';
    public $cf7_version = '5.1.3'; 
    protected static $_instance = null;
	
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        add_action( 'plugins_loaded', array( $this, 'setup' ) );
    }

    public function setup() {
        add_action( 'wpcf7_contact_form_shortcode', array( $this, 'filter_wpcf7_contact_form_shortcode' ), 10, 3 );
    }

    public function filter_wpcf7_contact_form_shortcode(  $shortcode, $args, $instance  ) {		
		// don't filter if shortcode empty; it will get filtered again https://bit.ly/2QS34yq
		if ($shortcode) {
            $html_id = 'cf7-form-' . $instance->id();
            // add html id before closing ]
            $new_shortcode = substr($shortcode, 0, strlen($shortcode) - 1);
            $new_shortcode .= ' html_id="'.$html_id.'"]';
            
            return $new_shortcode;
        }
		return $shortcode;
    }
}

endif;

function FX_Enforce_Html_ID() {
    return FX_Enforce_Html_ID::instance();
}

FX_Enforce_Html_ID();