<?php

if(!function_exists('cat_equipment_shortcode'))
{
    function cat_equipment_shortcode( $atts )
    {
        $atts = extract(shortcode_atts(
            array(
                'family' => 0
                ,'type' => ''
            )
            ,$atts
            ,'cat-new-families'
        ));

        $args = array(
            'post_type' => $type
            ,'posts_per_page' => -1
        );

        if($family) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => $type.'_family',
                    'field'    => ( is_numeric($family) ) ? 'term_id' : 'slug',
                    'terms'    => $family,
                )
            );
        }

        $args = apply_filters( 'cat_equipment_shortcode_args', $args );

        $view = ( strpos($type, 'cat_new_') === 0 )
                  ? 'new/loop/content-single'
                  : 'used/loop/content-single';

        $view = apply_filters( 'cat_equipment_shortcode_template', $view, $args );


        $equipment = new WP_Query($args);

        // include our template
        ob_start();
        if($equipment->have_posts()):
            echo apply_filters('cat_equipment_shortcode_before_template', '<section class="product-item-card__list"><div class="row soft--sides">', $args);
            while($equipment->have_posts()): $equipment->the_post();

                cat_template($view);

            endwhile;
            echo apply_filters('cat_equipment_shortcode_after_template', '</div></section>', $args);
        endif; wp_reset_postdata();
        return ob_get_clean();
    }
}
add_shortcode( 'cat-equipment', 'cat_equipment_shortcode' );