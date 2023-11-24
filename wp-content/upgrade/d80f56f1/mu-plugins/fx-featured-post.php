<?php
/**
 * Plugin Name: Featured Post
 * Version: 1.0
 * Description: Adds functionality for setting posts as "featured".
 * Author: The WebFX Team
 * Author URI: https://www.webfx.com
 * Plugin URI: https://www.webfx.com
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class FX_Featured_Post {

    protected static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        add_action( 'pre_get_posts', array( $this, 'maybe_exclude_featured_post' ) );
        add_action( 'acf/init', array( $this, 'add_acf_field_group' ) );
    }

    public function maybe_exclude_featured_post( $query ) {
        // Conditionals to affect only the main query on the blog page ( Settings -> Reading )
        if ( $query->is_home() && $query->is_main_query() ) {
            $featured_posts = get_posts(
                array(
                    'posts_per_page' => -1,
                    'meta_key'       => 'post_featured',
                    'meta_value'     => '1',
                    'fields'         => 'ids',
                )
            );
            $query->set( 'post__not_in', $featured_posts );
        }
    }

    public function add_acf_field_group() {
        acf_add_local_field_group(
            array(
                'key'                   => 'group_5aaa8e563750f',
                'title'                 => 'Featured Post',
                'fields'                => array(
                    array(
                        'key'               => 'field_5aaa8e5e332be',
                        'label'             => 'Feature Post?',
                        'name'              => 'post_featured',
                        'type'              => 'true_false',
                        'instructions'      => '',
                        'required'          => 0,
                        'conditional_logic' => 0,
                        'wrapper'           => array(
                            'width' => '',
                            'class' => '',
                            'id'    => '',
                        ),
                        'message'           => '',
                        'default_value'     => 0,
                        'ui'                => 1,
                        'ui_on_text'        => '',
                        'ui_off_text'       => '',
                    ),
                ),
                'location'              => array(
                    array(
                        array(
                            'param'    => 'post_type',
                            'operator' => '==',
                            'value'    => 'post',
                        ),
                    ),
                ),
                'menu_order'            => 0,
                'position'              => 'side',
                'style'                 => 'default',
                'label_placement'       => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen'        => '',
                'active'                => 1,
                'description'           => '',
            )
        );
    }
}

/**
 * Returns the main instance of FX_Featured_Post to prevent the need to use globals.
 *
 * @since  1.0
 * @return FX_Featured_Post
 */
function FX_Featured_Post() {
    return FX_Featured_Post::instance();
}
add_action( 'plugins_loaded', 'FX_Featured_Post' );
