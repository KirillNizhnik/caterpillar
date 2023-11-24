<?php
    $machine = CAT()->product();
?>

<?php echo cat_sized_image( reset($machine->images), array(600,400) ); ?>

<?php if( ! empty($machine->images) && is_object($machine->images[0]) ): ?>
<div class="product-detail__media-tabs tabs">
    <ul class="tabs__nav clearfix">
        <li class="active"><a href="#photos">Photos</a></li>
    </ul>

    <div class="tabs__content">

        <div class="tabs__tab tab-item--padded active" id="photos">

            <div class="frame js-thumbnails-scroller">
	            <ul class="product__thumbnails">

	                <?php foreach($machine->images as $image): ?>

	                	<li><a class="product__thumbnail" href="<?php echo $image->src; ?>"><?php echo cat_sized_image( $image, array(128,78) ); ?></a></li>

	                <?php endforeach; ?>

	            </ul>
            </div>
            <div class="scrollbar js-scrollbar">
                <div class="handle"></div>
            </div>

        </div>

    </div>
</div>
<?php endif; ?>