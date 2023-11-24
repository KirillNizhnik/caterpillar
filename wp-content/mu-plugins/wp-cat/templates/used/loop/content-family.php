<?php
    $family_model = CAT()->family($family);
?>

<article class="col-xs-6 col-sm-4 product-item-block">
    <a href="<?php echo get_term_link($family); ?>" class="product-item-card">
        <div class="product-item-card__thumb">
            <?php
                $thumbnail = $family_model->thumbnail($family->term_id, array(227,220));
            ?>
            <img src="<?php echo $thumbnail->src; ?>" itemprop="image" alt="" />
        </div>
        <div class="product-item-card__info">
            <div class="product-item-card__title">
                <h3><?php echo $family->name; ?> <span class="machine-count">(<?php echo $family->count; ?>)</span></h3>
            </div>
        </div>
    </a>
</article>