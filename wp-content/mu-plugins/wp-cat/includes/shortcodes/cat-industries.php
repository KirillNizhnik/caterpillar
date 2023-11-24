<?php

if(!function_exists('cat_industries_shortcode'))
{
    function cat_industries_shortcode( $atts )
    {
        $industries = new WP_Query(array(
            'post_type' => 'cat_industry'
            ,'posts_per_page' => -1
        ));
        
        // include our template
        ob_start();
        echo apply_filters('cat_industries_before_template', '');

        if ( $industries->have_posts() ) :
            while ( $industries->have_posts() ) :
                $industries->the_post();
                
                cat_template('industry/loop/content-single');
            endwhile;
        endif;
        
        
       

        wp_reset_postdata();
        
         
                // Check rows exists.
        if( have_rows('industries' ,get_the_ID()) ):
        
            // Loop through rows.
            while( have_rows('industries',get_the_ID()) ) : the_row();
            
            //acf possibilities
            $link = get_sub_field('industry_link',get_the_ID());
            $name = get_sub_field('industry_name',get_the_ID());
            $learn_more = get_sub_field('learn_more_text',get_the_ID());
            $thumbnail = get_sub_field('thumbnail',get_the_ID());
            ?>
            <article class="col-md-4 col-sm-6">
                <div class="image-button-box">
                    <a href="<?php echo $link;   ?>">
                        <div class="image-button-image">
                        <?php
                           // $thumbnail = get_the_post_thumbnail_url(get_the_id());
        
                            //echo cat_sized_image( $thumbnail, array(200,200), array('itemprop' => 'image') );
                            if($thumbnail){
                                echo cat_sized_image( $thumbnail, 'full', array('itemprop' => 'image') );
                            } else {
                                echo fx_get_image_tag(7339);
                            }
        
                        ?>
                        </div>
                        <div class="image-button-content">
                            <h4><?php echo $name; ?></h4>
                            <span class="btn btn-tertiary"><?php echo $learn_more; ?></span>
                        </div>
                    </a>
                </div>
            </article>
        
                 <?php       
        
            // End loop.
            endwhile;
        
        // No value.
        else :
            // Do something...
        endif;
           
        echo apply_filters('cat_industries_after_template', '');
        return ob_get_clean();

        
    }
}
add_shortcode( 'cat-industries', 'cat_industries_shortcode' );