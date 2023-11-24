<?php if( have_posts() ): while( have_posts() ): the_post(); ?>

    <?php
        $industry = CAT()->industry();
    ?>

    <div class="flexbox">
        <?php get_sidebar(); ?>

        <article class="page-article flexbox__item">
            <?php the_content(); ?>

            <?php
            $industry_families = $industry->products();
            if( !empty($industry_families) ): ?>

            <div class="product-item-card__list">
                <div class="row">
                <?php
                    foreach($industry_families as $family) {
                        cat_template('new/loop/content-family', array('family' => $family));
                    }
                ?>
                </div>
            </div>

            <?php endif; ?>

            <?php the_field('secondary_content'); ?>
        </article>
    </div>


<?php endwhile; endif; ?>