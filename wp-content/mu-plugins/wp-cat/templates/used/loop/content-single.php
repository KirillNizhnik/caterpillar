<?php $machine = CAT()->product(get_the_id()); // var_dump($machine); ?>

<div class="col-xs-6 col-sm-4 product-card-details-block used-product-family">
    <?php $permalink = str_replace( '%cat_used_machine_family%', $machine->family->slug, get_permalink() ); ?>
    <a href="<?php echo $permalink; ?>">
        <div class="product-card product-card-detail-info">
            <h3 class="product-card__title text--center flush"><?php the_title(); ?></h3>
            <figure class="product-card__thumb text--center">
                <!-- <img src="/content/themes/ho-penn/assets/img/demo-product-card-thumb.jpg" alt="" class="img-responsive"/> -->
                <?php echo cat_sized_image( reset($machine->images), array(312,233) ); ?>
            </figure>
        </div>
        <button class="button button--secondary button--block text--left">View Full Specs</button>
    </a>
</div>
