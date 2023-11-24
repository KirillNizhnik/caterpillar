<?php
   $product = CAT()->product();
?>
<?php if( ! empty($product->specs) ): ?>
<div class="product__specs tab-item active" id="specs">

	<div class="specs specs--list">
	    <?php foreach($product->specs as $spec_group_title => $spec_group): ?>

		        <div class="">
		            <div class="">
		                <div class="">
		                    <h3 class="">
					            <?php echo (!empty($spec_group_title)) ? $spec_group_title : 'Dimensions'; ?>
					            <span class="icon icon-positive pull--right"></span>
					        </h3>
		                    <div class="">
		                        <dl class="clearfix">
						        <?php foreach($spec_group as $spec): ?>
						        	<div class="specs-infos">
							            <dt><?php echo $spec->name; ?></dt>
							            <dd data-english="<?php echo $spec->value_english; ?><?php echo $spec->unit_english; ?>"
							                data-metric="<?php echo $spec->value_metric; ?><?php echo $spec->unit_metric; ?>">
							                <?php echo $spec->value_english; ?><?php echo $spec->unit_english; ?>
							            </dd>
						            </div>

						        <?php endforeach; ?>
						    </dl>
		                    </div>
		                </div>
		            </div>
		        </div>
		    

	    <?php endforeach; ?>
	</div>

</div>
<?php endif; ?>
