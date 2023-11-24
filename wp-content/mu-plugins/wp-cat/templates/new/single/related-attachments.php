<?php
global $wpdb;
$product = CatView::instance();

$related_attachments = $wpdb->get_col("SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key='equipment_id' AND meta_value IN (".implode(',',$product->related['product']['related']).")");
$attachments = new WP_Query(array(
    'post_type'      => 'cat_new_attachment'
    ,'post_status'    => 'publish'
    ,'posts_per_page' => count($product->related['product']['related'])
    ,'post__in' => $related_attachments
));

if($attachments->have_posts()): ?>

<section class="accordian__group">
    <h3 class="accordian__toggle">Worktool Attachments</h3>
    <div class="accordian__group__content row cf">

        <?php while($attachments->have_posts()): $attachments->the_post(); ?>
            <?php
                $family = array_shift(get_the_terms(get_the_id(), 'cat_new_attachment_family'));
            ?>

            <div class="col-lg-3 col-md-4 col-xs-6 work-tool-link hard">
                <a href="<?php the_permalink(); ?>">
                    <div class="work-tool-link__button-overlay">
                        <div class="work-tool-link__button button button--primary">View Details</div>
                    </div>

                    <?php cnf_preview(get_the_id(), 'cat_used_feed_archive'); ?>
                    <span class="work-tool-link__name text--small"><?php echo $family->name .': '. get_the_title(); ?></span>
                </a>
            </div>

        <?php endwhile; wp_reset_postdata(); ?>
    </div>
</section>
<?php endif; ?>