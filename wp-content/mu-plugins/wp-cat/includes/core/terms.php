<?php

/**
 * Adds our taxonomy term metadata to DB
 * @return [type] [description]
 */

function cat_taxonomy_metadata_wpdbfix()
{
    global $wpdb;
    $termmeta_name = 'cat_termmeta';

    $wpdb->cat_termmeta = $wpdb->prefix . $termmeta_name;
    $wpdb->tables[] = 'cat_termmeta';
}
add_action( 'init', 'cat_taxonomy_metadata_wpdbfix', 0 );
add_action( 'switch_blog', 'cat_taxonomy_metadata_wpdbfix', 0 );



/**
 * CAT Term API - Update term meta
 *
 * @access public
 * @param mixed $term_id
 * @param mixed $meta_key
 * @param mixed $meta_value
 * @param string $prev_value (default: '')
 * @return bool
 */

function update_cat_term_meta( $term_id, $meta_key, $meta_value, $prev_value = '' )
{
    return update_metadata( 'cat_term', $term_id, $meta_key, $meta_value, $prev_value );
}



/**
 * CAT Term API - Add term meta
 *
 * @access public
 * @param mixed $term_id
 * @param mixed $meta_key
 * @param mixed $meta_value
 * @param bool $unique (default: false)
 * @return bool
 */

function add_cat_term_meta( $term_id, $meta_key, $meta_value, $unique = false )
{
    return add_metadata( 'cat_term', $term_id, $meta_key, $meta_value, $unique );
}



/**
 * CAT Term API - Delete term meta
 *
 * @access public
 * @param mixed $term_id
 * @param mixed $meta_key
 * @param string $meta_value (default: '')
 * @param bool $delete_all (default: false)
 * @return bool
 */

function delete_cat_term_meta( $term_id, $meta_key, $meta_value = '', $delete_all = false )
{
    return delete_metadata( 'cat_term', $term_id, $meta_key, $meta_value, $delete_all );
}



/**
 * CAT Term API - Get term meta by key
 *
 * @access public
 * @param mixed $term_id
 * @param mixed $key
 * @param bool $single (default: true)
 * @return mixed
 */

function get_cat_term_meta( $term_id, $key = null, $single = true )
{
    return get_metadata( 'cat_term', $term_id, $key, $single );
}


/**
 * CAT Term API - Get all of terms meta
 *
 * @access public
 * @param mixed $term_id
 * @param mixed $key
 * @param bool $single (default: true)
 * @return mixed
 */

function get_cat_term_custom( $term_id )
{
    return get_metadata( 'cat_term', $term_id );
}
