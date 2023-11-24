
<article class="product-item-block">
    <div class="product-card-details-block">
        <div class="product-card">
            <div class="product-card-detail-info">

                <a href="<?php echo get_the_permalink($family); ?>" class="product-item-card">
                    <div class="product-item-card__thumb">
                        <?php 
                            if (get_the_post_thumbnail_url($family)) {
                                $img_url = get_the_post_thumbnail_url($family);
                            } else {
                                $img_url = "/wp-content/uploads/2021/09/default.jpg";
                            }
                            
                        ?>
                        <img src="<?php echo $img_url; ?>" itemprop="image" alt="" />
                        <span class="btn btn-tertiary family-btn">View Details</span>
                    </div>
                    <h4 class="family-name"><?php echo get_the_title($family); ?></h4>
                </a>
            </div>
        </div>
    </div>
</article>