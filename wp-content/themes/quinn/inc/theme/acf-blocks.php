<?php

/**
 * Register FX blocks
 *
 * fx_register_block() is, at its core, a wrapper function for acf_register_block_type with additional parameters for
 * our supporting functionality
 *
 * @see Guru card: https://app.getguru.com/card/Tn9zzk8c/FX-ACF-Blocks
 * @see more info for acf_register_block_type(): https://www.advancedcustomfields.com/resources/acf_register_block_type/
 *
 * Below is a reference for the parameters you can pass to fx_register_block(). You can also pass any setting from
 * acf_register_block_type() to fx_register_block().
 *
 * Required arguments: "name", "title", and "template"
 *
 */




/**
 * General blocks
 *
 * These blocks are intended to be used anywhere, including the homepage and innerpage.
 *
 * Block template path: /themes/fx/block-templates/general
 * Stylesheet path:     /themes/fx/assets/css/general
 * Script path:         /themes/fx/assets/js/general
 *
 */


/**
 * Create a "FX General Blocks" category in the block editor. Use "fx-general-blocks" as your "category" value in
 * fx_register_block()
 *
 */
fx_add_block_category( 'FX General Blocks', 'fx-general-blocks' );


/**
 * Plan WYSIWYG block for general usage
 *
 */
fx_register_block(
    [
        'name'          => 'wysiwyg',
        'title'         => 'WYSIWYG',
        'template'      => 'general/wysiwyg.php',
        'description'   => 'A basic "What you see is what you get" editor.',
        'css'           => 'general/wysiwyg.css',
        'post_types'    => [],
    ]
);


/**
 * To avoid issues with CF7 assets, we're creating our own CF7 block. You shouldn't need to touch this section.
 *
 */
$cf7_settings = [
    'name'          => 'cf7-block',
    'title'         => 'CF7 Block',
    'template'      => 'general/cf7-block.php',
    'css'           => 'general/cf7-block.css',
    'css_deps'      => [ 'fx_choices_plugin', 'fx_choices_custom' ],
    'description'   => 'Adds CF7 block to the page',
    'keywords'      => [ 'cf7', 'contact', 'form' ],
    'mode'          => 'edit',
    'js_deps'       => [ 'fx_choices_plugin', 'fx_choices', 'fx_phone_formatter', 'contact-form-7', 'wpcf7-recaptcha', 'google-recaptcha' ],
    //'js'            => 'innerpage/form-validation.js',
    'post_types'    => [], // all post types
];
$cf7_icon = WP_PLUGIN_DIR . '/contact-form-7/assets/icon.svg';
if( file_exists( $cf7_icon ) ) {
    $cf7_settings['icon'] = file_get_contents( $cf7_icon );
}
fx_register_block( $cf7_settings );

//  add additional general blocks below with the "fx-general-blocks" category



/**
 * Homepage blocks
 *
 * These blocks are intended to be used ONLY on the homepage.
 *
 * Block template path: /themes/fx/block-templates/homepage
 * Stylesheet path:     /themes/fx/assets/css/homepage
 * Script path:         /themes/fx/assets/js/homepage
 *
 */

/**
 * Create a "FX Homepage Blocks" category in the block editor. Use "fx-homepage-blocks" as your "category" value in
 * fx_register_block()
 *
 */
fx_add_block_category( 'FX Homepage Blocks', 'fx-homepage-blocks' );


/**
 * This is the main homepage "outer block." All other homepage blocks should be added within this block in the Block
 * Editor and in block-templates/homepage/homepage-block.php
 *
 */
fx_register_block(
    [
        'name'          => 'homepage-block',
        'title'         => 'Homepage',
        'template'      => 'homepage/homepage-block.php',
        'description'   => 'The main content block for the homepage',
        'mode'          => 'preview',
        'supports'      => [ 'jsx' => true ], // enables support for inner blocks
        'category'      => 'fx-homepage-blocks',
    ]
);



fx_register_block(
    [
        'name'          => 'homepage-masthead-slider',
        'title'         => 'Homepage - Masthead Slider',
        'template'      => 'homepage/masthead-slider.php',
        'description'   => 'Slider block for the homepage masthead.',
        'css'           => 'homepage/masthead-slider.css',
        'css_deps'      => [ 'fx_slick' ],
        'js'            => 'homepage/masthead-slider.js',
        'js_deps'       => [ 'fx_slick' ],
        'category'      => 'fx-homepage-blocks',
    ]
);



