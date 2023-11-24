<?php

/**
 * Bootstrap File
 * File is only used to load in the necessary files for the theme - no
 * functions should be added here directly.
 *
 * Please keep in mind that only presentation functionality should be added
 * inside the theme. Any additional functionality - custom post types,
 * taxonomies, etc. - should be added in plugins or mu-plugins to allow
 * the theme to be changed without affecting site functionality.
 */


// Remove unnecessary items from head
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_generator');

// Grab path for includes
$theme_path = get_template_directory();


/**
 * Include helper functions
 * Contains free-standing functions (not attached to specific WP hooks) to be used in templates, etc
 */
require_once $theme_path . '/inc/theme/helper-functions.php';


/**
 * Include admin-related functionality
 * Contains functions attached to admin-specific WP hooks
 */
require_once $theme_path . '/inc/theme/admin.php';


/**
 * Include frontend-related functionality
 * Contains functions attached to frontend-specific WP hooks
 */
require_once $theme_path . '/inc/theme/frontend.php';

/**
 * Include assets
 * Contains logic for enqueuing styles and scripts
 */
require_once $theme_path . '/inc/theme/assets.php';


/**
 * Include ACF blocks
 * Contains logic for registering ACF blocks
 */
require_once $theme_path . '/inc/theme/acf-blocks.php';


/**
 * Include shortcodes
 * Each shortcode should be a separate file in the /inc/shortcodes directory
 */
// TODO include shortcode files here (if applicable)
require_once $theme_path . '/inc/shortcodes/industry.php';
require_once $theme_path . '/inc/theme/breadcrumbs.php';

/**
 * Include classes
 * Each class should be a separate file in the /inc/classes directory
 */
// TODO include class files here (if applicable)

function wpb_custom_new_menu()
{
    register_nav_menu('my-custom-menu', __('My Custom Menu'));
}

add_action('init', 'wpb_custom_new_menu');

add_action('widgets_init', 'deals_new_sidebar');
function deals_new_sidebar()
{
    $args = array(
        'name' => 'Deals Sidebar',
        'id' => 'deals-sidebar',
        'description' => 'Sidebar for deals and specials.',
        'class' => '',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h2 class="widgettitle">',
        'after_title' => '</h2>'
    );
    register_sidebar($args);
}

// Disable SearchWP's logging of searches for anyone who is logged in.
add_filter('searchwp\statistics\log', function ($enabled, $query) {
    return !is_user_logged_in();
}, 20, 2);

function sitemap_exclude_taxonomy($excluded, $taxonomy)
{
    return $taxonomy === '__cat_type_slug_406__';
}

add_filter('wpseo_sitemap_exclude_taxonomy', 'sitemap_exclude_taxonomy', 10, 2);

/**
 * Excludes posts from XML sitemaps.
 *
 * @return array The IDs of posts to exclude.
 */
/*function exclude_posts_from_xml_sitemaps() {
    $posts = get_posts([
      'post_type' => 'cat_new_machine',
      'numberposts' => -1
       'order'    => 'ASC'
    ]);
    return [8152,8154,8156,8265,8158,8160,8162];
}*/

//add_filter( 'wpseo_exclude_from_sitemap_by_post_ids', 'exclude_posts_from_xml_sitemaps' );

function ti_custom_javascript()
{
    if (function_exists('cn_cookies_accepted') && cn_cookies_accepted()) {
        ?>
        <script type="text/javascript">
            jQuery(function () {
                console.log('accepted cookies..');
                setTimeout(function () {
                    jQuery('#cookie-notice').css('display', 'none');
                }, 500);
            });
        </script>
        <?php
    }
}

add_action('wp_head', 'ti_custom_javascript');

function writeError($data)
{
    $pluginlog = plugin_dir_path(__FILE__) . 'debug.log';
    $message = json_encode($data) . PHP_EOL;
    error_log($message, 3, $pluginlog);
}

function cat_filter_empty_families_from_menu($items, $menu, $args)
{
    if (!is_admin()) {
        foreach ($items as $key => $item) {
            if ($item->type == 'taxonomy' && ($item->object == 'family' || $item->object === 'used-family')) {
                $category = get_term($item->object_id, $item->object);
                if ($category && $category->count == 0) {
                    unset($items[$key]);
                }
            }
        }
    }
    return array_values($items);
}

add_filter('wp_get_nav_menu_items', 'cat_filter_empty_families_from_menu', 10, 3);


