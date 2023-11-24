<?php
/*
Plugin Name: Deals and Specials
Plugin URI: http://webpagefx.com/
Description: Registers Deals and Specials post type
Version: 1.0
Author: WebpageFX
Author URI: http://www.webpagefx.com/
*/

add_action('init', 'register_deals_posttype');

function register_deals_posttype()
{
    $labels = array(
        'name'               => _x( 'Deals and Specials', 'post type general name', 'deals_special' ),
        'singular_name'      => _x( 'Deals and Specials', 'post type singular name', 'deals_special' ),
        'menu_name'          => _x( 'Deals and Specials', 'admin menu', 'deals_special' ),
        'name_admin_bar'     => _x( 'Deals and Specials', 'add new on admin bar', 'deals_special' ),
        'add_new'            => _x( 'Add New', 'product', 'deals_special' ),
        'add_new_item'       => __( 'Add New Deals and Specials', 'deals_special' ),
        'new_item'           => __( 'New Deals and Specials', 'deals_special' ),
        'edit_item'          => __( 'Edit Deals and Specials', 'deals_special' ),
        'view_item'          => __( 'View Deals and Specials', 'deals_special' ),
        'all_items'          => __( 'All Deals & Specials', 'deals_special' ),
        'search_items'       => __( 'Search Deals and Specials', 'deals_special' ),
        'parent_item_colon'  => __( 'Parent Deals and Specials:', 'deals_special' ),
        'not_found'          => __( 'No products found.', 'deals_special' ),
        'not_found_in_trash' => __( 'No products found in Trash.', 'deals_special' )
    );


    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_rest' => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        // 'rewrite'            => array( 'slug' => 'deals-and-specialz' ),
        'capability_type'    => 'post',
        'menu_icon'          => 'dashicons-clipboard',
        'has_archive'        => true,
        'hierarchical'       => false,
        'exclude_from_search' => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
        // 'taxonomies'         => array( 'deals-category', 'category' )
    );

    register_post_type( 'deals-specials', $args );
}  

function taxonomies() {
    $taxonomies = array();

    $taxonomies['deals_category'] = array(
        'hierarchical'  => true,
        'query_var'     => 'deals-category',
        'rewrite'       => array(
            'slug'      => 'deals-categ'
        ),
        'labels'            => array(
            'name'          => 'Deals Categories',
            'singular_name' => 'Deals Categories',
            'edit_item'     => 'Edit Deals Categories',
            'update_item'   => 'Update Deals Categories',
            'add_new_item'  => 'Add Deals Categories',
            'new_item_name' => 'Add New Deals Categories',
            'all_items'     => 'All Deals Categories',
            'search_items'  => 'Search Deals Categories',
            'popular_items' => 'Popular Deals Categories',
            'separate_items_with_commas' => 'Separate Deals Categories with Commas',
            'add_or_remove_items' => 'Add or Remove Deals Categories',
            'choose_from_most_used' => 'Choose from most used categories',
        ),
        'show_admin_column' => true
    );

    $taxonomies['specials_category'] = array(
            'hierarchical'  => true,
            'query_var'     => 'location',
            'rewrite'       => array(
                'slug'      => 'specials-categ' 
            ),
            'labels'            => array(
                'name'          => 'Specials Categories',
                'singular_name' => 'Specials Categories',
                'edit_item'     => 'Edit Specials Categories',
                'update_item'   => 'Update Specials Categories',
                'add_new_item'  => 'Add Specials Categories',
                'new_item_name' => 'Add New Specials Categories',
                'all_items'     => 'All Specials Categories',
                'search_items'  => 'Search Specials Categories',
                'popular_items' => 'Popular Specials Categories',
                'separate_items_with_commas' => 'Separate Specials Categories Categories with Commas',
                'add_or_remove_items' => 'Add or Remove Specials Categories Categories',
                'choose_from_most_used' => 'Choose from most used categories',
            ),
            'show_admin_column' => true
        );

    flush_rewrite_rules();

    foreach( $taxonomies as $name => $args ) {
        register_taxonomy( $name, array( 'deals-specials' ), $args );
    }
}
add_action( 'init', 'taxonomies' );


// function filter_post_type_link($link, $post)
// {
//     if ($post->post_type != 'deals-specials')
//         return $link;

//     if ($cats = get_the_terms($post->ID, 'deals_category'))
//         $link = str_replace('%deals%', array_pop($cats)->slug, $link);
//     return $link;
// }
// add_filter('post_type_link', 'filter_post_type_link', 10, 2);

// function filter_post_type_link_location($link, $post)
// {
//     if ($post->post_type != 'deals-specials')
//         return $link;

//     if ($cats = get_the_terms($post->ID, 'specials_category'))
//         $link = str_replace('%specials%', array_pop($cats)->slug, $link);
//     return $link;
// }
// add_filter('post_type_link', 'filter_post_type_link_location', 10, 2);


function na_remove_slug( $post_link, $post, $leavename ) {

    if ( 'deals-specials' != $post->post_type || 'publish' != $post->post_status ) {
        return $post_link;
    }

    $post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );

    return $post_link;
}
add_filter( 'post_type_link', 'na_remove_slug', 10, 3 );

function na_parse_request( $query ) {

    if ( ! $query->is_main_query() || 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) {
        return;
    }

    if ( ! empty( $query->query['name'] ) ) {
        $query->set( 'post_type', array( 'post', 'deals-specials', 'page' ) );
    }
}
add_action( 'pre_get_posts', 'na_parse_request' );