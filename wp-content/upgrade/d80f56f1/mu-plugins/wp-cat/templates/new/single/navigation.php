<?php
    $product = CatView::instance();
    $related = get_cnf_related_equipment();
?>
<ul class="product-menu" id="product-menu">
    <li class="active"><button data-section="product__preview">Photos</button></li>

    <?php if( !empty($product->videos) AND !empty($product->vpts) ): ?>
    <li><button data-section="other-media">Other Media</button></li>
    <?php endif; ?>

    <?php if(!empty($product->features)): ?>
    <li><button data-section="benefits-and-features">Benefits &amp; Features</button></li>
    <?php endif; ?>

    <?php if( !empty($product->specs) ): ?>
    <li><button data-section="product-specs">Product Specs</button></li>
    <?php endif; ?>

    <?php if(isset($product->standard_equipment) OR isset($product->optional_equipment)): ?>
    <li><button data-section="equipment">Equipment</button></li>
    <?php endif; ?>
    
    <?php if(!is_empty_slideshow('deals-specials')): ?>
    <li><button data-section="deals-and-specials">Deals &amp; Specials</button></li>
    <?php endif; ?>

    <li class="important-content"><button data-section="quote">Get Quote</button></li>

    <?php if($related->have_posts()): ?>
    <li><button data-section="related-used">Related Used Equipment</button></li>
    <?php endif; ?>
    
</ul>