<?php

function fx_industry_families_shortcode($atts)
{
    $atts = shortcode_atts(
        array(
            'family' => 'all',
        ),
        $atts
    );

    $industry = CAT()->industry();
    $industry_families = $industry->products();

    $family_name = 'cat_new_' . $atts['family'] . '_family';

    ob_start(); ?>

    <?php if (!empty($industry_families)): ?>

    <div class="product-item-card__list">
        <div class="row">
            <?php
            foreach ($industry_families as $family) {
                if ($atts['family'] !== 'all' && $family->taxonomy !== $family_name) {
                    continue;
                }

                if ($family->post_type === 'page') {
                    cat_template('new/loop/content-page', array('family' => $family));
                } else {
                    cat_template('new/loop/content-family', array('family' => $family));
                }
            }
            ?>
        </div>
    </div>

<?php endif; ?>

    <?php
    return ob_get_clean();
}

add_shortcode('industry-families', 'fx_industry_families_shortcode');
