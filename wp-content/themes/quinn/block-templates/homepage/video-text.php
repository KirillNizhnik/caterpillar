<?php if ( is_front_page() ): ?>         
          <div class="home-about-since">
                <div class="home-about-since-video">
                    <?php 
                    $image = get_field('thumbnail_image');
                    $size = 'full'; // (thumbnail, medium, large, full or custom size)
                    if( $image ) {
                        echo fx_get_image_tag( $image, $size );
                    }?>
                    <div class="home-about-since-video-overlay">
                        <a class="home-about-since-video-button external" data-fancybox="" href="<?php the_field('video_text_video'); ?>" target="_blank" rel="noopener">
                            <span class="icon-play"></span>
                        </a>
                    </div>
                </div>
                <div class="home-about-since-content">
                    <h2><span><?php the_field('video_text_title_first'); ?></span> <?php the_field('video_text_title_second'); ?></h2>
                    <?php the_field('video_text_text_below'); ?>
                </div>
            </div>
        </div>
    </div>
</section>       

<?php else : ?>
<section class="home-about-video innerpage-about-vid">
    <div class="home-about-texture-video">
        <div class="mobile-texture">
            <!-- <img src="../wp-content/themes/quinn/assets/img/about-texture-mobile.jpg" class="img-responsive " alt=""> -->
            <?php echo fx_get_image_tag( 303 ); ?>
        </div>
        <div class="tablet-texture">
            <?php echo fx_get_image_tag( 305 ); ?>
        </div>
        <div class="desktop-texture">
            <?php echo fx_get_image_tag( 9918 ); ?>
        </div>
        <!-- <img src="../wp-content/themes/quinn/assets/img/about-texture-tab.jpg" class="img-responsive hidden-xs-down hidden-md-up" alt="">
        <img src="../wp-content/themes/quinn/assets/img/about-texture.jpg" class="img-responsive hidden-sm-down" alt=""> -->
    </div>
    <div class="home-about-overlay">
        <div class="container">			
            <div class="home-about-since">
                <div class="home-about-since-video">
                    <?php 
					$image = get_field('thumbnail_image');
					$size = 'full'; // (thumbnail, medium, large, full or custom size)
					if( $image ) {
					    echo fx_get_image_tag( $image, $size );
					}?>
                    <div class="home-about-since-video-overlay">
                        <a class="home-about-since-video-button external" data-fancybox="" href="<?php the_field('video_text_video'); ?>" target="_blank" rel="noopener">
                            <span class="icon-play"></span>
                        </a>
                    </div>
                </div>
                <div class="home-about-since-content">
                    <h2><span><?php the_field('video_text_title_first'); ?></span> <?php the_field('video_text_title_second'); ?></h2>
                    <?php the_field('video_text_text_below'); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>