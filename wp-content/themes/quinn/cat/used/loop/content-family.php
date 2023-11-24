<?php
    $family_model = CAT()->family($family);
?>

<article class="product-item-block">
    <div class="product-card-details-block">
        <div class="product-card">
            <div class="product-card-detail-info">
                <?php
                $link = get_term_link($family);
                if(get_field('alternate_url', $family)){
                        $link = get_field('alternate_url', $family);
                    }
                ?>
                <a href="<?php echo $link; ?>" class="product-item-card">
                    <div class="product-item-card__thumb">
                        <?php
                            $thumbnail = $family_model->thumbnail($family->term_id, array(227,220));
                        ?>
                        <img src="<?php echo $thumbnail->src; ?>" itemprop="image" alt="" />
                        <span class="btn btn-tertiary family-btn">View Machines</span>
                    </div>
                   <!-- <h4 class="family-name"><?php echo $family->name; ?><span class="machine-count">(<?php echo $family->count; ?>)</span></h4>-->
                   <h4 class="family-name"><?php echo $family->name; ?><span class="machine-count">(<?php echo $family->count; ?>)</span></h4>
                </a>
            </div>
        </div>
    </div>
</article>
