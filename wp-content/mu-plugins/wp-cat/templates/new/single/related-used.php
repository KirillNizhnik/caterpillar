<?php
    $product = CatView::instance();
    $related = get_cnf_related_equipment(array('posts_per_page' => -1));
?>

<?php if($related->have_posts()): ?>
<div class="row row--light-gray row--padding cf" id="related-used">

	<header class="text--center">
		<h2 class="section-title title--bigger">Related Used Equipment</h2>
	</header>

</div>

<div class="row row--light-gray ne-product__related cf">

    <section class="col-xs-12 ne-product__section hard">

        <div class="frame" id="related-scroller">
            <div class="slidee">
            <?php while($related->have_posts()): $related->the_post(); ?>
                <div class="ne-product__related-item">
                    <a href="<?php the_permalink(); ?>" class="featured-equipment__block block-link">
                        <div class="featured-equipment__content">
                            <h4 class="featured-equipment__name flush"><?php the_title(); ?></h4>
                            <div class="featured-equipment__price float--left"><?php used_equipment_price(get_the_id()); ?></div>
                            <div class="featured-equipment__link text--yellow float--left">View Details <span class="icon-fast-forward"></span></div>
                        </div>
                        <?php used_equipment_preview(get_the_id(), array(276,210), array('class' => 'block-link__image' )); ?>
                    </a>
                </div>
            <?php endwhile; ?>
            </div>
        </div>
        <ul class="page-bar" id="related-scroller__page-bar"></ul>
        <button class="page-bar__prev-arrow" id="related-scroller__prev-arrow"></button>
        <button class="page-bar__next-arrow" id="related-scroller__next-arrow"></button>
    </section>

</div>

<?php endif; wp_reset_postdata(); ?>
