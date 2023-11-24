<?php

if(!function_exists('company_representatives_search_shortcode'))
{
    function company_representatives_search_shortcode( $atts )
    {
        $zip_searched = ! empty( $_POST['zipcode'] ) ? esc_html( $_POST['zipcode'] ) : '';

        // tell the scripts that they should load
        CM_Template::$add_scripts = true;

        // Reps
        $q = new WP_Query(array(
            'post_type'       => 'rep'
            ,'posts_per_page' => -1
            ,'orderby' => 'title'
            ,'order' => 'ASC'
            ,'meta_query' => array(
                array(
                    'key' => 'zipcode'
                    ,'value' => $zip_searched,
                    'compare' => 'LIKE',
                    
                )
            )
        ));

        $representatives = array();

        while ( $q->have_posts() ) {
            $q->the_post();
            $representatives[] = WPCM()->rep( get_the_ID() );
        }


        $industries = array();

        // order by industries
        foreach ( $representatives as $rep ) {
            if( isset( $rep->industry ) ) {
                if ( !isset( $industries[$rep->industry] ) ) {
                    $industries[$rep->industry] = array( $rep );
                } else {
                    $industries[$rep->industry][] = $rep;
                }
            }
        }

        wp_reset_postdata();

        // include our template
        ob_start();
        wpcm_template( 'rep-list', array( 'representatives' => $industries, 'zip_searched' => $zip_searched ) );
        return ob_get_clean();
        
    }
}
add_shortcode( 'rep-search', 'company_representatives_search_shortcode' );
