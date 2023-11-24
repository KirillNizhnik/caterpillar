<?php

/** 
 * Documentation on FX Assets:
 * https://app.getguru.com/card/ceEjzyKi/FX-Assets
 */


/**
 * Register and enqueue theme styles
 *
 * @return void
 */
add_action( 'wp_enqueue_scripts', 'fx_theme_styles' );
function fx_theme_styles() {
    $theme_dir = get_template_directory();
    $theme_url = get_template_directory_uri();

    /* Inline critical/above-the-fold stylesheets */
    
    fx_assets_add_stylesheet(
        [
            'handle'    => 'normalize',
            'src'       => $theme_url . '/assets/css/normalize.css',
            'inline'    => true,
            'priority'  => PHP_INT_MIN,
        ]
    );
    
    // TODO remove this stylesheet registration if not using ubermenu.
    fx_assets_add_stylesheet(
        [
            'handle'    => 'fx-ubermenu',
            'src'       => plugins_url() . '/ubermenu/pro/assets/css/ubermenu.min.css',
            'inline'    => true,
        ]
    );
    
    // Styles that are required on every page
    fx_assets_add_stylesheet(
        [
            'handle'    => 'site-global',
            'src'       => $theme_url . '/assets/css/global.css',
            'inline'    => true,
        ]
    );

    fx_assets_add_stylesheet(
        [
            'handle'    => 'site-custom-properties',
            'src'       => $theme_url . '/assets/css/custom-properties.css',
            'inline'    => true,
        ]
    );
    
    // Header Styling
    fx_assets_add_stylesheet(
        [
            'handle'    => 'site-header',
            'src'       => $theme_url . '/assets/css/header.css',
            'inline'    => true,
        ]
    );
    
    fx_assets_add_stylesheet(
        [
            'handle'    => 'fx_fancy_box',
            'src'       => $theme_url . '/assets/css/plugins/fancybox.css',
        ]
    );
    
    
    /* Other theme styles, enqueued normally (not inline in header) */
    
    // Footer Styling
    fx_assets_add_stylesheet(
        [
            'handle'    => 'site-footer',
            'src'       => $theme_url . '/assets/css/footer.css',
            'enqueue'   => !is_admin()
        ]
    );
        
    // Posts-specific styling: blog singles, archives, search page
    fx_assets_add_stylesheet(
        [
            'handle'    => 'site-posts',
            'src'       => $theme_url . '/assets/css/posts.css',
            'enqueue'   => ( is_single() || is_home() || is_archive() || is_search() )
        ]
    );

    // Styles for only 404 page
    fx_assets_add_stylesheet(
        [
            'handle'    => 'site-404',
            'src'       => $theme_url . '/assets/css/404.css',
            'enqueue'   => is_404()
        ]
    );    
    
    // Print Styles
    fx_assets_add_stylesheet(
        [
            'handle'    => 'site-print',
            'src'       => $theme_url . '/assets/css/print.css',
            'enqueue'   => !is_admin(),
            'media'     => 'print'
        ]
    );
    
    /* Component-specific css. These will be enqueued per-block as dependencies or per-page as needed. These files can be edited to override default styling. */
    
    // Styles for social sharing buttons on blog pages
    fx_assets_add_stylesheet(
        [
            'handle'    => 'fx_social',
            'src'       => $theme_url . '/assets/css/components/FxSocialShare.css',
            'enqueue'   => is_single()
        ]
    );
    
    // Styles specifically for CF7 forms.
    fx_assets_add_stylesheet(
        [
            'handle'    => 'fx_cf7',
            'src'       => $theme_url . '/assets/css/components/cf7.css',
        ]
    );

    // Styles for accordion block sections
    fx_assets_add_stylesheet(
        [
            'handle'    => 'fx_accordion',
            'src'       => $theme_url . '/assets/css/components/FxAccordion.css',
        ]
    );
    
    // Styles for tab/accordion block sections
    fx_assets_add_stylesheet(
        [
            'handle'    => 'fx_tabs_accordion',
            'src'       => $theme_url . '/assets/css/components/FxTabsAccordion.css',
            'enqueue'   => is_search()
        ]
    );
    
    // Custom styling for choices.js library
    fx_assets_add_stylesheet(
        [
            'handle'        => 'fx_choices_custom',
            'src'           => $theme_url . '/assets/css/components/choices.css',
            'dependencies'  => [ 'fx_choices_plugin' ],
            // 'enqueue'       => is_archive(), // TODO uncomment if categories in sidebar will use drop-downs. Remove otherwise.
        ]
    );
    
    // Custom styling for slick library
    fx_assets_add_stylesheet(
        [
            'handle'        => 'fx_slick',
            'src'           => $theme_url . '/assets/css/components/slick.css',
            'dependencies'  => ['fx_slick_plugin'],
        ]
    );
    
    // Custom styling for ninja tables. TODO: remove if not using ninja tables.
    fx_assets_add_stylesheet(
        [
            'handle'        => 'fx_ninja',
            'src'           => $theme_url . '/assets/css/components/ninja-tables.css',
            'dependencies'  => ['fx_ninja_plugin'],
        ]
    );

     // Shared styling for "Read More" text sections. TODO: remove if not using Read More content
     fx_assets_add_stylesheet(
        [
            'handle'        => 'fx_readmore',
            'src'           => $theme_url . '/assets/css/components/FxHideShow.css',
        ]
    );
    
    /* Plugin-specific css dependencies. These will be enqueued per-block as dependencies or per-page as needed. These files should not be edited directly. */
    
    
    fx_assets_add_stylesheet(
        [
            'handle'    => 'fx_slick_plugin',
            'src'       => $theme_url . '/assets/css/plugins/slick.css',
        ]
    );
    
    fx_assets_add_stylesheet(
        [
            'handle'    => 'fx_choices_plugin',
            'src'       => $theme_url . '/assets/css/plugins/choices.css',
        ]
    );
    
    // TODO: remove if not using ninja tables.
    fx_assets_add_stylesheet(
        [
            'handle'    => 'fx_ninja_plugin',
            'src'       => $theme_url . '/assets/css/plugins/ninja-tables.css',
        ]
    );
    
    // custom dependencies for WP plugins. Ensures that assigned stylesheets loads BEFORE plugin's stylesheets(s) load
    fx_assets_add_plugin_style( 'contact-form-7', 'fx_choices_custom' );
    fx_assets_add_plugin_style( 'contact-form-7', 'fx_cf7' );

    // TODO: remove below if not using ninja tables.
    fx_assets_add_plugin_style( 'footable_styles', 'fx_ninja' );
}


