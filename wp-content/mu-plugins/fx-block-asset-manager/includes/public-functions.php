<?php


/**
 * These functions are intended to be available across the entire site as a way of using the FX BAM plugin or providing
 * accessibility to FX BAM's API
 * 
 * If the function is intended to be used only by the FX BAM plugin, please add the function to helper-functions.php
 */



/**
 * Get SVG markup for block icon
 *
 * @return  string
 */
function fx_get_block_icon() {
    $filepath = get_stylesheet_directory() . '/assets/icons/block-icon.svg';

    if( is_file( $filepath ) ) {
        return file_get_contents( $filepath );
    }

    return '';
}


/**
 * Wrapper for registering an ACF block
 * 
 * @link        https://app.getguru.com/card/Tn9zzk8c/FX-ACF-Blocks Guru card for documentation
 * @internal    Requires ACF Plugin to be enabled and active.
 * 
 * @param   array   $settings   Settings to use when registering the block
 * @return  mixed               Array, if successfully generated ACF block; otherwise, false
 */
function fx_register_block( array $settings ) {
    if( !function_exists('acf_register_block_type' ) ) {
        return false;
    } else {
        $block_register = FX_Register_Blocks();
    
        return $block_register->register_block( $settings );
    }
}


/**
 * Add script
 * 
 * @see     FX_Assets()->add_script
 *
 * @param	array   $args   Stylesheet args
 * @return  mixed   True, if successful; otherwise WP_Error
 */
function fx_assets_add_script( array $args ) {
    return FX_Assets()->add_script( $args );
}


/**
 * Add stylesheet
 * 
 * @see     FX_Assets()->add_stylesheet
 *
 * @param	array   $args   Script args
 * @return  mixed   True, if successful; otherwise WP_Error
 */
function fx_assets_add_stylesheet( array $args ) {
    return FX_Assets()->add_stylesheet( $args );
}


/**
 * Registers custom plugin stylesheet dependencies for later usage
 *
 * @param	string	$plugin_handle  Plugin asset handle
 * @param   string  $handle_to_add  Asset handle to add to plugin's dependencies
 * 
 * @return  void
 */
function fx_assets_add_plugin_style( string $plugin_handle, string $handle_to_add ) {
    return FX_Assets()->register_custom_plugin_style_dependency( $plugin_handle, $handle_to_add );
}


/**
 * Registers custom plugin script dependencies for later usage
 *
 * @param	string	$plugin_handle  Plugin asset handle
 * @param   string  $handle_to_add  Asset handle to add to plugin's dependencies
 * 
 * @return  void
 */
function fx_assets_add_plugin_script( string $plugin_handle, string $handle_to_add ) {
    return FX_Assets()->register_custom_plugin_script_dependency( $plugin_handle, $handle_to_add );
}


/**
 * Returns minified CSS
 *
 * @param	string	$content    CSS
 * @return  string              Minified CSS
 */
function fx_asset_minify_css( string $content ) {
    return FX_Asset_Minifier()->minify_content( $content, 'css' );
}


/**
 * Returns minified JS
 *
 * @param	string	$content    JS
 * @return  string              Minified JS
 */
function fx_asset_minify_js( string $content ) {
    return FX_Asset_Minifier()->minify_content( $content, 'js' );
}


/**
 * Takes a CSS file and returns minified CSS
 *
 * @param	string	$content    CSS
 * @return  string              Minified CSS
 */
function fx_asset_minify_stylesheet( string $file_path ) {
    return FX_Asset_Minifier()->minify_file( $file_path, 'css' );
}


/**
 * Takes a JS file and returns minified JS
 *
 * @param	string	$content    JS
 * @return  string              Minified JS
 */
function fx_asset_minify_script( string $file_path ) {
    return FX_Asset_Minifier()->minify_file( $file_path, 'js' );
}


/**
 * Wrapper for registering a block category
 *
 * @param	string	$title      Category title
 * @param   string  $slug       Category slug
 * @param   string  $icon       Category icon (dashicon or SVG)
 * 
 * @return  void
 */
function fx_add_block_category( string $title, string $slug = '', string $icon = '' ): void {
    FX_Register_Blocks()->register_block_category( $title, $slug, $icon );
}


/**
 * Returns field data for specified block in specified post
 * 
 * This works with inner blocks, top-level blocks, or reusable blocks. 
 * 
 * If there are multiple of one type of block on a page, this will grab the data from the first one.
 * 
 * @param   mixed   $post       Post ID or post object to get data from
 * @param   string  $block_name Name of block (taken from "name" parameter from fx_register_block setting). You can 
 *                              optionally include "acf/" prefix
 * 
 * @return  mixed               Array of field data, or false on failure
 */
function fx_get_block_data( $post, string $block_name ) {
    $post = fx_bam_ensure_wp_post( $post );

    if( $post ) {
        $post_blocks = parse_blocks( $post->post_content );
    
        // recursively grab innerblocks and add to top level of array for easy data-grab
        $flattened_blocks = FX_Parse_Block_Assets()::flatten_blocks( $post_blocks );
    
        // ACF prefixes blocks with namespace so we need to make sure it's applied here
        if( 'acf/' !== substr( $block_name, 0, 4 ) ) {
            $block_name = sprintf( 'acf/%s', $block_name );
        }    
    
        foreach( $flattened_blocks as $block ) {
            if( $block_name === $block['blockName'] ) {
                return [ $block['attrs']['data'] ];
            }
        }
    }

    return false;
}