fx_register_block(
    [
        'name'          => 'homepage-Icon-Blocks',
        'title'         => 'Homepage - Icon Blocks',
        'template'      => 'homepage/icon-blocks.php',
        'description'   => 'Clickable icons with text below',
        'css'           => 'homepage/icon-blocks.css',
        'category'      => 'fx-homepage-blocks',
    ]
);

fx_register_block(
    [
        'name'          => 'homepage-video-text',
        'title'         => 'Homepage - Video Text',
        'template'      => 'homepage/video-text.php',
        'description'   => 'Half side text half side video that popups when click',
        'css'           => 'homepage/video-text.css',
        'css_deps'      => [ 'fx_fancy_box' ],
        'js_deps'       => [ 'fx_fancy_box' ],
        'category'      => 'fx-homepage-blocks',
    ]
);

fx_register_block(
    [
        'name'          => 'homepage-industries',
        'title'         => 'Homepage - Industries',
        'template'      => 'homepage/industries.php',
        'description'   => 'Clickable Slider Boxes',
        'css'           => 'homepage/industries.css',
        'css_deps'      => [ 'fx_slick' ],
        'js'            => 'homepage/industries.js',
        'js_deps'       => [ 'fx_slick' ],
        'category'      => 'fx-homepage-blocks',
    ]
);

fx_register_block(
    [
        'name'          => 'homepage-deals-and-specials',
        'title'         => 'Homepage - Deals and Specials',
        'template'      => 'homepage/deals-and-specials.php',
        'description'   => 'Slider with Left Image Right Content',
        'css'           => 'homepage/deals-and-specials.css',
        'css_deps'      => [ 'fx_slick' ],
        'js'            => 'homepage/deals-and-specials.js',
        'js_deps'       => [ 'fx_slick' ],
        'category'      => 'fx-homepage-blocks',
    ]
);

fx_register_block(
    [
        'name'          => 'homepage-equipment-blocks',
        'title'         => 'Homepage - Equipment Blocks',
        'template'      => 'homepage/equipment-blocks.php',
        'description'   => 'Hero Image with select categories below',
        'css'           => 'homepage/equipment-blocks.css',
        'css_deps'      => [ 'fx_choices_plugin', 'fx_choices_custom' ],
        'js_deps'       => [ 'fx_choices_plugin', 'fx_choices' ],
        'category'      => 'fx-homepage-blocks',
    ]
);

fx_register_block(
    [
        'name'          => 'homepage-text-and-image',
        'title'         => 'Homepage - Text and Image',
        'template'      => 'homepage/text-and-image.php',
        'description'   => 'Text block left side with image on the right',
        'css'           => 'homepage/text-and-image.css',
        'category'      => 'fx-homepage-blocks',
    ]
);

fx_register_block(
    [
        'name'          => 'homepage-find-a-location',
        'title'         => 'Homepage - CTA Find location',
        'template'      => 'homepage/find-a-location.php',
        'description'   => 'Hero Image with text and CTA location finder',
        'css'           => 'homepage/find-a-location.css',
        'category'      => 'fx-homepage-blocks',
    ]
);

/**
 * Innerpage blocks
 *
 * These blocks are intended to be used ONLY on innerpages
 *
 * Block template path: /themes/fx/block-templates/innerpage
 * Stylesheet path:     /themes/fx/assets/css/innerpage
 * Script path:         /themes/fx/assets/js/innerpage
 *
 */

/**
 * Create a "FX Innerpage Blocks" category in the block editor. Use "fx-innerpage-blocks" as your "category" value in
 * fx_register_block()
 *
 */

fx_add_block_category( 'FX Innerpage Blocks', 'fx-inner-page' );
/** Registers inner page block-category */
// add_filter( 'block_categories_all', 'fx_innerpage_block_category', 10 );
// function fx_innerpage_block_category( $categories ) {
//     return array_merge(
//         array(
//             array(
//                 'slug' => 'fx-inner-page',
//                 'title' => __( 'FX Inner Page Blocks', 'fx' ),
//             ),
//         ),
//         $categories
//     );
// }

