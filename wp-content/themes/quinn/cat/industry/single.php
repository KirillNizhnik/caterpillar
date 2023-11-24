<?php get_header(); ?>

<?php echo get_template_part('partials/masthead') ?>

<?php if( have_posts() ): while( have_posts() ): the_post(); ?>

    <?php
        $industry = CAT()->industry();
        //var_dump($industry->products());
        //die();
    ?>

    <div class="flexbox">

        <div class="container">
            <?php //get_sidebar(); ?>

            <article class="page-article flexbox__item">
                <?php the_content(); ?>

                
                <?php //echo do_shortcode('[industry-families]'); 
                if( have_rows('families_selection')) :
                   while ( have_rows('families_selection') ) : the_row(); 
                    if (get_row_layout() == 'families_or_pages_selection') :
                        
                    $industry_families = array();
                    if(get_sub_field('new_machine_families')) {
                        $industry_families[] = get_sub_field('new_machine_families');
                    }
                    if(get_sub_field('new_attachment_families')) {
                        $industry_families[] = get_sub_field('new_attachment_families');
                    }
                    if(get_sub_field('power_systems')) {
                        $industry_families[] = get_sub_field('power_systems');
                    }
                    if(get_sub_field('site_support_products')) {
                        $industry_families[] = get_sub_field('site_support_products');
                    }
                    if(get_sub_field('home_and_outdoor_power')) {
                        $industry_families[] = get_sub_field('home_and_outdoor_power');
                    }


                     ?>
                    <div class="flex-image-cards">
                        <?php
                    //tax loopthrough
                    foreach($industry_families as $families) {
                        foreach($families as $family) {
                            //var_dump($family);
                            cat_template('new/loop/content-family', array('family' => $family));
                            
                        }
              
                    }
                    
                        //pages loopthrough
                        if( have_rows('pages')) :
                            while ( have_rows('pages') ) : the_row(); 
                            if (get_sub_field('individual_page')) {
                                $family = get_sub_field('individual_page');
                                //var_dump(get_the_post_thumbnail_url($family));
                                cat_template('new/loop/content-page', array('family' => $family));
                            }
                                
                            
                           
                           endwhile; 
                        endif;?>
                        
                    </div>
                    <?php
                    
                    endif;
                // End loop.
                    endwhile;
                

                endif;
                ?>
                
               
               
               
                <?php
               // $industry_families = $industry->products();
                //if( !empty($industry_families) ): ?>

               <!-- <div class="product-item-card__list">
                    <div class="row">
                    <?php
                        //foreach($industry_families as $family) {
                        //    cat_template('new/loop/content-family', array('family' => $family));
                       // }
                    ?>
                    </div>
                </div> -->

                <?php //endif; ?>
                <?php echo get_field('content_after_feed', get_the_ID()); ?>
                
               
            </article>
        </div>
        
    </div>


<?php endwhile; endif; ?>


<?php get_footer();