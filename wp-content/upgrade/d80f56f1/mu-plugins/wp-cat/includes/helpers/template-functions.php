<?php

/**
 * Alias for cat_template
 *  - parameters switched to mimic locate_template
 */
function cat_locate_template( $template_names, $load=false, $require_once=true)
{
    return cat_template( $template_names, null, $require_once, $load );
}

/**
 * Include templates/template parts
 *
 * @param  [mixed]  $template     template(s) to load - string or array
 * @param  boolean $require_once only require once
 * @return null
 */
function cat_template( $templates, $args = array(), $require_once=false, $include=true )
{
    if (is_string($templates))
        $templates = array($templates);

    if (!is_array($templates))
        return false;

    $clean_templates = array();
    $rental_templates = array();

    global $wp_query;
    $is_rental = $wp_query->is_cat_rental;

    // Make sure each option starts with cat/ and ends with .php
    foreach ($templates as $template)
    {
        // Add cat at beginning if necessary
        if ( substr($template, 0, 4) != 'cat/' )
            $template = 'cat/' . $template;

        // Add php at end if necessary
        if ( substr($template, -4) != '.php' )
            $template .= '.php';

        $clean_templates[]= $template;

        if ($is_rental and substr($template, 4, 4) == 'new/')
        {
            $rental_templates[] = str_replace('cat/new/', 'cat/rental/', $template);
        }
    }

    $found_template = false;

    // For rental pages, check rental directories first
    if ($is_rental and !empty($rental_templates))
        $found_template = _find_cat_template($rental_templates);

    // then fall back to new
    if (!$found_template)
        $found_template = _find_cat_template($clean_templates);

    if (!$found_template)
        return false;

    if ( $args && is_array( $args ) ) {
        extract( $args );
    }

    if ($include)
    {
        if( $require_once )
            include_once $found_template;
        else
            include $found_template;
    }
    else
    {
        return $found_template;
    }
}

/**
 * Find a cat template
 *  - helper function for cat_template
 */
function _find_cat_template ($templates)
{

    $found_template = false;

    // First check theme(s)
    if ( ! $found_template = locate_template($templates, false, false) )
    {
        // Not found in theme(s), check plugin
        foreach ($templates as $template)
        {
            // Remove cat/ from beginning
            $plugin_template = CAT()->plugin_path . 'templates/' . substr($template, 4);

            if (is_file($plugin_template))
            {
                $found_template = $plugin_template;
                break;
            }
        }

    }

    return $found_template;
}

/**
 * Returns the absolute path to the template name
 *
 * @param string $template template name
 * @return string Absolute path to template
 */
function get_cat_view_path($template)
{
    return \CAT\Core\Templates::view($template);
}


/**
 * Returns the full url to the appopriate asset file
 *
 * @param string $asset asset name
 * @return string url to the file
 */
function get_cat_asset_uri($asset)
{
    return \CAT\Core\Templates::asset($asset);
}


/**
 * echo the full url to the asset file
 *
 * @param string $asset asset name
 * @return string url to the file
 */
function cat_asset_uri($asset)
{
    return get_cat_asset_uri($asset);
}


/**
 * Helper for getting the current post id if no
 * id was passed
 * @param  int $post_id  post_id that you want.
 * @return [type]          [description]
 */
function get_cat_active_post_id($post_id='')
{
    global $post;

    if( ! empty($post_id) )
        return $post_id;

    return $post->ID;
}


function cat_js_object($machine)
{
    return array(
         'url' => get_permalink()
        ,'image' => cat_sized_image( reset($machine->images), array(220,165), array( 'class' => 'img-responsive' ) )
        ,'title' => get_the_title()
        ,'model' => isset($machine->model) ? $machine->model : '<span class="unavailable">N/A</span>'
        ,'year' => isset($machine->year) ? $machine->year : '<span class="unavailable">N/A</span>'
        ,'hours' => isset($machine->hours) ? $machine->hours : '<span class="unavailable">N/A</span>'
        ,'price' => (isset($machine->price) and is_numeric($machine->price)) ? '$' . number_format($machine->price) : '<span class="unavailable">N/A</span>'
        ,'rating' => isset($machine->rating) ? $machine->rating : '<span class="unavailable">N/A</span>'
        ,'serialnumber' => isset($machine->serial_number) ? $machine->serial_number : '<span class="unavailable">N/A</span>'
        ,'manufacturer' => isset($machine->manufacturer) ? $machine->manufacturer->name : '<span class="unavailable">N/A</span>'
    );
}


function is_cat()
{
    global $wp_query;
    return $wp_query->is_cat;
}

function is_cat_new()
{
    global $wp_query;
    return $wp_query->is_cat_new;
}

function is_cat_industry()
{
    global $wp_query;
    return $wp_query->is_cat_industry;
}

function is_cat_application()
{
    global $wp_query;
    return $wp_query->is_cat_application;
}

function is_cat_used()
{
    global $wp_query;
    return $wp_query->is_cat_used;
}

function is_cat_rental()
{
    global $wp_query;
    return $wp_query->is_cat_rental;
}

function is_cat_allied()
{
    global $wp_query;
    return $wp_query->is_cat_allied;
}

function is_equipment_search()
{
    return (bool) get_query_var('equipment-search');
}

