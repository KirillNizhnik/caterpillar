<?php 


?>
<article class="col-md-4 col-sm-6">
                <div class="image-button-box">
                    <a href="<?php echo get_sub_field('industry_link'); ?>">
                        <div class="image-button-image">
                        <?php
                            $thumbnail = get_sub_field('industry_thumbnail');
                            //echo cat_sized_image( $thumbnail, array(200,200), array('itemprop' => 'image') );
                            echo $thumbnail;
                            if($thumbnail){
                                echo cat_sized_image( $thumbnail, 'full', array('itemprop' => 'image') );
                            } else {
                                echo fx_get_image_tag(7339);
                            }
        
                        ?>
                        </div>
                        <div class="image-button-content">
                            <h4><?php echo get_sub_field('industry_name') ?></h4>
                            <span class="btn btn-tertiary"><?php echo get_sub_field('learn_more_text'); ?></span>
                        </div>
                    </a>
                </div>
            </article>

            