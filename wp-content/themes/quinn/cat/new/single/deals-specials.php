<?php
    $product = CatView::instance();
    $standard = $product->standard_equipment;
    $optional = $product->optional_equipment;

    $col_size = (isset($standard) && !empty($standard) || isset($optional) && !empty($optional)) ? 6 : 12;
?>

<?php if(!is_empty_slideshow('deals-specials')): ?>
    <section class="col-sm-<?php echo $col_size; ?> ne-deals-and-specials" id="deals-and-specials">

        <h2 class="text--center title--bigger">Deals &amp; Specials</h2>
        <?php echo do_shortcode('[fx-slideshow slideshow_name="deals-specials" size="slideshow-medium" scale="fill"]'); ?>

    </section>
<?php endif; ?>
