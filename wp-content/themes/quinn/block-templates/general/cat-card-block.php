<section class="cat-card-section">
    <div class="container">
        <?php if(get_field('equipment_cards_wysiwyg_content')){
            the_field('equipment_cards_wysiwyg_content'); 
            }?>
        <div class="row">
        <?php
        // Check rows exists.
            if( have_rows('block_equipment_card') ):
               while( have_rows('block_equipment_card') ) : the_row();
                ?>
                <div class="col-sm-6 col-md-4">
                    <div class="col-border">
                        <a class="product-item-column" href="<?php echo get_sub_field('individual_card_link'); ?>" <?php if(get_field('new_tab')){ echo 'target="_blank"'; } ?>>
                            <div class="product-card">
                                <div class="product-card-detail-info">
                                    <div class="product-item-column-thumb">
                                        <img src="<?php echo get_sub_field('individual_card_image'); ?>" itemprop="image" alt="" />
                                    </div>
                                    <h4 class="product-card-family-name"><?php echo get_sub_field('individual_card_title'); ?></h4>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <?php
                endwhile;
        endif;?>
        </div>
    </div>
</section>

