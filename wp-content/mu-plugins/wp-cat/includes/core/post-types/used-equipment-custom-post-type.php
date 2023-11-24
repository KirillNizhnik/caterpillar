<?php
class UsedEquipmentPostType extends AbstractEquipmentPostType
{

    public function getPostType(): string
    {
        return 'used-equipment';
    }

    public function getFamilyTax(): string
    {
        return 'used-family';
    }

    public function getManufacturerTax(): string
    {
        return 'used-manufacturer';
    }

    public function register(): void
    {
        $labels = array(
            'name' => 'Used Equipment',
            'singular_name' => 'Used Equipment',
            'menu_name' => 'Used Equipment',
            'name_admin_bar' => 'Post Type',
            'add_new' => 'Add new',
            'add_new_item' => 'Used Equipment',
            'new_item' => 'Used Equipment',
            'edit_item' => 'Edit ',
            'view_item' => 'View Used Equipment',
            'all_items' => 'All Equipment',
            'search_items' => 'Search Equipment',
            'parent_item_colon' => 'Parent Equipment:',
            'not_found' => 'Not Found',
            'not_found_in_trash' => 'Not Found in trash'
        );


        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'used-product'),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 23,
            'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
            'taxonomies' => array('used-family', 'used-manufacturer'),
            'menu_icon' => 'dashicons-admin-generic',

        );

        register_post_type('used-equipment', $args);
    }

    public function registerFamilyTaxonomy(): void
    {
        $labels = array(
            'name' => 'Used Equipment Families',
            'singular_name' => 'Used Equipment Families',
            'menu_name' => 'Used Equipment Families',
            'all_items' => 'All Used Families',
            'edit_item' => 'Edit family',
            'view_item' => 'View family',
            'update_item' => 'Update family',
            'add_new_item' => 'Add new family',
            'new_item_name' => 'Name new family',
            'search_items' => 'Search family',
            'popular_items' => 'Popular families',
            'separate_items_with_commas' => 'Separate categories with families',
            'add_or_remove_items' => 'Add or delete family',
            'choose_from_most_used' => 'Choose from the most popular families',
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'used',
                'hierarchical' => true),
        );

        register_taxonomy('used-family', 'used-equipment', $args);
    }

    public function registerManufacturerTaxonomy(): void
    {
        $labels = array(
            'name' => 'Used Manufacturers',
            'singular_name' => 'Manufacturer',
            'menu_name' => 'Manufacturers',
            'all_items' => 'All Used Manufacturers',
            'edit_item' => 'Edit Manufacturer',
            'view_item' => 'View Manufacturer',
            'update_item' => 'Update Manufacturer',
            'add_new_item' => 'Add new Manufacturer',
            'new_item_name' => 'Name new Manufacturer',
            'search_items' => 'Search Manufacturer',
            'popular_items' => 'Popular Manufacturers',
            'separate_items_with_commas' => 'Separate categories with Manufacturers',
            'add_or_remove_items' => 'Add or delete Manufacturer',
            'choose_from_most_used' => 'Choose from the most popular Manufacturer',
        );


        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'used-manufacturer'),
        );

        register_taxonomy('used-manufacturer', 'used-equipment', $args);
    }


}
new UsedEquipmentPostType();