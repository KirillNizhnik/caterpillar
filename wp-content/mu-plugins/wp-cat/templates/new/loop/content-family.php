<?php
$family_model = CAT()->family($family); 
?>

    <article class="col-xs-6 col-sm-4 product-item-block">
        <a href="<?php echo get_term_link($family); ?>" class="product-item-card">
            <div class="product-item-card__thumb">
                <?php
                    $thumbnail = $family_model->thumbnail();
                    echo cat_sized_image( $thumbnail, array(550,550), array('itemprop' => 'image') );
                ?>
            </div>

            <div class="product-item-card__info">
                <div class="product-item-card__title">
                    <h3><?php echo $family->name; ?></h3>
                </div>
            </div>
        </a>
    </article>

