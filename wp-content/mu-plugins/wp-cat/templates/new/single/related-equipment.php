<?php
global $wpdb;
$product = CatView::instance();


$related_products = $wpdb->get_col($wpdb->prepare(
        "SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key='related' AND meta_value LIKE %s"
        ,'%'.$product->equipment_id.'%'
));

$equipment = new WP_Query(array(
    'post_type'       => 'cat_new_equipment'
    ,'post_status'    => 'publish'
    ,'posts_per_page' => count($product->related['product']['related'])
    ,'post__in'       => $related_products
));


if($equipment->have_posts()): ?>

<section class="accordian__group">
    <h3 class="accordian__toggle">Related Equipment</h3>
    <div class="accordian__group__content row cf">

        <?php while($equipment->have_posts()): $equipment->the_post(); ?>
            <?php
                $family = array_shift(get_the_terms(get_the_id(), 'cat_new_equipment_family'));
            ?>

            <div class="col-lg-3 col-md-4 col-xs-6 work-tool-link hard">
                <a href="<?php the_permalink(); ?>">
                    <div class="work-tool-link__button-overlay">
                        <div class="work-tool-link__button button button--primary">View Details</div>
                    </div>

                    <?php cnf_preview(get_the_id(), 'full'); ?>
                    <span class="work-tool-link__name text--small"><?php the_title(); ?></span>
                </a>
            </div>

        <?php endwhile; wp_reset_postdata(); ?>
    </div>
</section>
<?php endif; ?>