/** 
 * Register and enqueue trump styles
 * 
 * @hooked wp_enqueue_scripts priority 103 so block asset manager completes running and trump styles are enqueued last.
 */
add_action( 'wp_enqueue_scripts', 'fx_theme_style_trumps', 9999 );
function fx_theme_style_trumps() {
    fx_assets_add_stylesheet(
        [
            'handle'        => 'site-trumps',
            'src'           => get_template_directory_uri() . '/assets/css/trumps.css',
            'enqueue'       => !is_admin()
        ]
    );
}



/**
 * Register and enqueue theme scripts
 *
 * @return void
 */
add_action( 'wp_enqueue_scripts', 'fx_theme_scripts' );
function fx_theme_scripts() {
    $theme_dir = get_template_directory();
    $theme_url = get_template_directory_uri();

    // Scripts that must be included on every page.
    fx_assets_add_script(
        [
            'handle'        => 'site-global',
            'src'           => $theme_url . '/assets/js/global.js',
            'dependencies'  => [ 'jquery', 'fx_fitvids' ],
            'enqueue'       => !is_admin(),
            'defer'         => true,
            'preload'       => true,
        ]
    );
    

    /**
     * Component-specific JS
     * 
     * These will be enqueued per-block as dependencies or per-page as needed. These files can be edited to override 
     * default behavior if necessary.
     */
    // 
    // fx_assets_add_script(
    //     [
    //         'handle'        => 'js-magnific-popup',
    //         'src'           => $theme_url . '/assets/js/plugins/js/jquery.magnific-popup.min.js',
    //         'dependencies'  => [ 'jquery' ],
    //         'enqueue'       => true, // todo — uncomment and add conditions for dropdowns, etc
    //     ]
    // );     

    /**
     * Component-specific JS
     * 
     * These will be enqueued per-block as dependencies or per-page as needed. These files can be edited to override 
     * default behavior if necessary.
     */
    // 
    fx_assets_add_script(
        [
            'handle'        => 'fx_choices',
            'src'           => $theme_url . '/assets/js/components/FxChoices.js',
            'dependencies'  => [ 'fx_choices_plugin' ],
            'defer'         => true,
            // 'enqueue'       => ( is_archive() || is_home() ), // todo — uncomment and add conditions for dropdowns, etc
        ]
    );     
    
    // Script for block sections that use parallax
    fx_assets_add_script(
        [
            'handle'        => 'fx_parallax',
            'src'           => $theme_url . '/assets/js/components/FxParallax.js',
            'dependencies'  => [ 'jquery' ],
            'defer'         => true,
        ]
    );     
    
    // Script social sharing buttons on blog pages
    fx_assets_add_script(
        [
            'handle'        => 'fx_social',
            'src'           => $theme_url . '/assets/js/components/FxSocialShare.js',
            'enqueue'       => is_single()
        ]
    );
    
    // Script for "Read More" block sections
    fx_assets_add_script(
        [
            'handle'        => 'fx_readmore',
            'src'           => $theme_url . '/assets/js/components/FxReadMore.js',
            'dependencies'  => [ 'jquery' ],
        ]
    );
    
    fx_assets_add_script(
        [
            'handle'        => 'fx_fancy_box',
            'src'           => $theme_url . '/assets/js/plugins/fancybox.js',
            'dependencies'  => [ 'jquery' ],
            'defer'         => true,
        ]
    );
    
    // Script for accordion block sections
    fx_assets_add_script(
        [
            'handle'        => 'fx_accordion',
            'src'           => $theme_url . '/assets/js/components/FxAccordion.js',
            'dependencies'  => [ 'jquery' ],
        ]
    );
    
    // Script for tabs/accordion block sections
    fx_assets_add_script(
        [
            'handle'        => 'fx_tabs_accordion',
            'src'           => $theme_url . '/assets/js/components/FxTabsAccordion.js',
            'enqueue'       => is_search()
        ]
    );
    
    // Script for block sections that use parallax
    fx_assets_add_script(
        [
            'handle'        => 'fx_parallax',
            'src'           => $theme_url . '/assets/js/components/FxParallax.js',
            'dependencies'  => [ 'jquery' ],
            'defer'         => true,
        ]
    );  
        
    
    /**
     * Plugin-specific JS
     * 
     * Enqueued per-block as dependencies or per-page as needed. These files should not be edited directly.
     */ 
    fx_assets_add_script(
        [
            'handle'        => 'fx_slick',
            'src'           => $theme_url . '/assets/js/plugins/slick.js',
            'dependencies'  => [ 'jquery', 'fx_choices' ],
        ]
    );
    
    fx_assets_add_script(
        [
            'handle'        => 'fx_choices_plugin',
            'src'           => $theme_url . '/assets/js/plugins/choices.js',
            'minify'        => false,
        ]
    );
    
    fx_assets_add_script(
        [
            'handle'        => 'fx_fitvids',
            'src'           => $theme_url . '/assets/js/plugins/fitvids.js',
            'dependencies'  => [ 'jquery' ],
            'defer'         => true,
            'enqueue'       => true
        ]
    );

    fx_assets_add_script(
        [
            'handle'        => 'fx_masked_input',
            'src'           => $theme_url . '/assets/js/plugins/jquery.maskedinput.min.js',
            'dependencies'  => [ 'jquery' ],
            'defer'         => true,
        ]
    );
    
    fx_assets_add_script(
        [
            'handle'        => 'fx_phone_formatter',
            'src'           => $theme_url . '/assets/js/plugins/FormatPhoneNumbers.js',
            'dependencies'  => [ 'jquery' ],
            'defer'         => true,
        ]
    );

    // custom dependencies for WP plugins. Ensures that assigned scripts loads BEFORE plugin's script(s) load
    fx_assets_add_plugin_script( 'contact-form-7', 'fx_choices' );
    fx_assets_add_plugin_script( 'contact-form-7', 'fx_masked_input' );
    fx_assets_add_plugin_script( 'contact-form-7', 'fx_phone_formatter' );

    if( !is_admin() ) {
        wp_localize_script(
            'site-global',
            'FX',
            [
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'siteurl' => site_url(),
            ]
        );
    }
}


