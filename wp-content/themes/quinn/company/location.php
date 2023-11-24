<?php get_header(); ?>

<main id="page-body" <?php post_class('page-body'); ?>>

<?php if ( have_posts() ): while( have_posts() ): the_post(); ?>

    <?php $location = new CM_Location( get_the_ID() ); ?>

    <script>
        var WPCM_LOCATION_ID = <?php echo get_the_ID(); ?>
    </script>

    <section class="masthead-inner">
        <div class="masthead-inner-texture-image">
            <!-- <img src="../wp-content/themes/quinn/assets/img/masthead-inner-texture-image.jpg" class="img-responsive" alt=""> -->
            <?php echo fx_get_image_tag( 404 ); ?>
        </div>
        <div class="masthead-inner-overlay">
            <div class="container">
                    <h1><?php the_title() ?></h1>
                <?php
                    if( function_exists( 'yoast_breadcrumb' ) ) {
                        yoast_breadcrumb( '<div class="breadcrumbs hidden-sm-down">', '</div>' );
                    };
                ?>
            </div>
        </div>
    </section>

    <section class="page-content single-location fx-test">
        <?php the_content(); ?>
    </section>

<?php endwhile; endif; ?>

</main>

<?php get_footer();