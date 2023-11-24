<?php
/**
 * Plugin Name: Company Manager
 * Plugin URI: https://www.webfx.com
 * Description: Manage company locations.
 * Version: 3.2
 * Author: WebFX
 * Author URI: https://www.webfx.com
 *
 * Text Domain: wpcm
 */
if ( ! defined('ABSPATH') ) exit;

final class Company_Manager {

    public $version = '3.1';
    protected static $_instance;

    public static function instance() {
        if ( ! isset( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    protected function __construct() {
        $this->define_constants();
        $this->define_paths();
        add_action( 'plugins_loaded', array( $this, 'setup' ) );
    }

    public function setup() {

        /**
         * ACF Check
         */
        if ( ! function_exists( 'acf_add_local_field_group' ) || version_compare( ACF_VERSION, '5.8.6', '<') ) {
            add_action( 'admin_notices', array( $this, 'install_acf_plugin_notice' ) );
        } else {
            $this->includes();
            add_action( 'widgets_init', array( $this, 'widgets_init' ), 100 );
            add_action( 'acf/init', function() {
                acf_update_setting( 'google_api_key', GMAPS_API_KEY );
            });

            add_action( 'init', array( $this, 'init'), 100 );
        }
    }

    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

    private function define_constants() {
        $this->define( 'WPCM_PLUGIN_FILE', __FILE__ );
        $this->define( 'WPCM_VERSION', $this->version );
        $maps_api_key = get_option( 'options_maps_api_key' );
        $geocoding_api_key = get_option( 'options_geocoding_api_key' );
        $cm_limit_search = get_option( 'options_cm_limit_search' );
        $cm_enable_search_save = get_option( 'options_cm_enable_search_save' );
        // If separate geocoding API key doesn't exist, use the maps API key value
        $geocoding_api_key = $geocoding_api_key ? $geocoding_api_key : $maps_api_key;
        $this->define( 'GMAPS_API_KEY', $maps_api_key );
        $this->define( 'GEOCODE_API_KEY', $geocoding_api_key );
        $this->define( 'CM_LIMIT_SEARCH', $cm_limit_search );
        $this->define( 'CM_SAVE_SEARCH', $cm_enable_search_save );
    }

    private function define_paths() {
        $this->plugin_url  = trailingslashit( plugins_url( '/', __FILE__ ) );
        $this->plugin_path = trailingslashit( plugin_dir_path( __FILE__ ) );
        $this->ajax_url    = admin_url( 'admin-ajax.php', 'relative' );
    }

    private function includes() {

        // Global
        include_once $this->plugin_path . 'includes/cm-location.php';
        include_once $this->plugin_path . 'includes/cm-helper-functions.php';
        include_once $this->plugin_path . 'includes/cm-post-types.php';
        include_once $this->plugin_path . 'includes/cm-api.php';
        include_once $this->plugin_path . 'includes/cm-rep.php';
        include_once $this->plugin_path . '/includes/abstracts/cm-abstract-factory.php';
        include_once $this->plugin_path . '/includes/abstracts/cm-abstract-post-type.php';
        include_once $this->plugin_path.'includes/cm-rep-factory.php';
        include_once $this->plugin_path.'includes/api/cm-api-server.php';
        include_once $this->plugin_path.'includes/api/cm-api-resource-interface.php';
        include_once $this->plugin_path.'includes/api/cm-api-rep.php';
        
        // Widgets
        include_once $this->plugin_path . 'includes/widgets/cm-widget-closest-location.php';
        include_once $this->plugin_path . 'includes/widgets/cm-widget-closest-rep.php';
        include_once $this->plugin_path . 'includes/widgets/cm-widget-search-by-zip.php';

        // Admin
        if ( is_admin() ) {
            include_once $this->plugin_path . 'includes/cm-install.php';
            include_once $this->plugin_path . 'includes/admin/cm-admin-location.php';
            include_once $this->plugin_path . 'includes/admin/cm-admin-settings.php';
            include_once $this->plugin_path.'includes/admin/cm-admin-rep.php';
        }

        // Front-End
        if ( ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) ) {
            include_once $this->plugin_path . 'includes/cm-template.php';
            $shortcodes = $this->plugin_path . 'includes/shortcodes/'; 
            foreach ( glob( $shortcodes . '*.php' ) as $shortcode ) {
                include_once( $shortcode );
            }
        }
    }

    public function widgets_init() {
        register_widget( 'CM_Widget_Closest_Location' );
        register_widget( 'CM_Widget_Search_By_Zip' );
        register_widget( 'CM_Widget_Closest_Rep' );
    }

    public function install_acf_plugin_notice() {
        $plugin_directory = 'advanced-custom-fields';
        $plugin_director_pro = 'advanced-custom-fields-pro';
        $plugin_name = 'acf';
        if ( ! file_exists( WP_PLUGIN_DIR . '/' . $plugin_directory . '/' . $plugin_name . '.php') || ! file_exists( WP_PLUGIN_DIR . '/' . $plugin_directory_pro . '/' . $plugin_name . '.php') || version_compare( ACF_VERSION, '5.8.6', '<') ) {
            // Main plugin file doesn't exist, so it needs to be installed
            $content = 'FX Company Manager requires an activated Advanced Custom Fields plugin and later than version 5.8.6. Unlimited license key can be found in LastPass.';
        }
        echo $this->generate_notice_html( 'error', $content );
    }

    public function generate_notice_html( $class = 'error', $content ) {
        $style = ' style="background-color:#f2dede;"';
        ob_start();
    ?>
        <div class="<?php echo $class; ?> notice" id="fx_wpcm_notice"<?php echo $class === 'error' ? $style : ''; ?>>
            <p><?php echo $content; ?></p>
        </div>
    <?php
        return ob_get_clean();
    }
    
    /**
     * Runs when wordpress fires init action
     *
     * @return void
     */

    public function init($hook)
    {
        if( class_exists( 'CM_Rep_Factory' ) ) {
            $this->rep_factory = new CM_Rep_Factory;
        }
    }
    
    /**
     * Return the Model for a location
     * @param  mixed $the_location    item
     * @return [type]          [description]
     */
    public function rep( $the_rep = false )
    {
        return $this->rep_factory->get($the_rep);
    }
    
     

    
    
}

/**
 * Returns the main instance of Company_Manager to prevent the need to use globals.
 * @since  1.0
 * @return WPCM
 */
function WPCM() {
    return Company_Manager::instance();
}

WPCM();