// add additional innerpage blocks below with the "fx-innerpage-blocks" category


/*------------------------------------*\
    Image Button
\*------------------------------------*/
fx_register_block(
    [
        'name'          => 'image-button',
        'title'         => 'Innerpage - Image Button',
        'template'      => 'innerpage/image-button.php',
        'css'           => 'innerpage/image-button.css',
        'description'   => 'Image Button Section',
        'category'      => 'fx-inner-page',
        'post_types'    => null,
    ]
);

/*------------------------------------*\
    Half and Half
\*------------------------------------*/
fx_register_block(
    [
        'name'          => 'half-and-half',
        'title'         => 'Innerpage - Half and Half',
        'template'      => 'innerpage/half-and-half.php',
        'css'           => 'innerpage/half-and-half.css',
        'description'   => 'Half and Half Section',
        'category'      => 'fx-inner-page',
        'post_types'    => null,
    ]
);

/*------------------------------------*\
    Half and Half Grey Blocks
\*------------------------------------*/
fx_register_block(
    [
        'name'          => 'half-and-half-grey-blocks',
        'title'         => 'Innerpage - Half and Half Grey Blocks',
        'template'      => 'innerpage/half-and-half-grey.php',
        'css'           => 'innerpage/half-and-half-grey.css',
        'description'   => 'Half and Half Grey Blocks Section',
        'category'      => 'fx-inner-page',
        'post_types'    => null,
    ]
);

/*------------------------------------*\
    Full Width Image Background
\*------------------------------------*/
fx_register_block(
    [
        'name'          => 'fullwidth-image-background',
        'title'         => 'Innerpage - Fullwidth Image Background',
        'template'      => 'innerpage/fullwidth-image-background.php',
        'css'           => 'innerpage/fullwidth-image-background.css',
        'description'   => 'Fullwidth Image Background Section',
        'category'      => 'fx-inner-page',
        'post_types'    => null,
    ]
);

/*------------------------------------*\
    Accordion
\*------------------------------------*/
fx_register_block(
    [
        'name'          => 'accordion',
        'title'         => 'Innerpage - Accordion',
        'template'      => 'innerpage/accordion.php',
        'css'           => 'innerpage/accordion.css',
        'js_deps'       => [ 'fx_accordion' ],
        'js'            => 'innerpage/accordion.js',
        'description'   => 'Accordion Section',
        'category'      => 'fx-inner-page',
        'post_types'    => null,
    ]
);

/*------------------------------------*\
    Accordion
\*------------------------------------*/
fx_register_block(
    [
        'name'          => 'location-contact',
        'title'         => 'Innerpage - Location Contact',
        'template'      => 'innerpage/location-contacts.php',
        'css'           => 'innerpage/location-contacts.css',
        'description'   => 'Location Contact Section',
        'category'      => 'fx-inner-page',
        'post_types'    => null,
    ]
);

/*------------------------------------*\
    Innerpage CTA
\*------------------------------------*/
fx_register_block(
    [
        'name'          => 'inner-CTA',
        'title'         => 'Innerpage - CTA',
        'template'      => 'innerpage/inner-cta.php',
        'css'           => 'innerpage/inner-cta.css',
        'description'   => 'CTA Section',
        'category'      => 'fx-inner-page',
        'post_types'    => null,
    ]
);

/*------------------------------------*\
    Intro Text Section
\*------------------------------------*/
fx_register_block(
    [
        'name'          => 'inner-intro-text-section',
        'title'         => 'Innerpage - Intro Text Section',
        'template'      => 'innerpage/intro-text-section.php',
        'css'           => 'innerpage/intro-text-section.css',
        'description'   => 'Image and text, switchable positions',
        'category'      => 'fx-inner-page',
        'post_types'    => null,
    ]
);

/*------------------------------------*\
    Deals and Specials Page
\*------------------------------------*/
fx_register_block(
    [
        'name'          => 'inner-deals-specials-page',
        'title'         => 'Innerpage - Deals and Specials Page',
        'template'      => 'innerpage/deals-specials-page.php',
        'css'           => 'innerpage/deals-specials-page.css',
        //'js'            => 'innerpage/load-more.js',
        'description'   => 'List of all Deals and Specials items',
        'category'      => 'fx-inner-page',
        //'post_types'    => null,
    ]
);

