<?php get_header(); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <?php // TODO if using a homepage masthead slider, follow instructions to configure the homepage block. Otherwise, delete this comment. ?>
    <article class="page-content">
        <?php the_content(); ?>
    </article>

<?php endwhile; endif; ?>

<?php echo do_shortcode('[quinnpopup]'); ?>

<?php get_footer();
