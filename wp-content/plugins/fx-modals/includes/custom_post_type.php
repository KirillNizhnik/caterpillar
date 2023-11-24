<?php

//Register custom post type

function fx_modals_register_custom_post_type() {
    $supports = array(
    'title', // post title
    'author', // post author
    'thumbnail', // featured images
    'custom-fields', // custom fields
    'revisions', // post revisions
    );
    $labels = array(
    'name' => _x('Modals', 'plural'),
    'singular_name' => _x('Modal', 'singular'),
    'menu_name' => _x('Modals', 'admin menu'),
    'name_admin_bar' => _x('Modals', 'admin bar'),
    'add_new' => _x('Add New', 'add new'),
    'add_new_item' => __('Add New Modal'),
    'new_item' => __('New Modal'),
    'edit_item' => __('Edit Modal'),
    'view_item' => __('View Modal'),
    'all_items' => __('All Modals'),
    'search_items' => __('Search Modals'),
    'not_found' => __('No Modals found.'),
    );
    $args = array(
    'supports' => $supports,
    'labels' => $labels,
    'public' => true,
    'query_var' => true,
    'rewrite' => array('slug' => 'modals'),
    'has_archive' => true,
    'hierarchical' => false,
    );
    register_post_type('fx_modal', $args);
}
add_action('init', 'fx_modals_register_custom_post_type');