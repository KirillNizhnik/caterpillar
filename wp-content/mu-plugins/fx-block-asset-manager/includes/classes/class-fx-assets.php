<?php



defined( 'ABSPATH' ) || exit;



/**
 * FX Assets class
 * Contains logic for enqueueing styles and scripts with advanced options
 * Documentation: https://app.getguru.com/card/ceEjzyKi/FX-Assets
 */


final class FX_Assets
{
    protected static $instance      = null;
    
    private $plugin_style_deps      = [];
    private $plugin_script_deps     = [];
    private $async_style_handles    = [];
        
    public $wp_version              = null;
    public $theme_directory_uri     = '';
    public $theme_directory_path    = '';
    public $site_url                = '';
    public $site_path               = '';


	
    /**
     * Static Singleton Factory Method
     * @return self returns a single instance of our class
     */
    public static function instance() 
    {
        if( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }



    protected function __construct()
    {
        $this->define();
        $this->add_wp_hooks();
	}


    /**
     * Define common expressions/references
     *
     * @return  void
     */
    public function define()
    {
        $this->theme_directory_uri  = get_stylesheet_directory_uri();
        $this->theme_directory_path = get_stylesheet_directory();
        $this->site_url             = site_url();
        $this->site_path            = untrailingslashit( ABSPATH );
    }


    /**
     * Hooks into WordPress
     *
     * @return  void
     */
    public function add_wp_hooks()
    {
        add_action( 'wp_enqueue_scripts',   [ $this, 'add_custom_plugin_script_dependencies' ], 101 );
        add_action( 'wp_enqueue_scripts',   [ $this, 'add_custom_plugin_style_dependencies' ], 101 );
        add_action( 'wp_head',              [ $this, 'add_preload_links' ], 1 );
        add_filter( 'script_loader_tag',    [ $this, 'maybe_modify_script_tag' ], 99, 2 );
        add_filter( 'style_loader_tag',     [ $this, 'intercept_async_stylesheets' ], 99, 2 );
    }


    /**
     * Check if stylesheet should load asynchronously
     *
     * @param	string	$tag        Link element
     * @param   string  $handle     Stylesheet handle
     * 
     * @return	string              Link element
     */
    public function intercept_async_stylesheets( string $tag, string $handle ): string
    {
        // should we async load style tag?
        if( in_array( $handle, $this->async_style_handles ) ) {
            $tag = str_replace(
                "media='all' />",
                "media='print' onload='this.media=\"all\"' />",
                $tag
            );
        }

        return $tag;
    }



    /**
     * Conditionally manipulate script tags before they print
     *
     * @param	string	$tag        HTML <script> tag
     * @param   string  $handle     Asset handle
     * 
     * @return	string              Updated HTML <script> tag
     */
    public function maybe_modify_script_tag( string $tag, string $handle ): string
    {
        $wp_scripts = wp_scripts();

        // handle async|defer; use only one or the other
        foreach( [ 'async', 'defer' ] as $attr ) {
            
            if( true === $wp_scripts->get_data( $handle, $attr ) ) {
                if( !preg_match( ":\s$attr(=|>|\s):", $tag ) ) {
                    $tag = preg_replace( ':(?=></script>):', " $attr", $tag, 1 );
                }
                
                break;
            }
        }

        // handle module attribute
        if( true === $wp_scripts->get_data( $handle, 'module', true ) ) {
            $tag = preg_replace( "/type=['\"]text\/javascript['\"]/", 'type="module"', $tag );
        }

        return $tag;
    }


    
    /**
     * Add custom stylesheet dependencies to plugins
     *
     * @return  void
     */
    public function add_custom_plugin_style_dependencies(): void
    {
        global $wp_styles;

        // loop through registered stylesheets
        foreach( $wp_styles->registered as $handle_plugin => &$registered ) {

            // loop through registered custom handles to find matches
            foreach( $this->plugin_style_deps as $handle_target => $additional_deps ) {

                // if match, add dependencies
                if( $handle_plugin === $handle_target ) {
                    $dependencies = array_merge( $registered->deps, $additional_deps );

                    // "reload" dependencies
                    $registered->deps = $dependencies;
                }
            }
        }
    }


    
    /**
     * Add custom script dependencies to plugins
     *
     * @return  void
     */
    public function add_custom_plugin_script_dependencies(): void
    {
        global $wp_scripts;

        // loop through registered stylesheets
        foreach( $wp_scripts->registered as $handle_plugin => &$registered ) {

            // loop through registered custom handles to find matches
            foreach( $this->plugin_script_deps as $handle_target => $additional_deps ) {

                // if match, add dependencies
                if( $handle_plugin === $handle_target ) {
                    $dependencies = array_merge( $registered->deps, $additional_deps );

                    // "reload" dependencies
                    $registered->deps = $dependencies;
                }
            }
        }
    }



    /**
     * Registers custom plugin stylesheet dependencies for later usage
     *
     * @param	string	$plugin_handle  Plugin asset handle
     * @param   string  $handle_to_add  Asset handle to add to plugin's dependencies
     * 
     * @return  void
     */
    public function register_custom_plugin_style_dependency( string $plugin_handle, string $handle_to_add ): void
    {
        if( !isset( $this->plugin_style_deps[ $plugin_handle ] ) ) {
            $this->plugin_style_deps[ $plugin_handle ] = [];
        }

        $dependency_handles     = $this->plugin_style_deps[ $plugin_handle ];
        $dependency_handles[]   = $handle_to_add;
        $dependency_handles     = array_unique( $dependency_handles );

        $this->plugin_style_deps[ $plugin_handle ] = $dependency_handles;
    }



    /**
     * Registers custom plugin script dependencies for later usage
     *
     * @param	string	$plugin_handle  Plugin asset handle
     * @param   string  $handle_to_add  Asset handle to add to plugin's dependencies
     * 
     * @return  void
     */
    public function register_custom_plugin_script_dependency( string $plugin_handle, string $handle_to_add ): void
    {
        if( !isset( $this->plugin_script_deps[ $plugin_handle ] ) ) {
            $this->plugin_script_deps[ $plugin_handle ] = [];
        }

        $dependency_handles     = $this->plugin_script_deps[ $plugin_handle ];
        $dependency_handles[]   = $handle_to_add;
        $dependency_handles     = array_unique( $dependency_handles );

        $this->plugin_script_deps[ $plugin_handle ] = $dependency_handles;
    }    



    /**
     * Add stylesheet
     *
     * @param	array   $args
     * @return  mixed   True, if successful; otherwise WP_Error
     */
    public function add_stylesheet( array $base_args )
    {
        $defaults = [

            // required
            'handle'        => '',
            'src'           => '',

            // optional
            'path'          => '',
            'dependencies'  => [],

            // maybe add head links
            'preload'       => false,
            // 'prefetch'   => false,  // deprecated as of 0.2.6

            // value for "as" attribute (font, image, etc.)
            'type'          => 'style',

            // echo stylesheet instead of using <link /> tag
            'inline'        => false,

            // if inlining, wp_print_styles priority
            'priority'      => 0,

            // true, to enqueue; false, to register
            'enqueue'       => false,

            // async (@see intercept_async_styles() )
            'async'         => false,
            
            // media (e.g. all, print, etc)
            'media'         => 'all',
        ];

        $args   = wp_parse_args( $base_args, $defaults );
        $error  = new WP_Error();

        $handle = $args['handle'];
        if( empty( $handle ) ) {
            $error->add_data( 'Handle is required.' );
        }

        $src = $args['src'];
        if( empty( $src ) ) {
            $error->add_data( 'Source file is required.' );
        }

        if( $error->has_errors() ) {
            return $error;
        }

        $this->handle_style_deprecations( $args, __FUNCTION__ );

        // preloading handled in $this->add_preload_links
        $attributes['preload']  = boolval( $args['preload'] );

        // are we inlining script?
        $inline = boolval( $args['inline'] );
        $priority = intval( $args['priority'] );

        // are we enqueuing/registering script?
        $enqueue = boolval( $args['enqueue'] );     
        
        // should style be loaded async?
        $async = boolval( $args['async'] );
        if( $async ) {
            $this->async_style_handles[] = $handle;
        }
        
        // media type
        $media = $args['media'];

        // ensure file path
        if( empty( $path ) ) {
            $path = $this->get_file_path_from_src( $args['src'] );
        }

        // check if child theme has same file, which'll override parent theme file
        if( fx_bam_child_theme_is_active() ) {
            $child_path = fx_bam_maybe_get_child_theme_file_path( $path );

            if( $path !== $child_path ) {
                $path = $child_path;
                $args['src'] = fx_bam_get_file_src_from_path( $path );
            }
        }

        // get versioning for cache-busting
        if( is_file( $path ) ) {
            $version = filemtime( $path );
        } else {
            $version = $this->get_wp_version();
        }

        // use empty array versus other "empty" values like false, null, etc 
        $dependencies = array_filter( (array)$args['dependencies'] );
        
        // are we printing a style right now?
        if( $inline ) {

            // if stylesheet has dependencies, inline dependencies as well
            if( !empty( $dependencies ) ) {
                $this->inline_css_dependencies( $dependencies, $priority );
            }
            
            $this->print_inline_style_tag( $handle, $src, $path, $priority );

        // otherwise, let's enqueue the style            
        } else {

            if( $enqueue ) {
                wp_enqueue_style(
                    $handle,
                    $src,
                    $dependencies,
                    $version,
                    $media
                );
            } else {
                wp_register_style(
                    $handle,
                    $src,
                    $dependencies,
                    $version,
                    $media
                );                
            }

            // add attributes as script data
            foreach( $attributes as $prop => $value ) {
                wp_style_add_data( $handle, $prop, $value );
            }            
        }

        return true;        
    }

    

    /**
     * Add script
     *
     * @param	array 	$args   
     * @return  mixed           True, if successful; otherwise WP_Error
     */
    public function add_script( array $base_args )
    {
        $defaults = [
            
            // required
            'handle'        => '',
            'src'           => '',
            
            // optional
            'path'          => '',
            'dependencies'  => [],
            'in_footer'     => true,

            // script attributes
            'async'         => false,
            'defer'         => false,
            'module'        => false,
            
            // maybe add head links?
            'preload'       => false,
            // 'prefetch'   => false, // deprecated as of 0.2.6
            
            // echo script instead of enqueuing
            'inline'        => false,

            // if inlining, wp_print_scripts priority
            'priority'      => 0,

            /**
             * If attribute is set to false, asset won't be minified
             * 
             * Inlined assets are automatically minified through FX_Asset_Minifier class. Already-minified assets can 
             * result in issues when asset is minified again, so this attribute can prevent redundant minification
             * 
             * If asset is NOT inline, then this attribute has no effect. Source file will be used instead, which is 
             * minified by WP Rocket (which has its own UI for excluding assets from minification)
             */
            'minify'        => true,

            // true, to enqueue; false to register
            'enqueue'       => false,

            // assorted tag attributes //  — combine with script attributes above?
            'attrs'         => []
        ];

        $args   = wp_parse_args( $base_args, $defaults );
        $error  = new WP_Error();

        $handle = $args['handle'];
        if( empty( $handle ) ) {
            $error->add_data( 'Handle is required.' );
        }

        $src = $args['src'];
        if( empty( $src ) ) {
            $error->add_data( 'Source file is required.' );
        }
        
        if( $error->has_errors() ) {
            return $error;
        }

        $this->handle_script_deprecations( $args, __FUNCTION__ );

        // ensure file path
        if( empty( $path ) ) {
            $path = $this->get_file_path_from_src( $args['src'] );
        }

        // check if child theme has same file, which'll override parent theme file
        if( fx_bam_child_theme_is_active() ) {
            $child_path = fx_bam_maybe_get_child_theme_file_path( $path );

            if( $path !== $child_path ) {
                $path = $child_path;
                $args['src'] = fx_bam_get_file_src_from_path( $path );
            }
        }
        
        // get versioning for cache-busting
        if( is_file( $path ) ) {
            $version = filemtime( $path );
        } else {
            $version = $this->get_wp_version();
        }
        
        // use empty array versus other "empty" values like false, null, etc 
        $dependencies = array_filter( (array)$args['dependencies'] );
        
        $in_footer = $args['in_footer'];
        
        // attributes should be array
        $attributes = array_filter( (array)$args['attrs'] );
        
        // ensure boolean values are bool
        $attributes['async']    = boolval( $args['async'] );
        $attributes['defer']    = boolval( $args['defer'] );
        $attributes['module']   = boolval( $args['module'] );
        $attributes['minify']   = boolval( $args['minify'] );

        // preloading handled in $this->add_preload_links
        $attributes['preload']  = boolval( $args['preload'] );

        // are we inlining script?
        $inline = boolval( $args['inline'] );
        $priority = intval( $args['priority'] );

        // are we enqueuing/registering script?
        $enqueue = boolval( $args['enqueue'] );

        // if using both defer and async, just use defer
        if( $attributes['defer'] && $attributes['async'] ) {
            $attributes['async'] = false;
        }

        /**
         * Maybe exclude asset from minification. Note that we're applying this to ALL assets regardless of whether the 
         * asset is explicitly inlined. Assets for the first block and dependencies for inlined assets are 
         * automatically inlined, so this ensures that wherever the asset is used, it's not minified
         */
        if( !$attributes['minify'] ) {
            add_filter( 'fx_bam_minify_inline_js', function( $should_minify, $asset_handle ) use ( $handle ) {
                if( $handle === $asset_handle ) {
                    $should_minify = false;
                }

                return $should_minify;
            }, 10, 2 );
        }

        // at this point, let's ditch falsey attributes
        $attributes = array_filter( $attributes );

        // are we printing a script right now?
        if( $inline ) {

            // if script has dependencies, inline dependencies as well
            if( !empty( $dependencies ) ) {
                $this->inline_js_dependencies( $dependencies, $priority ); 
            }
            
            $tag_args = [
                'id' => esc_attr( sprintf( '%s-js', $handle ) ),
            ];
            $tag_args = array_merge( $tag_args, $attributes );
            
            $this->print_inline_script_tag( $handle, $src, $tag_args, $path, $priority );
            
        // otherwise, let's enqueue the script            
        } else {
            if( $enqueue ) {
                wp_enqueue_script( 
                    $handle, 
                    $src, 
                    $dependencies, 
                    $version,
                    $in_footer
                );
            } else {
                wp_register_script( 
                    $handle, 
                    $src, 
                    $dependencies, 
                    $version,
                    $in_footer
                );                
            }

            // add attributes as script data
            foreach( $attributes as $prop => $value ) {
                wp_script_add_data( $handle, $prop, $value );
            }
        }

        return true;
    }
    
    
    /**
     * Print an inline stylesheet and mark stylesheet as enqueued so WordPress doesn't add it again.
     * 
     * @param 	string	$handle		Handle of registered CSS asset
     * @param 	string 	$src 		Source of CSS asset
     * @param 	string 	$file_path 	Absolute file path to the stylesheet. If not included,
     *          					the path will be determined from $src
	 *
	 * @return 	mixed 				True on success; false if encountered error
	 * 
     */
    public function print_inline_style_tag( $handle, $src, $file_path = false, $priority = 0 ) 
    {
        $hook = function() use ( $handle, $src, $file_path ) {
            global $wp_styles;
            
            if( false === $src ) {
                return;
            }
            
            // If no path provided, get file path from src
            if( !$file_path ) {
                $file_path = $this->get_file_path_from_src( $src );
            }
            
            if( !in_array( $handle, $wp_styles->done ) ) {
                if( is_file( $file_path ) ) {
                    $content = file_get_contents( $file_path );

                    // allow plugins/theme to prevent minification of asset
                    if( apply_filters( 'fx_bam_minify_inline_css', true, $handle ) ) {
                        $content = fx_asset_minify_css( $content );
                    }

                    printf( 
                        '<style id="%s-css" type="text/css">%s</style>', 
                        $handle,
                        $content
                    );
                    
                    // Let WP know this stylesheet has already been enqueued
                    $wp_styles->done[] = $handle;

                } else {
                    $error = new WP_Error();
                    $error->add_data( 'Invalid source file: ' . $file_path );

                    return $error;
                }
            }
            
            return true;
        };

        add_action( 'wp_print_styles', $hook, $priority );
    }
		

    /**
     * Print an inline script and mark script as enqueued so WordPress doesn't add it again.
     * 
     * @param 	string	$handle		Handle of registered JS asset
     * @param 	string 	$src 		Source of JS asset
     * @param 	array 	$tag_args 	Attributes to be added to the script tag
     * @param 	string 	$file_path 	Absolute file path to the script. If not included,
     *          					the path will be determined from $src
	 *
	 * @return 	mixed 				True on success; false if encountered error
	 * 
     */
    public function print_inline_script_tag( $handle, $src, $tag_args = [], $file_path = false, $priority = 10 ) 
    {
        $hook = function() use ( $handle, $src, $tag_args, $file_path ) {
            global $wp_scripts;
            
            // If no source, skip
            if( false === $src ) {
                return;
            }
                
            if( !$file_path ) {
                $file_path = $this->get_file_path_from_src( $src );
            }
            
            if( !in_array( $handle, $wp_scripts->done ) ) {
                if( is_file( $file_path ) ) {
                    $content = file_get_contents( $file_path );

                    // minify JS before printing
                    if( apply_filters( 'fx_bam_minify_inline_js', true, $handle ) ) {
                        $content = fx_asset_minify_js( $content );
                    }

                    echo wp_print_inline_script_tag( $content, $tag_args );
                    
                    // Let WP know this script has already been enqueued
                    $wp_scripts->done[] = $handle;

                } else {
                    $error = new WP_Error();
                    $error->add_data( 'Invalid source file: ' . $file_path );

                    return $error;
                }
            }

            return true;
        };

        add_action( 'wp_print_scripts', $hook, $priority );
    }

    
    /** 
     * Accepts an array of CSS dependency handles and recursively prints them and their dependencies inline.
     * 
     * @param   array   $deps       CSS dependency handles
     * @param   int     $priority   Asset priority
     * 
     * @return  void
     */
    public function inline_css_dependencies( $deps, int $priority = 0 ): void 
    {
        global $wp_styles;

        $registered_styles = $wp_styles->registered;

        foreach( $deps as $dep ) {
            if( isset( $registered_styles[ $dep ] ) ) {
                $registered_style = $registered_styles[ $dep ];
                
                // if handle has dependencies, inline those dependencies too
                $next_level_deps = $registered_style->deps;
                $this->inline_css_dependencies( $next_level_deps );
                
                // print inline dependency (after sub-dependencies have been output from above)
                $this->print_inline_style_tag( $registered_style->handle, $registered_style->src, false, $priority );
            }
        }
    }
    

    /** 
     * Accepts an array of JS dependency handles and recursively prints them and their dependencies inline.
     * 
     * @param   array   $deps       JS dependency handles
     * @param   int     $priority   Asset priority
     * 
     * @return  void
     */
    public function inline_js_dependencies( $deps, int $priority = 10 ): void 
    {
        global $wp_scripts;

        $registered_scripts = $wp_scripts->registered;
        
        foreach( $deps as $dep ) {
            if( isset( $registered_scripts[ $dep ] ) ) {
                $registered_script = $registered_scripts[$dep];
    
                // if handle has dependencies, inline those dependencies too
                $next_level_deps = $registered_script->deps;
                $this->inline_js_dependencies( $next_level_deps, $priority - 1 );
                
                $tag_args = [
                    'id' => esc_attr( sprintf( '%s-js', $registered_script->handle ) ),
                ];
                
                // print inline dependency (after sub-dependencies have been output from above)
                $this->print_inline_script_tag( $registered_script->handle, $registered_script->src, $tag_args, false, $priority );
            }
        }
    }
    

    /**
     * Get current WP core version
     *
     * @return  string  Current WP core version
     */
    public function get_wp_version(): string
    {
        if( empty( $this->wp_version ) ) {
            require( ABSPATH . WPINC . '/version.php' );

            $this->wp_version = $wp_version;   
        }

        return $this->wp_version;
    }
	

    
    /**
     * Get absolute file path based on argument
     *
     * @param	string	$src    File source path
     * @return  mixed           String, if valid file; otherwise, false
     */
    public function get_file_path_from_src( string $src )
    {
        // remove URL for source
        if( false !== strpos( $src, $this->site_url ) ) {
            $src = substr_replace( $src, '', 0, strlen( $this->site_url ) );
        }
        
        return untrailingslashit( ABSPATH ) . $src;
    }


    /**
     * Create and add preload links to <head> based on script attributes
     *
     * @return  void
     */
    public function add_preload_links() 
    {
        global $wp_scripts;
        foreach( $wp_scripts->queue as $handle ) {

            // handle marked to preload?
            if( true === wp_scripts()->get_data( $handle, 'preload' ) ) {
                $dependency = $wp_scripts->registered[ $handle ];

                $src = $dependency->src;

                // check for (and append) version query string
                if( isset( $dependency->ver ) && !empty( $dependency->ver ) ) {
                    $src = sprintf( '%s?ver=%s', $src, $dependency->ver );   
                }

                printf( '<link rel="preload" as="script" href="%s" />', esc_url( $src ) );
            }
        }

        global $wp_styles;
        foreach( $wp_styles->queue as $handle ) {

            // handle marked to preload?
            if( true === wp_styles()->get_data( $handle, 'preload' ) ) {
                $dependency = $wp_styles->registered[ $handle ];

                $src = $dependency->src;

                // check for (and append) version query string
                if( isset( $dependency->ver ) && !empty( $dependency->ver ) ) {
                    $src = sprintf( '%s?ver=%s', $src, $dependency->ver );   
                }                

                printf( '<link rel="preload" as="style" href="%s" />', esc_url( $src ) );
            }
        }
    }

    
    /**
     * Show deprecation warnings for adding stylesheets
     * 
     * @since   2.6.0
     *
     * @param	array   $args       Arguments for stylesheet asset
     * @param   string  $function   Calling function
     * @return	void
     */
    private function handle_style_deprecations( array $args, string $function )
    {
        // handle deprecations
        if( isset( $args['prefetch'] ) ) {
            fx_bam_handle_deprecated_argument( 
                $function, 
                'prefetch', 
                '0.2.6', 
                '"prefetch" is no longer an accepted argument' 
            );
        }
    }
    

    /**
     * Show deprecation warnings for adding scripts
     * 
     * @since   2.6.0
     *
     * @param	array   $args       Arguments for script asset
     * @param   string  $function   Calling function
     * @return	void
     */
    private function handle_script_deprecations( array $args, string $function )
    {
        // handle deprecations
        if( isset( $args['prefetch'] ) ) {
            fx_bam_handle_deprecated_argument( 
                $function, 
                'prefetch', 
                '0.2.6', 
                '"prefetch" is no longer an accepted argument' 
            );
        }
    }   

}



function FX_Assets() {
    return FX_Assets::instance();
}



FX_Assets();

