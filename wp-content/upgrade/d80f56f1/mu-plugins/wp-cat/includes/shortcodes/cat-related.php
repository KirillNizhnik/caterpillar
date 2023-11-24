<?php

if(!function_exists('cat_related_shortcode'))
{
    function cat_related_shortcode( $atts )
    {
        $atts = extract(shortcode_atts(
            array(
                'product_id' => '',
                'posts_per_page' => -1,
            )
            ,$atts
            ,'cat-related'
        ));

        $view = apply_filters( 'cat_related_shortcode_template', 'used/modules/related', $atts );

        if (empty($product_id))
        {
            // Attempt to get product id automatically
            $queried = get_queried_object();
            if (is_object($queried) and !empty($queried->ID) and !empty($queried->post_type))
            {
                $cat_new_types = array_values(CAT()->get_class_post_type_relation());
                if (in_array($queried->post_type, $cat_new_types))
                {
                    $product_id = $queried->ID;
                }
            }
        }

        if (empty($product_id))
            return false;

        $product = CAT()->product($product_id);

        $related_used = $product->related_used_equipment(array('posts_per_page' => $posts_per_page));

        // include our template
        ob_start();

        if ($related_used->have_posts() )
        {
            include get_cat_view_path($view);
        }
        wp_reset_postdata();

        return ob_get_clean();
    }
}

add_shortcode( 'cat-related', 'cat_related_shortcode' );