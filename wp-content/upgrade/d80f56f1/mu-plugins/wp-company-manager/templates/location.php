<main id="page-body" <?php post_class('page-body'); ?>>

<?php if ( have_posts() ): while( have_posts() ): the_post(); ?>

    <?php $location = new CM_Location( get_the_ID() ); ?>

    <script>
        var WPCM_LOCATION_ID = <?php echo get_the_ID(); ?>
    </script>

    <header class="masthead" id="masthead">
        <h2><?php the_title() ?></h2>
    </header>

    <?php echo do_shortcode( '[locations-map]' ); ?>

    <section class="page-content">

        <?php the_content(); ?>

        <section class="location-listing">
            <div class="location-listing__address">
                <?php echo $location->address; ?>
            </div>
        <?php if ( ! empty( $location->email ) ): ?>
            <div class="location-listing__section">
                <?php echo $location->email; ?>
            </div>
        <?php endif; ?>
        <?php if ( ! empty( $location->phone ) ): ?>
            <div class="location-listing__section">
                <?php echo $location->phone; ?>
            </div>
        <?php endif; ?>
        <?php if ( ! empty( $location->hours ) ): ?>
            <div class="location-listing__section">
                <?php echo $location->hours; ?>
            </div>
        <?php endif; ?>
            <div class="location-listing__section">
                <a class="btn btn-tertiary" target="_blank" href="<?php echo $location->directions; ?>">Get Directions</a>
            </div>
        </section>

    </section>

<?php endwhile; endif; ?>

</main>
