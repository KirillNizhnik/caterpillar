<?php
    $product = CAT()->product();
?>

<div class="product__features tab-item" id="features">

	<?php if( ! empty($product->features) ): ?>

		<?php foreach($product->features as $feature): ?>

            <h3><?php echo $feature->name; ?></h3>

            <?php echo wpautop($feature->content); ?>

            <?php if(isset($feature_image)):
				echo cat_sized_image($feature_image);
            endif; ?>

            <?php if(isset($feature->children) AND !empty($feature->children)):
            	foreach($feature->children as $child): ?>
                <h3><?php echo $child->name; ?></h3>
                <?php echo wpautop($child->content); ?>
            	<?php endforeach;
            endif; ?>

        <?php endforeach; ?>


	<?php endif; ?>
</div>