/** 
 * Prevents CF7 scripts from being inlined if CF7 is in first block on page
 * 
 * @param   array   $excluded_blocks    Excluded blocks
 * @return  array   Excluded blocks
 */
add_filter( 'fx_bam_do_not_inline_block_assets', 'fx_exclude_cf7_asset_inline' );
function fx_exclude_cf7_asset_inline( array $excluded_blocks ): array {
    $excluded_blocks[] = 'acf/cf7-block';

    return $excluded_blocks;
}


/** 
 * Remove jQuery Migrate as a dependency for jQuery to improve load times
 * 
 * @param   WP_Scripts  $scripts    WP_Scripts object
 * @return  void
 */
add_action( 'wp_default_scripts', 'fx_dequeue_jquery_migrate' );
function fx_dequeue_jquery_migrate( WP_Scripts $scripts ): void {
    if( !is_admin() && !empty( $scripts->registered['jquery'] ) ) {
        $scripts->registered['jquery']->deps = array_diff(
            $scripts->registered['jquery']->deps,
            [ 'jquery-migrate' ]
        );
    }
}

add_action( 'wp_enqueue_scripts', 'fx_queue_industry_page_assests');
function fx_queue_industry_page_assests() {
    if(get_queried_object_id() == 7109)
    wp_enqueue_style(
            'fx_img_btn'
            ,get_template_directory_uri()  . '/assets/css/blocks/innerpage/image-button.css'
             ,array()
                ,false
                ,'all'
        );
}







