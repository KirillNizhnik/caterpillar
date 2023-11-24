<?php
/**
 * Plugin Name: WP CAT
 * Plugin URI: http://webpagefx.com
 * Description: Integrates CAT Equipment data
 * Version: 2.4.3
 * Author: WebpageFX
 * Author URI: http://webpagefx.com/
 *
 * Text Domain: catfeed
 *
 * @Package CAT New Feed
 * @category Core
 * @author WebpageFX
 */

if ( ! defined('ABSPATH') ) exit;


final class CAT
{
    /**
     * @var string
     */
    public $version = '2.4.3';

    /**
     * @var string
     */
    public $domain = 'wp-cat';

    /**
     * Plugin instance.
     *
     * @see instance()
     * @type object
     */
    protected static $instance = NULL;

    /**
     * Weather or not to output scripts
     *
     * @type bool
     */
    public $enqueue_scripts = false;


    /**
     * Available Feed Classes
     *
     * @type array
     */
    public $available_classes = array(
		 '406' => 'Machines'
		,'405' => 'Attachments'
		,'402' => 'Power Systems'
		,'486' => 'Site Support Products'
		,'410' => 'Home and Outdoor Power'
	);

	/**
     * Class number to post type relationship
     *
     * @type array
     */
    public $class_post_type_relation = array(
		 '406' => 'cat_new_machine'
		,'405' => 'cat_new_attachment'
		,'402' => 'cat_new_power'
		,'486' => 'cat_new_allied'
		,'410' => 'cat_new_home_power'
	);

    public $default_urls = array(
        'rental_cpc' => array(
            'qa' => 'https://cpchaq.rd.cat.com/ws/xml',
            'production' => 'https://cpc.cat.com/ws/xml',
        )
    );
    
    /**
     * API V2 Credentials
     *
     * Replace CHANNELCODE with actual sales channel code. ie: cpc_d090_cc_client
     */
    public static function fetch_sales_channel_code_user() {
        if( get_option('cat_new_sales_channel_code')) { 
                $code_lower = strtolower(get_option('cat_new_sales_channel_code'));
                $sales_code = 'cpc_' . $code_lower . '_cc_client';
        } else {
                $sales_code = 'cpc_CHANNELCODE_cc_client';
        }
        return $sales_code;
    } 
    public static function fetch_api_auth_secret_key() {
        if( get_option('cat_new_secret_api_code') ) { 
                $secret = get_option('cat_new_secret_api_code');
        }  else {
                $secret = 'API_KEY';
        }
        return $secret;
    } 

    /**
     * Classes selected to be used
     *
     * @type array
     */
    public $classes = array();


    /**
     * URL to this plugin's directory.
     *
     * @type string
     */
    public $plugin_url = '';

    /**
     * Path to this plugin's directory.
     *
     * @type string
     */
    public $plugin_path = '';

    /**
     * URL to ajax directory.
     *
     * @type string
     */
    public $ajax_url = '';

    /**
     * Is the plugin using industries
     *
     * @type bool
     */
    public $usingIndustries;

    /**
     * Is the plugin using applications
     *
     * @type bool
     */
    public $usingApplications;

    /**
     * Factory for returning products
     * @var null
     */
    private $product_factory = null;

    /**
     * Factory for returning families
     * @var null
     */
    private $family_factory = null;

    /**
     * Factory for returning industries
     * @var null
     */
    private $industry_factory = null;

    /**
     * Factory for returning applications
     * @var null
     */
    private $application_factory = null;




    /**
     * Static Singleton Factory Method
     * @return self returns a single instance of our class
     */

    public static function instance()
    {
        if (!isset(self::$instance)) {
            $class_name = __CLASS__;
            self::$instance = new $class_name;
        }
        return self::$instance;
    }

 

    /**
     * Initiate the plugin
     *
     * @return void
     */

    protected function __construct( )
    {
        $this->define_constants();
        $this->define_paths();
        $this->includes();


        // Hooks
        add_action( 'init', array( $this, 'init'), 100 );
        add_action( 'admin_init', array( $this, 'admin_init'), 100 );

        add_action( 'admin_enqueue_scripts', array($this, 'admin_assets'));
        
        add_action( 'wp_enqueue_scripts', array($this, 'frontend_assets'));

	    // $midnight = mktime(0, 0, 0, date('n'), date('j') + 1);
        // wp_schedule_event( $midnight, 'daily', 'cat_used_cron_import');
     }

