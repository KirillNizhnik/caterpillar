<section class="home-about">
        <div class="home-about-texture">
            <img src="../wp-content/themes/quinn/assets/img/about-texture-mobile.jpg" class="img-responsive hidden-sm-up" alt="">
            <img src="../wp-content/themes/quinn/assets/img/about-texture-tab.jpg" class="img-responsive hidden-xs-down hidden-md-up" alt="">
            <img src="../wp-content/themes/quinn/assets/img/about-texture.jpg" class="img-responsive hidden-sm-down" alt="">
        </div>
        <div class="home-about-overlay">
            <div class="container">
                <div class="home-about-quipment">
                    <div class="home-about-quipment-wrapper">


                        <?php if( have_rows('icon_blocks') ): ?>
                            <?php while( have_rows('icon_blocks') ): the_row();
                                $btn  = get_sub_field("icon_link"); 
                                ?>
                                <div class="home-about-quipment-list">
                                    <?php if($btn): ?>
                                    <a href="<?php echo $btn['url']; ?>">
                                        <div class="home-about-quipment-title clearfix">
                                            <div class="home-about-quipment-icon">
                                                <span class="icon-sec"></span>
                                            </div>
                                            <div class="title-quipment">
                                                <h4><?php the_sub_field('icon_block_title_above'); ?> <br> <span><?php the_sub_field('icon_block_title_below'); ?></span></h4>
                                            </div>
                                        </div>
                                        <span class="nopad btn btn-tertiary">View now</span>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </div>
                    <div class="home-about-contact-area">
                        <div class="home-about-contact-area-wrapper">
                            <?php the_field('icon_block_banner_text'); ?>
                            <?php $btn  = get_field("icon_block_banner_link"); ?>
                            <?php if($btn): ?><a class="btn btn-secondary" href="<?php echo $btn['url']; ?>"><?php echo $btn['title']; ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>