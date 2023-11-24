<?php
    $product = CatView::instance();
?>
<div id="other-media">
    <?php if( !empty($product->videos) AND !empty($product->vpts) ): ?>

    <div class="row row--dark-gray cf">
        <div class="col-sm-6 ne-video hard">
            <img src="<?php echo get_stylesheet_directory_uri();?>/assets/img/img-video.jpg" alt="Videos" class="responsive-image"/>
            <button class="circle" id="videos-button"><span class="icon-play"></span></button>
        </div>

        <div class="col-sm-6 ne-360 hard">
            <img src="<?php echo get_stylesheet_directory_uri();?>/assets/img/img-360.jpg" alt="360° Views" class="responsive-image"/>
            <button class="circle"id="vpts-button"><span class="icon-cycle"></span> 360&deg; View</button>
        </div>
    </div>

    <?php elseif( !empty($product->videos) AND empty($product->vpts) ): ?>

    <div class="row row--dark-gray cf soft--ends ne-single-video-360">
    	<h2 class="text--center title--big ne-single-video-360__title">Learn More About This Piece of Equipment</h2>
        <div class="col-sm-6 col-sm-offset-3 ne-video hard">
            <img src="<?php echo get_stylesheet_directory_uri();?>/assets/img/img-video.jpg" alt="Videos" class="responsive-image"/>
            <button class="circle" id="videos-button"><span class="icon-play"></span></button>
        </div>
    </div>

    <?php elseif( empty($product->videos) AND !empty($product->vpts) ): ?>

    <div class="row row--dark-gray cf soft--ends ne-single-video-360">
    	<h2 class="text--center title--big ne-single-video-360__title">Learn More About This Piece of Equipment</h2>
        <div class="col-sm-6 col-sm-offset-3 ne-360 hard">
            <img src="<?php echo get_stylesheet_directory_uri();?>/assets/img/img-360.jpg" alt="360° Views" class="responsive-image"/>
            <button class="circle"id="vpts-button"><span class="icon-cycle"></span> 360&deg; View</button>
        </div>
    </div>

    <?php endif; ?>


    <?php if( !empty($product->videos) ): ?>
        <div class="visuallyhidden" id="product__videos">
            <?php foreach($product->videos as $i => $video): ?>
                <a href="<?php echo $video->src; ?>"><?php echo $video->title; ?></a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if( !empty($product->vpts) ): ?>
        <div class="mfp-hide" id="product__vpts">

            <div class="vpt-wrapper">
                <div class="tabs tabs--justified two-up">
                    <nav class="tabs__nav">
                        <ul>
                            <li>
                                <a class="active" href="#vpt-exterior">Exterior View</a>
                            </li>

                            <li>
                                <a href="#vpt-interior">Interior View</a>
                            </li>
                        </ul>
                    </nav>
                    <div class="tabs__content">
                        <?php foreach($product->vpts as $i => $vpt): ?>
                            <div class="tabs__tab <?php echo ($i == 0) ? 'active' :'';?>"
                                 id="<?php echo $vpt->type; ?>">

                                <iframe src="<?php echo $vpt->src; ?>"
                                        frameborder="0"
                                        scrolling="no"
                                        width="100%"
                                        style="width: 620px; height: 356px;"
                                ></iframe>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>