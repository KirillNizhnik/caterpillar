<section class="right-image-left-content">
        <div class="right-image hidden-sm-up">
            <div class="right-image-box">
                <?php 
                $image = get_field('text_and_image_image');
                $size = 'full'; // (thumbnail, medium, large, full or custom size)
                if( $image ) {
                    echo fx_get_image_tag( $image, $size );
                }?>
            </div>
            <div class="right-image-triangle">
                <?php echo fx_get_image_tag( 432 ); ?>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-sm-7">
                    <div class="left-content">
                        <h2><span><?php the_field('text_and_image_title_above'); ?></span> <br> <?php the_field('text_and_image_title_below'); ?></h2>
                        <?php the_field('text_and_image_text_content'); ?>
                        <?php $btn  = get_field("text_and_image_link"); ?>
                        <?php if($btn): ?><a class="btn btn-primary" href="<?php echo $btn['url']; ?>"><?php echo $btn['title']; ?></a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="right-image hidden-xs-down">
                        <div class="right-image-box">
                            <?php 
                            $image = get_field('text_and_image_image');
                            $size = 'full'; // (thumbnail, medium, large, full or custom size)
                            if( $image ) {
                                echo fx_get_image_tag( $image, $size );
                            }?>
                        </div>
                        <div class="right-image-triangle">
                            <?php echo fx_get_image_tag( 432 ); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>