    /**
     * filtered available classes
     */
    public function get_available_classes($id=null)
    {
        $classes = apply_filters('cat_available_classes', $this->available_classes);
        if (is_null($id))
            return $classes;
        else
            return empty($classes[$id]) ? false : $classes[$id];
    }

    /**
     * filtered available class post type relations
     */
    public function get_class_post_type_relation($id=null)
    {
        $relation = apply_filters('cat_class_post_type_relation', $this->class_post_type_relation);
        if (is_null($id))
            return $relation;
        else
            return empty($relation[$id]) ? false : $relation[$id];
    }


    /**
     * Define constant if not already set
     * @author  woocommerce
     *
     * @param  string $name
     * @param  string|bool $value
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }


    /**
     * What type of request is this?
     * @author  woocommerce
     *
     * @var string $type ajax, frontend or admin
     * @return bool
     */
    private function is_request( $type ) {
        switch ( $type ) {
            case 'admin' :
                return is_admin();
            case 'ajax' :
                return defined( 'DOING_AJAX' );
            case 'cron' :
                return defined( 'DOING_CRON' );
            case 'frontend' :
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
        }
    }


    /**
     * Define Constants
     */
    private function define_constants()
    {
        $this->define( 'DS', DIRECTORY_SEPARATOR );
        $this->define( 'CAT_PLUGIN_FILE', __FILE__ );
        $this->define( 'CAT_VERSION', $this->version );
        $this->define( 'CAT_DOMAIN', $this->domain );
        $this->define( 'CAT_DEBUG', TRUE );

        $this->usingIndustries   = get_option('cat_use_industries');
        $this->usingApplications = get_option('cat_use_applications');
    }


    /**
     * Define plugin paths and urls
     */
    private function define_paths()
    {
        $plugin_folder = dirname(__FILE__);
        $plugin_folder_parent = dirname($plugin_folder);
        $this->plugin_url  = trailingslashit(content_url().'/'.basename($plugin_folder_parent).'/'.basename($plugin_folder));
        $this->plugin_path = trailingslashit(plugin_dir_path( __FILE__ ));
        $this->ajax_url    = admin_url( 'admin-ajax.php', 'relative' );
    }



    /**
     * Include required files
     * @return void
     */

    private function includes()
    {
        include_once $this->plugin_path.'lib/wp-session-manager/wp-session-manager.php';
        
        // Helpers
        include_once $this->plugin_path.'includes/helpers/general-functions.php';
        include_once $this->plugin_path.'includes/helpers/template-functions.php';
        include_once $this->plugin_path.'includes/helpers/image-functions.php';
        include_once $this->plugin_path.'includes/helpers/industry-functions.php';
        include_once $this->plugin_path.'includes/helpers/search-functions.php';
        include_once $this->plugin_path.'includes/helpers/logger.php';

        // core
        include_once $this->plugin_path.'includes/core/autoload.php';
        include_once $this->plugin_path.'includes/core/post-types.php';
        include_once $this->plugin_path.'includes/core/terms.php';
        include_once $this->plugin_path.'includes/core/templates.php';
        include_once $this->plugin_path.'includes/core/rewrite.php';
        include_once $this->plugin_path.'includes/controllers/Image.php';

        if ( $this->is_request( 'admin' ) ) {
            $this->admin_includes();
        }
        if ( $this->is_request( 'ajax' ) ) {
            $this->ajax_includes();
        }
        if ( $this->is_request( 'frontend' ) ) {
            $this->frontend_includes();
        }
        if ( $this->is_request( 'cron' ) ) {
            $this->cron_includes();
        }
    }

    private function frontend_includes()
    {
        include_once $this->plugin_path.'includes/core/breadcrumbs.php';
        include_once $this->plugin_path.'includes/controllers/facet-wp.php';
        include_once $this->plugin_path.'includes/controllers/search.php';

        include_once $this->plugin_path.'includes/shortcodes/cat-family.php';
        include_once $this->plugin_path.'includes/shortcodes/cat-equipment.php';
        include_once $this->plugin_path.'includes/shortcodes/cat-industries.php';
        include_once $this->plugin_path.'includes/shortcodes/cat-featured.php';
         include_once $this->plugin_path.'includes/shortcodes/cat-related.php';
    }

