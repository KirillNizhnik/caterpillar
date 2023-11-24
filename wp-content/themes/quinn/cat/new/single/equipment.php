<?php
    $product = CAT()->product();
?>

<div class="product__equipment tab-item" id="standard">

	<?php if( isset($product->standard_equipment) ): ?>
	    <h3>Standard Equipment:</h3>
        <ul>
        <?php foreach($product->standard_equipment as $standard): ?>
            <li>
                <?php if( !is_array($standard) ):
                	echo $standard;
                else: ?>

                <dl>
                    <dt><?php echo ucwords(strtolower($standard['description'])); ?></dt>
                    <dd>
                        <ul>
                            <?php foreach($standard['children'] as $option): ?>
                                <li><?php echo $option; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </dd>
                </dl>

                <?php endif; ?>
            </li>
        <?php endforeach; ?>
        </ul>
  
    <?php endif; ?>

    <?php if(isset($product->optional_equipment)): ?>
    	<h3>Optional Equipment:</h3>
        <ul>
        <?php foreach($product->optional_equipment as $option): ?>
            <li>
                <?php if( !is_array($option) ):
                	echo $option;
                else: ?>

                <dl>
                    <dt><?php echo ucwords(strtolower($option['description'])); ?></dt>
                    <dd>
                        <ul>
                            <?php foreach($option['children'] as $child): ?>
                                <li><?php echo $child; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </dd>
                </dl>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
        </ul>
  
    <?php endif; ?>

</div>