<?php
if ( ! defined('ABSPATH') ) exit;

class CM_Install {

    protected static $_instance;

    public static function instance() {
        if ( ! isset( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    protected function __construct() {
        register_activation_hook( WPCM_PLUGIN_FILE, array( $this, 'install' ) );
        register_deactivation_hook( WPCM_PLUGIN_FILE, array( $this, 'uninstall' ) );
        add_action( 'admin_init', array( $this, 'maybe_install' ), 5 );
        add_action( 'admin_init', array( $this, 'check_version' ), 5 );
    }

    /**
     * On activation, copies over template and ACF files and saves WPCM version to DB
     */
    public function install( $new = true ) {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        global $wpdb;
        CM_Post_Types::register_post_types();
        CM_Post_Types::register_taxonomies();
         // include api endpoint register before url flush
        CM_API_Server::instance()->create_api_end_point();
        
        $this->copy_template_files();
        $this->copy_acf_json();
        $this->create_wpcm_table();
        update_option( 'wpcm_db_version', WPCM_VERSION );
        flush_rewrite_rules();
        do_action( 'wpcm_installed' );
    }

    public function uninstall() {
        delete_option( 'wpcm_db_version' );
    }

    /**
     * Creates custom db table for zipcode/query results
     */
    public function create_wpcm_table() {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE `{$wpdb->base_prefix}wpcm_queries` (
          id int NOT NULL AUTO_INCREMENT,
          userquery varchar(255) NOT NULL,
          latitude varchar(255) NOT NULL,
          longitude varchar(255) NOT NULL,
          created_at datetime NOT NULL,
          updated_at datetime NOT NULL,
          last_queried datetime NOT NULL,
          total_queries varchar(255) NOT NULL,
          PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);
    }

    /**
     * Copy template files to active theme folder
     */
    public function copy_template_files() {
        $theme_folder = get_stylesheet_directory();
        $company_folder = $theme_folder . "/company";
        if ( ! is_dir( $company_folder ) ) {
            $this->copy_dir( WPCM()->plugin_path . 'templates', $company_folder, array( 'admin' ) );
        }
    }

    /**
     * Copy ACF JSON
     */
    public function copy_acf_json() {
        $theme_folder = get_stylesheet_directory();
        $acf_folder = $theme_folder . "/acf-json";
        if ( ! is_dir( $acf_folder ) ) {
            $this->copy_dir( WPCM()->plugin_path . 'acf-json', $acf_folder, array( 'admin' ) );
        }
    }

    /**
     * Custom function to copy a directory, since WordPress filesystem
     * doesn't seem to load soon enough
     */
    protected function copy_dir( $from, $to, $exclude = array() ) {

        $from = trailingslashit( $from );
        $to = trailingslashit( $to );
        if ( ! is_dir( $from ) ) {
            error_log( "Source directory must exist in copy_dir" );
            return false;
        }

        if ( ! is_dir( $to ) ) {
            mkdir( $to, 0755, 'recursive' );
        }

        $fromdir = opendir( $from );
        while ( $file = readdir( $fromdir ) ) {
            if ( $file != "." and $file != ".." and ! in_array( $file, $exclude ) ) {
                if ( is_file( $from . $file ) ) {
                    copy( $from . $file, $to . $file );
                }
                if ( is_dir( $from . $file ) ) {
                    $this->copy_dir( $from . $file, $to . $file );
                }
            }
        }

        closedir( $fromdir );
    }

    /**
     * Maybe run installation (for mu-plugins integration)
     */
    public function maybe_install() {
        $is_mu_dir    =  strpos( WPCM_PLUGIN_FILE, ABSPATH . MUPLUGINDIR ) === 0;
        $mu_installed = get_option( 'wpcm_mu' );
        if( $is_mu_dir AND ! $mu_installed ) {
            update_option( 'wpcm_mu', true );
            $this->install();
        }
    }

    /**
     * Runs any database updates that need run
     */
    public function check_version() {
        $version = get_option( 'wpcm_db_version' );
        if( $version != WPCM_VERSION ) {
            $this->install( empty( $version ) );
            do_action( 'wpcm_updated' );
        }
    }
}

CM_Install::instance();
