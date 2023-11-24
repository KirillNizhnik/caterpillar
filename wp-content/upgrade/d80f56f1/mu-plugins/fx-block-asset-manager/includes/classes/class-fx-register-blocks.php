<?php



defined( 'ABSPATH' ) || exit;



/**
 * Block Registration File
 * Contains code for registering blocks and block-assets
 * Add after plugins loaded so ACF Pro will be initialized
 */
 

class FX_Register_Blocks
{
    protected static $instance              = null;

    protected static $default_icon          = '';

    protected $all_post_types               = [];

    protected $blocks_to_register           = [];
    protected $registered_blocks            = [];
    protected $custom_block_categories      = [];



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
		self::$default_icon = fx_get_block_icon();

        add_action( 'init', [ $this, 'get_all_post_types' ], 90 );
        add_action( 'init', [ $this, 'register_acf_blocks' ], 99 );
                
        add_filter( 'block_categories_all', [ $this, 'set_custom_block_categories' ], 99 );
        add_filter( 'render_block',         [ $this, 'maybe_add_block_name_to_output' ], 99, 2 );
        
       // add_action( 'fx_bam_after_register_block', [ $this, 'after_register_block' ] );
	}


    /**
     * Get all registered post types in WordPress (used later when checking for post type exclusion)
     *
     * @return  void
     */
    public function get_all_post_types(): void
    {
        $this->all_post_types = get_post_types(
            [
                'public' => true
            ]
        );
    }
	

    /**
     * Captures block settings to register. Doesn't immediately register block (as we need to wait until init), but 
     * saves block settings until that time
     *
     * @param	array   $settings   Block settings
     * @return  bool                True
     */
    public function register_block( array $settings ): bool
    {
        $block_name = $settings['name'];
        $this->blocks_to_register[ $block_name ] = $settings;

        return true;
    }


    /**
     * Wrapper function for registering blocks as ACF blocks
     *
     * @return  void
     */
    public function register_acf_blocks(): void
    {
        foreach( $this->blocks_to_register as $block_settings ) {
            $this->register_acf_block( $block_settings );
        }
    }
	

    /**
     * Parse block settings and register block
     *
     * @param	array   $settings   Block settings
     * @return  mixed               Array, if block was registered; otherwise, false
     */
	private function register_acf_block( array $settings )
    {
        // parse passed settings with ACF defaults and assets
        $defaults = [

            // ACF defaults
            'name'                  => '', // required
            'title'                 => '', // required
            'description'           => 'WebFX-created block',
            'category'              => 'fx-general-blocks',
            'icon'                  => self::$default_icon,
            'keywords'              => [],
            'post_types'            => [ 'page' ],
            'mode'                  => 'edit',
            'align'                 => 'full',
            'align_text'            => 'center',
            'align_content'         => 'center',
            'render_template'       => '', // if empty, we try to determine template below based on name
            'render_callback'       => null,
            'enqueue_style'         => '',
            'enqueue_script'        => '',
            'enqueue_assets'        => null,
            'supports'              => [],
            'example'               => [],

            // FX-added
            'template'              => '', // required
            'css'                   => '',
            'css_deps'              => [],
            'js'                    => '',
            'js_deps'               => [],
            'exclude_post_types'    => null,
            'thumbnail'             => null,
        ];
        $args = wp_parse_args( $settings, $defaults );

        // standardize how args are formatted, including in anticipation for ACF
        $args = $this->clean_up_args( $args );

        // check if block supports preview when hovering over block in Block Library
        $args = $this->add_library_preview_support( $args );

        // check for valid template for rendering or valid function for rendering
        $args = $this->validate_render_method( $args ); 

        // hook for theme/plugins before block is registered
        $args = apply_filters( 'fx_bam_before_register_block', $args, $settings );

        // get converted ACF block
        $registered_block = acf_register_block_type( $args );

        $this->push_block( $registered_block, $registered_block['name'] );

        do_action( 'fx_bam_after_register_block', $registered_block, $args );

        return $registered_block;
    }


    /**
     * Cleans up arguments to be more standardized
     *
     * @param	array	$args   Block args
     * @return  array           Block args
     */
    private function clean_up_args( array $args ): array
    {
        // guess name if value wasn't passed
        if( empty( $args['name'] ) ) {
            $args['name'] = sanitize_title( $args['title'] );
        }

        // values need to be arrays; if not arrays, use empty
        $args['supports'] = ( is_array( $args['supports'] ) ) ? $args['supports'] : [];
        $args['example'] = ( is_array( $args['example'] ) ) ? $args['example'] : [];

        // values can be passed as strings or arrays, but final format needs to be array
        $args['css_deps']           = fx_bam_ensure_clean_array( $args['css_deps'] );
        $args['js_deps']            = fx_bam_ensure_clean_array( $args['js_deps'] );
        $args['post_types']         = fx_bam_ensure_clean_array( $args['post_types'] );
        $args['exclude_post_types'] = fx_bam_ensure_clean_array( $args['exclude_post_types'] );

        // not using these properties right now
        $args['enqueue_style']  = null;
        $args['enqueue_script'] = null;
        $args['enqueue_assets'] = null;        

        // ensure correct mode
        if( isset( $settings['supports']['jsx'] ) && $settings['supports']['jsx'] ) {
            $args['mode'] = 'preview';
        }

        // if empty array, assume all post types
        $args['post_types'] = ( !empty( $args['post_types'] ) ) ? $args['post_types'] : $this->all_post_types;

        // check if any post types were explicitly excluded
        if( !empty( $args['exclude_post_types'] ) ) {

            // remove exclusions
            $args['post_types'] = array_values( array_diff( $args['post_types'], $args['exclude_post_types'] ) );
        }

        return $args;
    }


    /**
     * If block has preview thumbnail, this checks if the current request is an AJAX request from ACF and determines if 
     * the request is meant to fetch a preview of the block in the Block Library or refresh the preview view for a 
     * preexisting block on the page
     *
     * @param	array   $args   Block args
     * @return  array           Block args
     */
    public function add_library_preview_support( array $args ): array
    {
        if( empty( $args['thumbnail'] ) ) {
            return $args;
        }

        // differentiates between getting preview for Block Library or ACF updating live block preview
        $args['example']['attributes']['data']['fx_thumbnail'] = $args['thumbnail'];

        // if handling an AJAX request from ACF, check if generating a preview image versus preview of template
        if( wp_doing_ajax() && 'acf/ajax/fetch-block' === $_REQUEST['action'] ) {
            $post_block = wp_unslash( $_POST['block'] );
            $post_block = json_decode( $post_block, true );

            // live preview for preexisting blocks won't have this value (all fields are field keys)
            if( isset( $post_block['data']['fx_thumbnail'] ) ) {
                $args['render_callback'] = [ $this, 'render_preview_thumbnail' ];
            }
        }

        return $args;
    }



    /**
     * Check that block has either a valid template for rendering or a valid function for rendering
     * 
     * If block has a value for "template", function will either check that file exists or tries to guess file based on
     * block-templates directory in theme. This value WILL overwrite value for "render_template" if that was separately
     * passed.
     *
     * @param	array   $args   Block args
     * @return  array           Block args
     */
    private function validate_render_method( array $args ): array
    {
        // is rendering callback valid?
        if( !is_callable( $args['render_callback'] ) ) {
            $args['render_callback'] = '';
        }

        // if "template" setting was passed, set (and potentially override) "render_template"
        if( !empty( $args['template'] ) ) {
            $template_path  = $this->get_template_path( $args['template'] );

            if( !empty( $template_path ) ) {
                $args['render_template'] = $template_path;
            }
        }

        return $args;
    }


    /**
     * Get template for path, including checks for multisite
     * 
     * @since   0.2.6
     *
     * @param	string	$template   Template name to check/use
     * @return  string              Template path
     */
    public function get_template_path( string $template = '' ): string
    {
        $template_path = '';

        // passed full file path?
        if( is_file( $template ) ) {
            $template_path = $template;

        // guess template file
        } else {
            $template_path = sprintf( 
                '%s/block-templates/%s', 
                get_template_directory(), // (intentionally) checking parent theme first 
                $template 
            );
        }

        // check if child theme has template file match
        $template_path = fx_bam_maybe_get_child_theme_file_path( $template_path );

        // double-check just to be safe
        if( !is_file( $template_path ) ) {
            $template_path = '';
        }

        return $template_path;
    }


    /**
     * Add block to registrar
     *
     * @param	array   $block  Block data
     * @param   string  $key    Key for array
     * 
     * @return  void
     */
    public function push_block( array $block, string $key )
    {
        $this->registered_blocks[ $key ] = $block;
    }
    

    /**
     * Get block from register and remove block
     *
     * @param	string	$block_name     Block name
     * @return  mixed                   Array if matching block; otherwise false
     */
    public function pop_block( string $block_name )
    {
        if( isset( $this->registered_blocks[ $block_name ] ) ) {
            $block = $this->registered_blocks[ $block_name ];

            unset( $this->registered_blocks[ $block_name ] );

            return $block;
        }

        return false;
    }
    

    /**
     * Get specific block currently in registrar
     *
     * @param	string	Description
     * @return  mixed       Array, if block is found; otherwise, false
     */
    public function get_registered_block( string $key )
    {
        if( isset( $this->registered_blocks[ $key ] ) ) {
            return $this->registered_blocks[ $key ];
        }

        return false;
    }


    /**
     * Get all blocks currently in registrar
     *
     * @return  array
     */
    public function get_registered_blocks(): array
    {
        return $this->registered_blocks;
    }


    /**
     * Registers block category. Used for organizing blocks in Block Editor
     *
     * @param	string	$title  Category title
     * @param   string  $slug   Category slug (if blank, title is keyified)
     * @param   string  $icon   Category icon (dashicon or SVG)
     * 
     * @return  void
     */
    public function register_block_category( string $title, string $slug = '', string $icon = '' ): void
    {
        $title = trim( $title );
        if( empty( $slug ) ) {
            $slug = sanitize_title( $title );
        }

        $this->custom_block_categories[ $slug ] = [
            'icon'  => $icon,
            'title' => $title,
        ];
    }


    /**
     * Set custom block categories
     *
     * @return  array   Block categories
     */
    public function set_custom_block_categories( array $categories = [] ): array
    {
        $custom_categories = [];

        foreach( $this->custom_block_categories as $slug => $data ) {
            $custom_categories[] = [
                'icon'  => ( $data['icon'] ) ?: null,
                'slug'  => $slug,
                'title' => $data['title'],
            ];
        }

        if( !empty( $custom_categories ) ) {
            $categories = array_merge( $custom_categories, $categories );
        }

        return $categories;
    }


    /**
     * Add block name to block for page output to help out with troubleshooting
     *
     * @param	string	$output     HTML output
     * @param   array   $block      Block data
     * 
     * @return  string
     */
    public function maybe_add_block_name_to_output( string $output, array $block ): string
    {
        if( fx_bam_debug_mode_enabled() && !empty( trim( $block_name = $block['blockName'] ) ) ) {
            $before_block   = sprintf( '<!-- Start Block: %s -->', $block_name );
            $after_block    = sprintf( '<!-- End Block: %s -->', $block_name );
    
            $output = sprintf( "%s\n%s\n%s", $before_block, $output, $after_block );
        }
    
        return $output;
    }


    /**
     * Check for block thumbnail and if image exists, show as preview for block in Block Library
     *
     * @param	array	$block          Block to render
     * @param   array   $content        Rendered content
     * @param   array   $is_preview     True, if block is being rendered for preview
     * 
     * @return  string                  Rendered content
     */
    public function render_preview_thumbnail( array $block, string $content = '', bool $is_preview = false )
    {
        if( !$is_preview ) {
            return;
        }

        $thumbnail = $block['thumbnail'];

        if( is_numeric( $thumbnail ) ) {
            $thumbnail = absint( $thumbnail );
            $thumbnail = wp_get_attachment_url( $thumbnail );
        }

        if( is_string( $thumbnail ) && !empty( $thumbnail ) ) {
            printf( '<img src="%s" />', esc_url( $thumbnail ) );
        }
    }


    /**
     * Built-in functions for handling block after block registration
     *
     * @since   0.2.15 
     * 
     * @param	array   $registered_block   Block registered in ACF
     * @param   array   $args               Original block args
     * 
     * @return  void
     */
    public function after_register_block( array $registered_block ): void
    {
        // if block contains anything CF7-related, exclude block's assets from being inlined
         if( fx_bam_array_contains_value( $registered_block, [ 'cf7', 'contact-form-7' ] ) ) {
           add_filter( 'fx_bam_do_not_inline_block_assets', function( $excluded_blocks ) use ( $registered_block ) {
                $excluded_blocks[] = $registered_block['name'];

                return $excluded_blocks;
            }); 
        }
    }
    
}
 


function FX_Register_Blocks() {
    return FX_Register_Blocks::instance();
}

