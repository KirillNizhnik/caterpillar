<section class="home-quipment">
        <div class="home-quipment-texture-image hidden-sm-down">
            <?php echo fx_get_image_tag( 9918 ); ?>
        </div>
        <div class="home-quipment-texture-image hidden-sm-up">
            <?php echo fx_get_image_tag( 9927 ); ?>
        </div>
        <div class="home-quipment-banner">
            <div class="home-quipment-banner-image">
                <?php
                $image = get_field('equipment_blocks_banner_image');
                $size = 'full'; // (thumbnail, medium, large, full or custom size)
                if( $image ) {
                    echo fx_get_image_tag( $image, $size );
                }?>
            </div>
            <div class="home-quipment-banner-overlay">
                <div class="container">
                    <div class="home-quipment-header">
                        <div class="home-quipment-header-wrapper">
                            <h2><?php the_field('equipment_blocks_title'); ?></h2>
                            <?php the_field('equipment_blocks_text'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="home-quipment-texture">
            <div class="home-quipment-texture-overlay">
                <div class="container">
                    <div class="home-quipment-white-box">
                        <div class="home-quipment-list">

                            <?php if( have_rows('equipment_blocks_boxes') ): ?>
                                <?php while( have_rows('equipment_blocks_boxes') ): the_row();
                                    $image = get_sub_field('equipment_blocks_box_image');
                                    $btn = get_sub_field("equipment_blocks_box_link");
                                    ?>
                                    <div class="home-quipment-list-items">
                                        <?php if($btn): ?>
                                        <a href="<?php echo $btn['url']; ?>">
                                            <div class="home-quipment-image">
                                                <?php echo fx_get_image_tag( $image, 'full' ); ?>
                                            </div>
                                            <div class="home-quipment-content">
                                                <h4><?php the_sub_field('equipment_blocks_box_title'); ?></h4>
                                                <?php the_sub_field('equipment_blocks_box_text'); ?>
                                                <span class="btn btn-tertiary"><?php echo $btn['title']; ?></span>
                                            </div>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </div>
                        <?php get_template_part( 'partials/homepage-machines-filter' ); ?>
                    </div>
                    <div class="home-quipment-view-button hidden-sm">
                        <a href="<?php echo get_permalink( $post = 6 ); ?>" class="btn btn-primary">View all equipment</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
