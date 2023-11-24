<section class="featured-equipment">

    <div class="yellowbox-title">
        Featured Used Equipment
    </div>

    <div class="container-product container clearfix">
        <div class="clearfix js-featured-equipment">

            <?php while( $featured->have_posts() ): $featured->the_post(); ?>
                <div class='col-sm-3'>
                    <?php cat_template('used/loop/content-single'); ?>
                </div>
            <?php endwhile; ?>

        </div>
    </div>

</section>
