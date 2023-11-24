<?php get_header(); ?>

<?php get_template_part('partials/masthead'); ?>

<?php $pagination = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1; ?>
<?php if ( 1 === $pagination ) : // Only show Featured post on first page of posts ?>
    <?php
        $featured_post_query = new WP_Query(
            [
                'posts_per_page' => 1,
                'meta_key'       => 'post_featured',
                'meta_value'     => '1',
            ] 
        );
    ?>
    <?php if ( $featured_post_query->have_posts() ) : while ( $featured_post_query->have_posts() ) : $featured_post_query->the_post(); ?>
        <!-- featured post HTML goes here -->
    <?php endwhile; endif; wp_reset_postdata(); ?>
<?php endif; ?>

<?php if( have_posts() ): ?>
    <section class="blog-listing-container js-load-more-block">
        <div class="container">   
            <div class="row">     
                <div class="blog-listing js-load-more-posts col-md-8">    
                    <?php while( have_posts() ): the_post(); ?>
                        <?php get_template_part( 'partials/loop-content' ); ?>
                    <?php endwhile; ?>
                </div>
                <div class="blog-sidebar col-md-4"> 
                    <?php get_sidebar(); ?>
                </div>
                <div class="blog-listing__pagination">
                    <div class="col-xxs-12">
                        <?php get_template_part( 'partials/pagination' ); ?> 
                    </div>
                </div>        
            </div> 
        </div> 
    </section>
<?php endif; ?>


<?php get_footer(); ?>