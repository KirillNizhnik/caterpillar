<?php get_header(); ?>

<?php echo get_template_part('partials/masthead'); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<section class="container single-blog">
    <div class="row">
        <div class="col-md-8 col-sm-12 col-xs-12">
            <div class="post-wrap">
                <?php if( get_field( 'blog_hero_image' ) ): ?>
                    <div class="deals-image-wrap blog-single">
                        <?php echo fx_get_image_tag( get_field( 'blog_hero_image' ), 'deals-image' ); ?>
                    </div>
                <?php endif; ?>
                <div class="date-author">
                    <p class="post-date"><?php the_date( 'F j, Y'); ?>
                        <!-- <span class="post-author"><?php// the_author(); ?></span> -->
                    </p>
                </div>
                <div class="post-content-wrap">
                    <?php the_content(); ?>
                    <?php get_template_part( 'partials/social-share' ); ?>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-12 col-xs-12">
            <div class="blog-sidebar">
                <?php get_sidebar(); ?>
            </div>
        </div>
    </div>
</section>
<?php endwhile; endif; ?>

<?php get_footer();