/*------------------------------------*\
    Location List Archive
\*------------------------------------*/
fx_register_block(
    [
        'name'          => 'location',
        'title'         => 'Location',
        'template'      => 'innerpage/location.php',
        'css'           => 'innerpage/location.css',
        'description'   => 'List of locations',
        'css_deps'      => [ 'fx_choices_plugin', 'fx_choices_custom' ],
        'js_deps'       => [ 'fx_choices_plugin', 'fx_choices' ],
        'category'      => 'fx-inner-page',
        'post_types'    => null,
    ]
);

/*------------------------------------*\
    Specials List Archive
\*------------------------------------*/
fx_register_block(
    [
        'name'          => 'specials-archive',
        'title'         => 'Specials Archive',
        'template'      => 'innerpage/specials-archive.php',
        //'css'           => 'innerpage/specials-archive.css',
        //'js'            => 'innerpage/load-more.js',
        'description'   => 'Lists of all the Specials',
        'category'      => 'fx-inner-page',
        //'post_types'    => null,
    ]
);


/*------------------------------------*\
    Location List Archive
\*------------------------------------*/
fx_register_block(
    [
        'name'          => 'equipment-card',
        'title'         => 'Equipment Card',
        'template'      => 'general/cat-card-block.php',
        'css'           => 'general/cat-card-block.css',
        'description'   => 'Create an equipment or family page linked in a styled Card with customizable image and url.',
        //'css_deps'      => [ 'fx_choices_plugin', 'fx_choices_custom' ],
        //'js_deps'       => [ 'fx_choices_plugin', 'fx_choices' ],
        'category'      => 'fx-general-blocks',
        'post_types'    => null,
    ]
);

/*------------------------------------*\
    Image with alternate
\*------------------------------------*/
fx_register_block(
    [
        'name'          => 'image-w-alternate',
        'title'         => 'Innerpage - Image with Alternate Spanish Imag',
        'template'      => 'innerpage/image-w-alternate.php',
        'css'           => 'innerpage/image-button.css',
        'description'   => 'Image with Alternate Spanish Image',
        'category'      => 'fx-inner-page',
        'post_types'    => null,
    ]
);

/*------------------------------------*\
    Location Half Contact And Map
\*------------------------------------*/
fx_register_block(
    [
        'name'          => 'half-contact-map',
        'title'         => 'Innerpage - Location Half Contact And Map',
        'template'      => 'innerpage/half-contact-map.php',
        'css'           => 'innerpage/half-contact-map.css',
        'description'   => 'Location Half Contact And Map',
        'category'      => 'fx-inner-page',
        'post_types'    => null,
    ]
);

/*------------------------------------*\
    Location Contact Blocks
\*------------------------------------*/
fx_register_block(
    [
        'name'          => 'contact-blocks',
        'title'         => 'Innerpage - Location Contact Blocks',
        'template'      => 'innerpage/contact-blocks.php',
        'css'           => 'innerpage/contact-blocks.css',
        'description'   => 'Location Contact Blocks',
        'category'      => 'fx-inner-page',
        'post_types'    => null,
    ]
);

/*------------------------------------*\
    Location Brand Logos
\*------------------------------------*/
fx_register_block(
    [
        'name'          => 'brand-logos',
        'title'         => 'Innerpage - Location Brand Logos',
        'template'      => 'innerpage/brand-logos.php',
        'css'           => 'innerpage/brand-logos.css',
        'description'   => 'Location Brand Logos',
        'category'      => 'fx-inner-page',
        'post_types'    => null,
    ]
);

/*------------------------------------*\
    Location Services
\*------------------------------------*/
fx_register_block(
    [
        'name'          => 'location-services',
        'title'         => 'Innerpage - Location Services',
        'template'      => 'innerpage/location-services.php',
        'css'           => 'innerpage/location-services.css',
        'description'   => 'Location Services',
        'category'      => 'fx-inner-page',
        'post_types'    => null,
    ]
);