<section class="masthead">
        <div class="masthead-slider masthead-homepage-slider">

            <?php if( have_rows('masthead_slides') ): ?>
                <?php while( have_rows('masthead_slides') ): the_row(); 
                    $image = get_sub_field('masthead_slide_image');
                    ?>
                    <div class="masthead-slider-item">
                        <div class="masthead-image">
                            <?php echo fx_get_image_tag( $image, 'full' ); ?>
                        </div>
                        <div class="masthead-overlay">
                            <div class="container">
                                <div class="masthead-content">
                                    <h1><span><?php the_sub_field('masthead_slide_title_above') ?></span> <br> <?php the_sub_field('masthead_slide_title_below') ?></h1>
                                    <?php $btn  = get_sub_field("masthead_slide_link"); ?>
                                    <?php if($btn): ?><a class="btn btn-secondary" href="<?php echo $btn['url']; ?>"><?php echo $btn['title']; ?></a><?php endif; ?>
                                            </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </section>