<?php
$industry = CAT()->industry(get_the_id());
?>

    <article class="col-xs-6 col-sm-4 product-item-block">
        <a href="<?php the_permalink(); ?>" class="product-item-card">
            <div class="product-item-card__thumb">
            <?php
                $thumbnail = $industry->thumbnail();
                echo cat_sized_image( $thumbnail, array(200,200), array('itemprop' => 'image') );
            ?>
            </div>
            <div class="product-item-card__info">
                <div class="product-item-card__title">
                    <h3><?php echo $industry->name; ?></h3>
                </div>
            </div>
        </a>
    </article>