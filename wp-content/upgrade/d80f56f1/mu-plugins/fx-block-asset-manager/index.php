<?php


/**
 * Plugin Name: FX Block and Asset Manager
 * Version:     0.2.15
 * Description: Allows for easy registration of blocks and FX assets
 * Author:      The WebFX Team
 * Author URI:  https://webfx.com
 * Plugin URI:  https://webfx.com
 * Text Domain: fx
 */


class FX_Block_Asset_Manager
{
	protected static $instance      = null;
	
	public static $plugin_path      = null;
    public static $plugin_path_inc  = null;
    public static $plugin_url       = null;


    /**
     * Singleton instance
     *
     * @return  self
     */
	public static function instance() {
		if( null === self::$instance ) {
			self::$instance = new self();
        }

        return self::$instance;
	}


    /**
     * Constructor
     * 
     * @return  void
     */
    public function __construct()
    {
        $this->define();
        $this->include();
    }


    /**
     * Define common vars
     *
     * @return  void
     */
    public function define(): void
    {
        self::$plugin_path 		= plugin_dir_path( __FILE__ );
        self::$plugin_url 		= plugin_dir_url( __FILE__ );
        self::$plugin_path_inc 	= sprintf( '%sincludes/', self::$plugin_path );
    }


    /**
     * Include required files
     * 
     *
     * @return  void
     */
    private function include(): void
    {
        // load functions to internally support plugin
        require_once( self::$plugin_path_inc . '/helper-functions.php' );

        // load functions to help theme and other plugins interact with FX BAM
		require_once( self::$plugin_path_inc . '/public-functions.php' );
        
		require_once( self::$plugin_path_inc . '/classes/class-fx-register-blocks.php' );
        require_once( self::$plugin_path_inc . '/classes/class-fx-asset-minifier.php' );
		require_once( self::$plugin_path_inc . '/classes/class-fx-assets.php' );
		require_once( self::$plugin_path_inc . '/classes/class-fx-parse-block-assets.php' );
    }

}



/**
 * Returns main instance of FX_Block_Asset_Manager to prevent need of global usage
 * @return FX_Block_Asset_Manager
 */
function FX_Block_Asset_Manager() {
	return FX_Block_Asset_Manager::instance();
}

FX_Block_Asset_Manager();
