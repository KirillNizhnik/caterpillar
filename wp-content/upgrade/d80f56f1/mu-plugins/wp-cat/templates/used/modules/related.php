<section class="featured-equipment">

    <div class="yellowbox-title">
        Related Used Equipment
    </div>

    <div class="container-product container clearfix">
        <div class="clearfix js-related-equipment">

            <?php while( $related_used->have_posts() ): $related_used->the_post(); ?>
                <div class='col-sm-4'>
                    <?php cat_template('used/loop/content-single'); ?>
                </div>
            <?php endwhile; ?>

        </div>
    </div>

</section>