    private function admin_includes()
    {
    	include_once $this->plugin_path.'includes/controllers/admin/alert.php';
        include_once $this->plugin_path.'includes/controllers/admin/assistant.php';
        include_once $this->plugin_path.'includes/controllers/admin/settings.php';
    	include_once $this->plugin_path.'includes/controllers/install.php';

        // admin functionality for post types
        include_once $this->plugin_path.'includes/controllers/admin/new-machines.php';
        include_once $this->plugin_path.'includes/controllers/admin/new-machines-family.php';
        include_once $this->plugin_path.'includes/controllers/admin/new-power-systems.php';
        include_once $this->plugin_path.'includes/controllers/admin/new-power-systems-family.php';
        include_once $this->plugin_path.'includes/controllers/admin/new-attachments.php';
        include_once $this->plugin_path.'includes/controllers/admin/new-attachments-family.php';
        include_once $this->plugin_path.'includes/controllers/admin/new-allied.php';
        include_once $this->plugin_path.'includes/controllers/admin/new-allied-family.php';
        include_once $this->plugin_path.'includes/controllers/admin/new-home-power.php';
        include_once $this->plugin_path.'includes/controllers/admin/new-home-power-family.php';
        include_once $this->plugin_path.'includes/controllers/admin/used-machines.php';
        include_once $this->plugin_path.'includes/controllers/admin/used-machines-family.php';
        include_once $this->plugin_path.'includes/controllers/admin/industries.php';
        include_once $this->plugin_path.'includes/controllers/admin/applications.php';
    }


    private function ajax_includes()
    {
        include_once $this->plugin_path.'includes/controllers/progress.php';
        include_once $this->plugin_path.'includes/controllers/importers/new-feed.php';
        include_once $this->plugin_path.'includes/controllers/importers/used-feed.php';
        include_once $this->plugin_path.'includes/controllers/importers/rental-feed.php';
        include_once $this->plugin_path.'includes/controllers/search.php';
    }

    private function cron_includes()
    {
        include_once $this->plugin_path.'includes/controllers/importers/new-feed.php';
        include_once $this->plugin_path.'includes/controllers/importers/used-feed.php';
        include_once $this->plugin_path.'includes/controllers/importers/rental-feed.php';
    }


    /**
     * Runs when wordpress fires init action
     *
     * @return void
     */

    public function init($hook)
    {
        do_action( 'before_cat_init' );


        $sizes = apply_filters('cat_image_sizes', array(
            'cat_preview' => array(
                'width'   => 429
                ,'height' => 294
                ,'crop'   => true
            )
            ,'cat_header'=> array(
                'width'   => 1600
                ,'height' => 400
                ,'crop'   => true
            )
            ,'cat_thumbnail'=> array(
                'width'   => 97
                ,'height' => 72
                ,'crop'   => true
            )
        ));

        if( ! empty($sizes) )
        {
            foreach($sizes as $name => $dimensions)
            {
                add_image_size( $name, $dimensions['width'], $dimensions['height'], $dimensions['crop'] );
            }
        }


        // Create factories to create new class instances
        $this->product_factory     = new \Cat\Core\Product_Factory;
        $this->family_factory      = new \Cat\Core\Family_Factory;
        $this->industry_factory    = new \Cat\Core\Industry_Factory;
        $this->application_factory = new \Cat\Core\Application_Factory;
        $this->session             = \WP_Session::get_instance();

        do_action( 'after_cat_init' );
    }


    public function admin_init()
    {
    }


