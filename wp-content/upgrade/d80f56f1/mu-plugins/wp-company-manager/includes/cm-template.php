<?php

if ( ! defined('ABSPATH') ) exit;

class CM_Template {

    protected static $_instance;

    public static $add_scripts;

    public static function instance() {
        if ( ! isset( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    protected function __construct() {
        add_filter( 'template_include', array( $this, 'set_template' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
        add_action( 'wp_print_footer_scripts', array( $this, 'print_conditional_scripts' ) );
    }

    public function set_template( $template ) {
        if ( is_singular( 'location' ) ) {
            $template = self::get_template( 'location' );
        }
        return $template;
    }

    public function register_scripts() {
        // GMAPS_API_KEY is defined in wp-company-manager.php
        wp_register_script(
            'google-maps',
            'https://maps.googleapis.com/maps/api/js?v=3.exp&key='.GMAPS_API_KEY,
            array(),
            false,
            true
        );
        wp_register_script(
            'js-cookie',
            self::get_asset( 'js-cookie.min.js' ),
            array(),
            false,
            true
        );
        $version = filemtime( WPCM()->plugin_path . 'assets/js/wpcm.js' );
        wp_register_script(
            'wpcm',
            self::get_asset( 'wpcm.js' ),
            array( 'jquery', 'google-maps', 'backbone', 'js-cookie' ),
            $version,
            true
        );
        wp_localize_script( 'wpcm', 'WPCM', array( 'plugin_url' => WPCM()->plugin_url, 'action' => array() ) );
    }

    public function print_conditional_scripts() {
        if ( ! self::$add_scripts ) {
            return;
        }
        wp_print_scripts( 'wpcm' );
    }

    public static function get_template( $template ) {
        if ( substr( $template, -4 ) != '.php' ) {
            $template .= '.php';
        }
        if ( $theme_file = locate_template( array( 'company/' . $template ) ) ) {
            $file = $theme_file;
        } else {
            $file = WPCM()->plugin_path . 'templates/' . $template;
        }
        return $file;
    }

    public static function get_asset( $asset ) {
        // figure out which folder to look in
        $folder = ( substr( $asset, -3 ) == '.js' ) ? 'js/' : 'css/';
        // test if file exists in theme
        if ( $theme_file = locate_template( array( 'company/'.$asset ) ) ) {
            $file = get_stylesheet_directory_uri() . '/company/' . $asset;
        } else {
            $file = WPCM()->plugin_url . 'assets/' . $folder . $asset;
        }
        return $file;
    }
}

CM_Template::instance();
