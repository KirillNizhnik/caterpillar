<?php

if(!function_exists('cat_featured_shortcode'))
{
    function cat_featured_shortcode( $atts )
    {
        $atts = extract(shortcode_atts(
            array(
                'posts_per_page' => -1,
            )
            ,$atts
            ,'cat-featured'
        ));

        $featured = new WP_Query( array(
            'post_type'       => 'cat_used_machine'
            ,'post_status'    => 'publish'
            ,'posts_per_page' => $posts_per_page
            ,'meta_key'       => 'featured'
            ,'meta_value_num' => 1
        ));

        $view = apply_filters( 'cat_featured_shortcode_template', 'used/modules/featured', $atts );

        // include our template
        ob_start();

        if ($featured->have_posts() )
        {
            include get_cat_view_path($view);
        }
        wp_reset_postdata();

        return ob_get_clean();
    }
}

add_shortcode( 'cat-featured', 'cat_featured_shortcode' );