    public function frontend_assets()
    {
        //make sure this only goes to select templates and CAT templates
        if(!is_cat() && get_queried_object_id() !== 42 && !is_equipment_search() && get_queried_object_id() !== 9864 ){ //42 is the main equip page - 9864 is equip search refers to /equipment/search
            return;
        }
        // Grab path for includes
        $theme_path = content_url() . '/themes/quinn';
        $plugin_path = trailingslashit( $this->plugin_path );
        
        wp_enqueue_script(
            'cat-search-js'
            ,cat_asset_uri('search.js')
            ,array('jquery', 'underscore')
            ,filemtime( $plugin_path . 'assets/js/search.js' )
            ,true
        );
        
       /* wp_enqueue_script(
            'cat-template-search-js'
            ,$theme_path . '/assets/js/search-machines.js'
            ,array('jquery', 'underscore')
            ,false
            ,true
        ); */
        
        
        wp_enqueue_script(
            'fx_slick'
            ,$theme_path . '/assets/js/plugins/slick.js'
            ,array('jquery')
            ,false
            ,true
        );
        
        
        wp_enqueue_script(
            'fx_accordion-js'
            ,$theme_path . '/assets/js/components/FxAccordion.js'
            ,array('jquery')
            ,false
            ,true
        );
        
        wp_enqueue_script(
            'acf_accordion-js'
            ,$theme_path . '/assets/js/blocks/innerpage/accordion.js'
            ,array('jquery')
            ,false
            ,true
        );
        
        wp_enqueue_script(
            'og_choices'
            ,$theme_path . '/assets/js/plugins/choices.js'
            ,array('jquery')
            ,false
            ,true
        );
        
        wp_enqueue_script(
            'fx_choices'
            ,$theme_path . '/assets/js/components/FxChoices.js'
            ,array('jquery')
            ,false
            ,true
        );
        
        
        
        
        wp_enqueue_style(
                'acf-wysiwyg-css'
                ,$theme_path . '/assets/css/blocks/general/wysiwyg.css'
                ,array()
                ,false
                ,'all'
            );
        wp_enqueue_style(
                'acf-inner-cta-css'
                ,$theme_path . '/assets/css/blocks/innerpage/inner-cta.css'
                ,array()
                ,false
                ,'all'
            );
            wp_enqueue_style(
                'acf-homepage-find-a-location-css'
                ,$theme_path . '/assets/css/blocks/homepage/find-a-location.css'
                ,array()
                ,false
                ,'all'
            );
            
         if(!wp_style_is('fx_cf7')) { 
        wp_enqueue_style(
                'fx_cf7'
                ,$theme_path . '/assets/css/components/choices.css'
                ,array()
                ,false
                ,'all'
            ); 
         }
        wp_enqueue_style(
                'fx_choices_plugin-css'
                ,$theme_path . '/assets/css/plugins/choices.css'
                ,array()
                ,false
                ,'all'
            );
         
        wp_enqueue_style(
                'fx_tabs_accordion'
                ,$theme_path . '/assets/css/components/FxTabsAccordion.css'
                ,array()
                ,false
                ,'all'
            );        
         wp_enqueue_style(
                'fx_innerpage_accordion'
                ,$theme_path . '/assets/css/blocks/innerpage/accordion.css'
                ,array()
                ,false
                ,'all'
            );   

        if (!defined('WP_CAT_DISABLE_TEMPLATE_ASSETS') or !WP_CAT_DISABLE_TEMPLATE_ASSETS)
        {
            wp_enqueue_style(
                'cat-template-css'
                ,cat_asset_uri('template.css')
                ,array()
                ,false
                ,'all'
            );
            wp_enqueue_script(
                'cat-template-js'
                ,cat_asset_uri('template.js')
                ,array('jquery')
                ,filemtime( $plugin_path . 'assets/js/template.js' )
                ,true
            );
        }
        
        //Single template specifics
        if( is_singular('cat_used_machine') || is_singular('cat_new_machine')) {
            wp_enqueue_script(
                'fx_magnific'
                ,$theme_path . '/assets/js/plugins/magnific-custom-scroll.js'
                ,array('jquery')
                ,false
                ,true
            );
            wp_enqueue_script(
                'fx_dcr'
                ,$theme_path . '/assets/js/dcr.js'
                ,array('jquery')
                ,false
                ,true
            );
            
            
            wp_enqueue_script(
                'fx_equipment_singles'
                ,$theme_path . '/assets/js/equipment-specific.js'
                ,array('jquery')
                ,false
                ,true
            );
            
            wp_enqueue_style(
                'fx_equipment_specific'
                ,$theme_path . '/assets/css/equipment-specific.css'
                ,array()
                ,false
                ,"all"
            ); 
            
            
        }
        
        wp_enqueue_style(
                        'fx_slick'
                        ,$theme_path . '/assets/css/plugins/slick.css'
                        ,array()
                        ,false
                        ,"all"
                    );
                    
        wp_localize_script( 
            'cat-search-js', 
            'CSE', 
            array(
                'siteurl' => site_url(), 
                'ajaxurl' => admin_url( 'admin-ajax.php' )
            ) 
        );

        do_action( 'cat_new_feed_enqueue' );
    }


