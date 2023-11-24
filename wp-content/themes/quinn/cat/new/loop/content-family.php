<?php

    $family_model = CAT()->family($family);
   
    //var_dump($family_model);
?>
<article class="product-item-block">
    <div class="product-card-details-block">
        <div class="product-card">
            <div class="product-card-detail-info">
            <?php 
            $link = "";
            if(get_term_link($family)) {
                $link = get_term_link($family);
            }
            if(is_object($family_model)){
                if($family_model->slug == "ag-tractors"){
                    $link = get_permalink(10579);
                }
            }
                 
            if(get_field('alternate_url', $family)){
                $link = get_field('alternate_url', $family);
            }
            //var_dump($family_model->header);
            
            //var_dump($link);die();
            ?>
                <a href="<?php echo $link; ?>" class="product-item-card">
                    <div class="product-item-card__thumb">
                        <?php
                            
                            if ($family_model->header !== false) {
                                $thumbnail = $family_model->header->src;
                            } else {
                                if(is_int($family)){
                                    $thumbnail_raw = $family_model->thumbnail($family, array(227,220));
                                    
                                    $thumbnail = $thumbnail_raw->src;
                                    
                                } elseif(is_object($family) || is_array($family)) {
                                    $thumbnail_raw = $family_model->thumbnail($family->term_id, array(227,220));
                                    $thumbnail = $thumbnail_raw->src;
                                } else {
                                    $thumbnail = wp_get_attachment_url(7339);
                                }
                                
                                
                            }
                        ?>
                        <img src="<?php echo $thumbnail; ?>" itemprop="image" alt="" />
                        <span class="btn btn-tertiary family-btn">View Details</span>
                    </div>
                    <?php 
                    $name = $family_model->name;
                    if (!empty($family->name)) {
                        $name = $family->name;
                    }
                    ?>
                    <h4 class="family-name"><?php echo $name; ?></h4>
                </a>
            </div>
        </div>
    </div>
</article>