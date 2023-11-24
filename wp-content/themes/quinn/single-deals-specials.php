<?php get_header(); ?>
<?php get_template_part( 'partials/masthead' ); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<section class="container">
    <div class="deals-wrap">
        <?php if( get_field( 'deals_and_specials_image' ) ): ?>
            <div class="deals-image-wrap push-bottom">
                <?php echo fx_get_image_tag( get_field( 'deals_and_specials_image' ), 'deals-image' ); ?>
            </div>
        <?php endif; ?>
        <div class="deals-wrap-content">
            <div class="deals-content-wrap">
                <?php the_content(); ?>
                <?php get_template_part( 'partials/social-share' ); ?>
            </div>
        </div>
    </div>
</section>
<?php endwhile; endif; ?>

<?php get_footer();