    public function admin_assets($hook)
    {
        $screen = get_current_screen();
        $dev    = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';
        $is_cat = ( strpos($screen->post_type, 'cat_') === 0
                    OR strpos($screen->taxonomy, 'cat_') === 0
                    OR $hook == 'settings_page_cat-settings'
                    OR $hook == 'tools_page_cat_importer'
                  ) ? true : false;


        // echo '<pre>'; var_dump($screen); echo '</pre>';
        // echo '<pre>'; var_dump($hook); echo '</pre>'; die;

        if( ! $is_cat ) return;


        wp_enqueue_media();

        wp_enqueue_style(
            'cat-admin'
            ,$this->plugin_url.'assets/css/admin'.$dev.'.css'
        );

        wp_enqueue_script(
            'cat-new-admin'
            ,$this->plugin_url.'assets/js/admin'.$dev.'.js'
            ,array('jquery', 'underscore', 'jquery-ui-sortable')
            ,'4.9.10'
            ,true
        );

        wp_localize_script('cat-new-admin', 'WPCAT', array( 'pluginUrl' => $this->plugin_url ) );

        /**
         * Add jasmine spec tests scripts if debugging
         */
        /*
        if(defined('SCRIPT_DEBUG') AND SCRIPT_DEBUG OR CAT_DEBUG)
        {
            wp_enqueue_style( 'jasmine-css', $this->plugin_url.'assets/js/tests/lib/jasmine-2.1.3/jasmine.css');
            wp_enqueue_script( 'jasmine', $this->plugin_url.'assets/js/tests/lib/jasmine-2.1.3/jasmine.js');
            wp_enqueue_script( 'jasmine-html', $this->plugin_url.'assets/js/tests/lib/jasmine-2.1.3/jasmine-html.js', array('jasmine'));
            wp_enqueue_script( 'jasmine-boot', $this->plugin_url.'assets/js/tests/lib/jasmine-2.1.3/boot.js', array('jasmine', 'jasmine-html'));
            wp_enqueue_script( 'jasmine-jquery', $this->plugin_url.'assets/js/tests/lib/jasmine-jquery.js', array('jasmine', 'jasmine-html', 'jasmine-boot'));

            wp_enqueue_script( 'jasmine-spec-helper', $this->plugin_url.'assets/js/tests/spec/SpecHelper.js');
            wp_enqueue_script( 'jasmine-spec-wp', $this->plugin_url.'assets/js/tests/spec/wpSpec.js');
            wp_enqueue_script( 'jasmine-spec-importer', $this->plugin_url.'assets/js/tests/spec/importerSpec.js');
            wp_enqueue_script( 'jasmine-spec-industries', $this->plugin_url.'assets/js/tests/spec/industriesSpec.js');
        }
         */

        /**
         * Action for theme specific javascript adding
         */
        do_action( 'cat_new_feed_admin_enqueue' );
    }


    /**
     * See Logger helper
     * @param  string|array $log [description]
     * @return void
     */
    public function log( $log )
    {
        CAT_Log($log);
    }



    /**
     * Return the View Model a product
     * @param  mixed $the_product    item
     * @return [type]          [description]
     */
    public function product( $the_product = false )
    {
        return $this->product_factory->get($the_product);
    }


    /**
     * Return the View Model an industry
     * @param  mixed $the_family    item
     * @return [type]          [description]
     */
    public function family( $the_family = false )
    {
        return $this->family_factory->get($the_family);
    }


    /**
     * Return the View Model an industry
     * @param  mixed $the_industry    item
     * @return [type]          [description]
     */
    public function industry( $the_industry = false )
    {
        return $this->industry_factory->get($the_industry);
    }


    /**
     * Return the View Model an application
     * @param  int $post_id    item
     * @return [type]          [description]
     */
    public function application( $the_app = false )
    {
        return $this->application_factory->get($the_app);
    }



    /**
     * Get the plugin url.
     *
     * @return string
     * @deprecated
     */

    public static function plugin_url()
    {
        $self = self::instance();
        return $self->plugin_url;
    }


    /**
     * Get the plugin path.
     *
     * @return string
     * @deprecated
     */

    public static function plugin_path()
    {
        $self = self::instance();
        return $self->plugin_path;
    }


    /**
     * Get Ajax URL.
     *
     * @return string
     * @deprecated
     */

    public static function ajax_url()
    {
        $self = self::instance();
        return $self->ajax_url;
    }
    
    /**
	 * Check whether a cli command is being invoked.
	 *
	 * @return bool
	 */
	public function is_cli() {
		return class_exists( 'WP_CLI' );
	}



}

/**
 * Returns the main instance of WC to prevent the need to use globals.
 *
 * @since  2.1
 * @return CAT
 */
function CAT() {
    return CAT::instance();
}

/**
 * Helper function for interacting with logger
 *
 * @since  2.4
 * @param mixed $data to log or null to get instance of logger
 * @return Logger or void depending on parameters
 */
function CAT_Log($data=null) {
    $logger = \CAT\Helpers\Logger::instance();

    if (empty($data))
        return $logger;
    else
        $logger->write($data);
}

add_action('plugins_loaded', 'CAT' );
