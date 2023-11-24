<?php
/**
 * Plugin Name: FX Popular Posts
 * Plugin URI: https://www.webfx.com
 * Description: Track and display the most viewed posts.
 * Version: 1.0
 * Author: The WebFX Team
 * Author URI: https://www.webfx.com
 */
class FX_Popular_Posts {

    protected static $instance = null;

	public static $post_meta_key = 'view_count';

    public $plugin_url;
    public $plugin_path;

    public static function instance() {
        if ( ! isset( self::$instance ) ) {
            $class_name     = __CLASS__;
            self::$instance = new $class_name();
        }
        return self::$instance;
    }

    public function __construct() {
        // Defines
        $this->plugin_url  = trailingslashit( plugins_url( '/', __FILE__ ) );
        $this->plugin_path = trailingslashit( plugin_dir_path( __FILE__ ) );

        // Admin
        add_action( 'admin_init', array( $this, 'admin_cols' ) );

        // Front End
        add_action( 'template_redirect', array( $this, 'frontend' ) );

        // AJAX Endpoints
        add_action( 'wp_ajax_update_view_count', array( $this, 'update_view_count' ) );
        add_action( 'wp_ajax_nopriv_update_view_count', array( $this, 'update_view_count' ) );

        // Pull in helper functions
        include_once $this->plugin_path . 'includes/widget.php';
    }

    public function admin_cols() {
        // Admin modifications
        add_filter( 'manage_edit-post_columns', array( $this, 'add_column_head' ) );
        add_action( 'manage_post_posts_custom_column', array( $this, 'manage_column_content' ) );
        add_filter( 'manage_edit-post_sortable_columns', array( $this, 'sortable_columns' ) );
    }

    public function frontend() {
        if ( is_singular( 'post' ) ) {
            add_action( 'wp_enqueue_scripts', array( $this, 'frontend_assets' ) );
        }
    }

    public function frontend_assets() {
        $version = filemtime( $this->plugin_path . 'assets/popular-posts.js' );
        wp_enqueue_script(
            'fx-popular-posts',
            $this->plugin_url . 'assets/popular-posts.js',
            array( 'jquery' ),
            $version,
            true
        );
        global $post;
        wp_localize_script(
        	'fx-popular-posts',
        	'FXPP',
        	array(
        		'ajaxurl'  => admin_url( 'admin-ajax.php' ),
                'pid'      => $post->ID,
                'posttype' => get_post_type( $post->ID ),
        	)
        );
    }

    public function add_column_head( $columns ) {
        $screen = get_current_screen();
        if ( 'post' !== $screen->post_type ) {
            return $columns;
        }
        $new_cols = array();
        foreach ( $columns as $key => $title ) {
            $new_cols[ $key ] = $title;
            if ( 'date' === $key ) {
                $new_cols['view_count'] = 'Views';
            }
        }
        return $new_cols;
    }

    public function manage_column_content( $name ) {
        if ( 'view_count' === $name ) {
            global $post;
            echo get_post_meta( $post->ID, self::$post_meta_key, true );
        }
    }

    public function sortable_columns( $cols ) {
	    $cols['view_count'] = self::$post_meta_key;
	    return $cols;
	}

    public function update_view_count() {
        $post_type  = $_POST['post_type'];
        $post_id    = $_POST['post_id'];
        $view_count = get_post_meta( $post_id, self::$post_meta_key, true );
        update_post_meta( $post_id, self::$post_meta_key, $view_count + 1 );
        wp_die();
        exit;
    }
}

function FX_Popular_Posts() {
    return FX_Popular_Posts::instance();
}
FX_Popular_Posts();
