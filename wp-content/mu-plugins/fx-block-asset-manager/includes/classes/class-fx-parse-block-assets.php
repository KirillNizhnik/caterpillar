<?php



defined( 'ABSPATH' ) || exit;



/**
 * FX Parse Blocks
 * 
 * Contains logic for parsing block assets and enqueuing them wherever the block is used to improve site speed.
 */
 
class FX_Parse_Block_Assets
{
    protected static $instance      = null;

    protected static $fx_assets     = null;
    protected static $fx_registrar  = null;

    public $cf7_on_page             = false;
    

    
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
        self::$fx_assets    = FX_Assets();
        self::$fx_registrar = FX_Register_Blocks();

        // runs with priority of 102 so script/stylesheet tags modified by FX_Assets class will be applied
		add_action( 'wp_enqueue_scripts',                   [ $this, 'enqueue_block_assets' ], 102 );
        add_action( 'fx_bam_parse_non_fx_blocks',           [ $this, 'double_check_for_cf7' ] );
        add_action( 'fx_bam_after_parse_block_assets',      [ $this, 'double_check_for_cf7' ] );
        //add_action( 'fx_bam_after_parse_all_block_assets',  [ $this, 'maybe_dequeue_cf7' ] );
	}

    
    /** 
     * Fetch blocks in whose assets should be excluded from inlining. 
     * 
     * For example, blocks with dependencies using wp_localize_script should be excluded to prevent conflicts
     * 
     * @return  array
     */
    public static function get_blocks_excluded_from_inline(): array 
    {
        $excluded_blocks = [];

        return apply_filters( 'fx_bam_do_not_inline_block_assets', $excluded_blocks );
    }
    

    /**
     * Loop through blocks in the_content and enqueues block assets.
     * 
     * Assets for first block will be inlined to improve paint times (can be override with 
     * fx_do_not_inline_block_assets) filter
     * 
     * Originally blocks that were added through the WP admin would error out if those blocks' registration changed 
     * (e.g. the code was commented out in acf-blocks). As of 0.2.12, this will now throw a notice/warning
     * 
     * @return void
     */
    public function enqueue_block_assets(): void 
    {
        $blocks = self::get_post_blocks();

        // remove non-FX and duplicate blocks, and extract innerblocks into single array
        $blocks = self::flatten_blocks( $blocks );
        $excluded_inline_blocks = self::get_blocks_excluded_from_inline();
        
        // for tracking invalid blocks
        $block_errors = new WP_Error();
        
        $block_index = 0;
        foreach( $blocks as $block ) {
            $block_name     = $block['attrs']['name'];
            $block_settings = self::$fx_registrar->get_registered_block( $block_name );

            // todo — move to FX_Register_Blocks?
            if( !is_array( $block_settings ) ) {
                $block_errors->add( 
                    'warning',
                    sprintf( 'This page contains the following invalid block: "%s" and should be deleted.', $block_name ) ,
                    [
                        'block_name' => $block_name,
                        'block_settings' => $block_settings
                    ]
                );

                continue;
            }

            $block_assets   = self::get_block_assets( $block_settings );

            // if first block (and not explicitly excluded, let's inline assets)
            $inline_assets  = ( 0 === $block_index && !in_array( $block_name, $excluded_inline_blocks ) );

            if( !empty( $block_assets ) ) {
                $css            = $block_assets['css'];
                $css_src        = $css['src'] ?? false;
                $css_filepath   = $css['path'] ?? false;
                $css_deps       = $block_assets['css_deps'];
                
                $js             = $block_assets['js'];
                $js_src         = $js['src'] ?? false;
                $js_filepath    = $js['path'] ?? false;
                $js_deps        = $block_assets['js_deps'];

                // conditionally inline assets (for first block)
                if( $inline_assets ) {
                    if( !empty( $css ) ) {
                        fx_assets_add_stylesheet( 
                            [
                                'handle'        => $block_name,
                                'src'           => $css_src,
                                'inline'        => true,
                                'dependencies'  => $css_deps,
                                'path'          => $css_filepath,
                            ]
                        );

                    // even if block doesn't have a dedicated stylesheet, inline block CSS dependencies (e.g. components)
                    } elseif( !empty( $css_deps ) ) {
                        self::$fx_assets->inline_css_dependencies( $css_deps );
                    }

                    if( !empty( $js ) ) {
                        fx_assets_add_script(
                            [
                                'handle'        => $block_name,
                                'src'           => $js_src,
                                'inline'        => true,
                                'dependencies'  => $js_deps,
                                'path'          => $js_filepath,
                            ]
                        ); 
                    
                    // even if block doesn't have a dedicated stylesheet, inline block CSS dependencies (e.g. components)
                    } elseif( $js_deps ) {
                        self::$fx_assets->inline_js_dependencies( $js_deps );
                    } 

                } else {
                    if( !empty( $css_src ) ) {
                        fx_assets_add_stylesheet(
                            [
                                'handle'        => $block_name,
                                'src'           => $css_src,
                                'enqueue'       => true,
                                'dependencies'  => $css_deps,
                                'path'          => $css_filepath,
                            ]
                        );
                    } elseif( !empty( $css_deps ) ) {
                        foreach( $css_deps as $css_dep ) {
                            wp_enqueue_style( $css_dep );
                        }
                    }

                    if( !empty( $js_src ) ) {
                        fx_assets_add_script(
                            [
                                'handle'        => $block_name,
                                'src'           => $js_src,
                                'enqueue'       => true,
                                'defer'         => true,  
                                'dependencies'  => $js_deps,
                                'path'          => $js_filepath,
                            ]
                        );
                        
                    } elseif( !empty( $js_deps ) ) {
                        foreach( $js_deps as $dep ) {

                            // don't defer jQuery
                            if( false === strpos( $dep, 'jquery' ) ) {
                                wp_script_add_data( $dep, 'defer', true );
                            }

                            wp_enqueue_script( $dep );
                        }
                    }                    
                }

                // hook for theme/plugins after a specific block's assets are processed
                do_action( 'fx_bam_after_parse_block_assets', $block );
            }

            // if first block contains inner blocks, ensure that inner block assets are also inlined
            if( empty( $block['innerBlocks'] ) ) {
                ++$block_index;
            }
        }

        // show error for invalid blocks to prompt deletion in WP admin
        if( $block_errors->has_errors() ) {
            foreach( $block_errors->get_error_messages() as $message ) {
                trigger_error( $message, E_USER_WARNING );
            }
        }

        // hook for theme/plugins after all block assets are processed
        do_action( 'fx_bam_after_parse_all_block_assets', $blocks );
    }
    

    /**
     * Flatten all blocks (including inner blocks) into a single, one-level array
     *
     * @param	array   $blocks     Blocks
     * @return  array               Flattened block array
     */
    public static function flatten_blocks( array $blocks ): array 
    {
        $flattened_blocks = [];

        foreach( $blocks as $block ) {
            $block_name = $block['attrs']['name'] ?? null;

            // skip block is already added
            if( isset( $flattened_blocks[ $block_name ] ) ) {
                continue;
            }

            if( !empty( $block_name ) ) {
                $flattened_blocks[ $block_name ] = $block;

            // handle reusable blocks
            } elseif( isset( $block['attrs']['ref'] ) ) {
                $reusable_block_id = $block['attrs']['ref'];
                $reusable_block = get_post( $reusable_block_id );

                // get blocks inside block
                $reusable_blocks = parse_blocks( $reusable_block->post_content );
                $reusable_blocks = self::flatten_blocks( $reusable_blocks );

                if( !empty( $reusable_blocks ) ) {
                    $flattened_blocks = array_merge( $flattened_blocks, $reusable_blocks );
                }

            // ignore non-FX blocks
            } else {
                do_action( 'fx_bam_parse_non_fx_blocks', $block );
            }

            if( isset( $block['innerBlocks'] ) && !empty( $block['innerBlocks'] ) ) {
                $inner_blocks = self::flatten_blocks( $block['innerBlocks'] );

                if( !empty( $inner_blocks ) ) {
                    $flattened_blocks = array_merge( $flattened_blocks, $inner_blocks );
                }
            }
        }

        return array_values( $flattened_blocks );
    }


	/**
     * Get all blocks on post
     *
     * @param   int|WP_Post     $post   Optionally pass post ID or WP_Post
     * @return  array                   Array of parsed blocks     
     */
    public static function get_post_blocks( $post = null ): array
    {
        $blocks = [];

        // passed post ID?
        if( is_numeric( $post ) ) {
            $post = absint( $post );
            $post = get_post( $post );
        }

        // if not a post, try to fetch from global object
        if( !is_a( $post, 'WP_Post' ) ) {
            global $post;
        }

        // double-check that it's a post
        if( is_a( $post, 'WP_Post' ) && true === setup_postdata( $post ) ) {
            $blocks = parse_blocks( $post->post_content );
        }

        return $blocks;
    }


    /**
     * Get block stylesheet, script, and CSS/JS dependencies
     *
     * @param	array   $block_settings Block settings
     * @return  array                   Block assets
     */
    public static function get_block_assets( array $block_settings ): array
    {
        $assets = [
            'css'       => null,
            'js'        => null,
            'css_deps'  => $block_settings['css_deps'],
            'js_deps'   => $block_settings['js_deps'],
        ];

        /**
         * Don't worry about parent/child theme assets here. FX_Assets class will take care of:
         *  1. checking if file is in parent theme 
         *  2. override that file if file with matching name/path exists in child theme
         */
        $theme_path = get_template_directory();
        $theme_url  = get_template_directory_uri();

        if( !empty( $css = $block_settings['css'] ) ) {
            if( is_file( $css ) ) {
                $assets['css'] = [ 
                    'path'  => $css,
                    'src'   => fx_bam_get_file_src_from_path( $css ),
                ];

            } else {
                $assets['css'] = [ 
                    'path'  => sprintf( 
                        '%s/assets/css/blocks/%s', 
                        $theme_path, 
                        $css 
                    ),
                    'src'   => sprintf( 
                        '%s/assets/css/blocks/%s', 
                        $theme_url, 
                        $css 
                    ),
                ];
            }
        }

        if( !empty( $js = $block_settings['js'] ) ) {
            if( is_file( $js ) ) {
                $assets['js'] = [ 
                    'path'  => $js,
                    'src'   => fx_bam_get_file_src_from_path( $js ),
                ];

            } else {
                $assets['js'] = [ 
                    'path'  => sprintf( 
                        '%s/assets/js/blocks/%s', 
                        $theme_path, 
                        $js 
                    ),
                    'src'   => sprintf( 
                        '%s/assets/js/blocks/%s', 
                        $theme_url, 
                        $js 
                    ),
                ];
            }
        }

        return $assets;
    }


    /**
     * Checks block content to see if CF7 shortcode was passed and if CF7 assets should be loaded
     * 
     * @internal        This may be deleted if we adopt Gravity Forms
     * @since   0.2.3
     * 
     * @param	array   $block  Block
     * @return  void
     */
    public function double_check_for_cf7( array $block ): void
    {
        if( true !== $this->cf7_on_page ) {
            if( fx_bam_array_contains_value( $block, [ 'cf7', 'contact-form-7' ] )  ) {
                $this->cf7_on_page = true;
            }
        }
    }


    /**
     * Conditionally dequeue CF7 assets if CF7 is not on page
     *
     * @return  void
     */
    public function maybe_dequeue_cf7(): void
    {
        // hook for theme/plugins to override setting
        $this->cf7_on_page = apply_filters( 'fx_bam_include_cf7_scripts', $this->cf7_on_page );

        if( !$this->cf7_on_page || get_queried_object_id() !== 13082 ) {
            wp_dequeue_script( 'google-recaptcha' );
            wp_dequeue_script( 'wpcf7-recaptcha' );
            wp_dequeue_script( 'contact-form-7' );
        }
    }

    
}
 
 
function FX_Parse_Block_Assets() {
    return FX_Parse_Block_Assets::instance();
}


FX_Parse_Block_Assets();