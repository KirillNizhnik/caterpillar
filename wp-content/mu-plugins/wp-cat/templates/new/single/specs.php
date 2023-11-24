<?php
   $product = CAT()->product();
?>
<?php if( ! empty($product->specs) ): ?>
<div class="product__specs tab-item active" id="specs">

	<div class="specs specs--list">
	    <?php foreach($product->specs as $spec_group_title => $spec_group): ?>

        <h3>
            <?php echo (!empty($spec_group_title)) ? $spec_group_title : 'Dimensions'; ?>
            <span class="icon icon-positive pull--right"></span>
        </h3>

	    <dl class="clearfix">
	        <?php foreach($spec_group as $spec): ?>

	            <dt><?php echo $spec->name; ?></dt>
	            <dd data-english="<?php echo $spec->value_english; ?><?php echo $spec->unit_english; ?>"
	                data-metric="<?php echo $spec->value_metric; ?><?php echo $spec->unit_metric; ?>">
	                <?php echo $spec->value_english; ?><?php echo $spec->unit_english; ?>
	            </dd>

	        <?php endforeach; ?>
	    </dl>

	    <?php endforeach; ?>
	</div>

</div>
<?php endif; ?>