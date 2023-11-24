<?php

if(!function_exists('cat_family_shortcode'))
{
    function cat_family_shortcode( $atts )
    {

        $atts = shortcode_atts(
            array(
                'type' => ''
                ,'parent' => 0
                ,'limit' => ''
                ,'exclude' => ''
            )
            ,$atts
            ,'cat-family'
        );


        if( empty($atts['type']) )
            return '';

        $taxonomy =  (false !== strpos( $atts['type'], '_family')) ? $atts['type'] : $atts['type'].'_family';

        if( ! is_numeric($atts['parent']) )
        {
            $term = get_term_by( 'slug', $atts['parent'], $taxonomy );
            $atts['parent'] = $term->term_id;
        }


        $args = array(
            'hide_empty' => false
            ,'parent'    => $atts['parent'],
           
        );
        if($atts['type'] == "cat_new_power_family") {
            $args = array(
            'hide_empty' => true,
            'parent' => 0
           
        );
        }

        if( ! empty($atts['limit']) ) {
            $includes = explode(',', $atts['limit']);

            foreach ($includes as &$value) {
                if( ! is_numeric($value) ) {
                    $term = get_term_by( 'slug', $value, $taxonomy );
                    $value = $term->term_id;
                }
            }

            $args['include'] = $includes;
        }

        if( ! empty($atts['exclude']) ) {
            $excludes = explode(',', $atts['exclude']);
            foreach ($excludes as &$value) {
                if( ! is_numeric($value) ) {
                    $term = get_term_by( 'slug', $value, $taxonomy );
                    $value = $term->term_id;
                }
            }
            $args['exclude'] = $excludes;
        }

        $view = ( strpos($taxonomy, 'cat_new_') === 0 )
                  ? get_cat_view_path('new/family-list')
                  : get_cat_view_path('used/family-list');


        $families = get_terms($taxonomy, $args);

        // include our template
        ob_start();
        echo apply_filters('cat_family_before_template', '');
        include $view;
        echo apply_filters('cat_family_after_template', '');
        return ob_get_clean();
    }
}
add_shortcode( 'cat-family', 'cat_family_shortcode' );
