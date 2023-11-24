<?php
/**
 * Plugin Name: FX Landing Page
 * Plugin URI: https://www.webfx.com
 * Description: Adds Built-in Landing Page Functionality
 * Version: 1.1
 * Author: The WebFX Team
 * Author URI: https://www.webfx.com
 * Documentation: https://webpagefx.mangoapps.com/user/wikis/list?loc=T&open=51642
 * Text Domain: webfx
 */
class FX_Landing_Page {

    protected $plugin_slug;
    private static $instance = null;
    protected $templates;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new FX_Landing_Page();
        }
        return self::$instance;
    }

    private function __construct() {

        // Add your templates to this array.
        $this->templates = array(
            'page-landing.php' => 'Landing Page',
        );

        add_filter( 'theme_page_templates', array( $this, 'add_new_template' ) );

        // Add a filter to the save post to inject our template into the page cache
        add_filter( 'wp_insert_post_data', array( $this, 'register_project_templates' ) );

        // Add a filter to the template include to determine if the page has our template assigned and return its path
        add_filter( 'template_include', array( $this, 'view_project_template' ), 1000 );

        add_action( 'wp_print_styles', array( $this, 'landing_page_assets' ), 99 );
    }

    public function add_new_template( $posts_templates ) {
        $posts_templates = array_merge( $posts_templates, $this->templates );
        return $posts_templates;
    }

    public function register_project_templates( $atts ) {

        // Create the key used for the themes cache
        $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

        // Retrieve the cache list.
        // If it doesn't exist, or it's empty prepare an array
        $templates = wp_get_theme()->get_page_templates();
        if ( empty( $templates ) ) {
            $templates = array();
        }

        // New cache, therefore remove the old one
        wp_cache_delete( $cache_key, 'themes' );

        // Now add our template to the list of templates by merging our templates
        // with the existing templates array from the cache.
        $templates = array_merge( $templates, $this->templates );

        // Add the modified cache to allow WordPress to pick it up for listing
        // available templates
        wp_cache_add( $cache_key, $templates, 'themes', 1800 );

        return $atts;

    }

    public function view_project_template( $template ) {

        // Only use LP template on actual page (prevents template from being pulled in on things like search results pages)
        if ( ! is_page() ) {
			return $template;
        }

        global $post;
        if ( null !== $post ) {
            $page_template = get_post_meta( $post->ID, '_wp_page_template', true );
            if ( ! isset( $this->templates[ $page_template ] ) ) {
                return $template;
            }
            $file = plugin_dir_path( __FILE__ ) . 'templates/' . $page_template;
            if ( file_exists( $file ) ) {
                return $file;
            }
        }

        return $template;
    }

    public function landing_page_assets() {

        wp_register_style(
            'site-landing',
            plugins_url( '/assets/css/landing.css', __FILE__ )
        );

        if ( $this->is_landing_page() ) {
            wp_deregister_style( 'site-main' );
            wp_enqueue_style( 'site-landing' );
        }
    }

    /**
     * Can be called in theme files: FX_Landing_Page()->is_landing_page()
     */
    public function is_landing_page() {
        foreach ( $this->templates as $template => $label ) {
            if ( is_page_template( $template ) ) {
                return true;
            }
        }
        return false;
    }
}

function FX_Landing_Page() {
    return FX_Landing_Page::instance();
}
add_action( 'plugins_loaded', array( 'FX_Landing_Page', 'instance' ) );
