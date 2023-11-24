<?php 

$custom_terms = get_terms('specials_category');
            
            $term_array = array();
            foreach($custom_terms as $term){
                $term_array[] = $term->slug;
            }
            //var_dump($term_array);
                $args = array(
                    'post_type' => 'deals-specials',
                    'posts_per_page'=> 6,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'specials_category',
                            'field' => 'slug',
                            'terms' => $term_array
                        ),
                    ),
                );

                $loop = new WP_Query($args); 



?>

<section class="image-cards" >
    <div class="container">
        
            <?php 
            
         
                ?>
                <?php if ( $loop->have_posts() ) : ?>
                <div class="row" >
                    <?php while($loop->have_posts()) : $loop->the_post(); ?>

                        <div class="col-sm-6 image-cards-block-load-content ">
                            <div class="image-cards-block">
                                <a href="<?php the_permalink(); ?>">
                                    <div class="image-cards-block-image">
                                        <?php the_post_thumbnail( 'full' ); ?>
                                    </div>
                                    <div class="image-cards-block-content">
                                        <h6 class="image-cards-category-name">
                                            <?php
                                            $empty = get_the_terms( get_the_ID(),'specials_category' );

                                            if (!empty($empty)) {
                                                foreach ( get_the_terms( get_the_ID(),'specials_category' ) as $tax ) {
                                                echo $tax->name . '<span class="comma">,</span> ';
                                                }
                                            }
                                            ?>
                                        </h6>
                                        <h4><?php the_title(); ?></h4>
                                        <?php the_excerpt(); ?> 
                                        <span class="btn btn-primary">Read more</span>
                                    </div>
                                </a>
                            </div>
                        </div>

                    <?php endwhile; ?>
                <?php endif;
                 ?>
                  
                 <?php
                 wp_reset_query();
                 ?>
        </div>
       <?php echo do_shortcode('[facetwp facet="specials_specific_pager"]'); ?>
    </div>
   
    
</section>