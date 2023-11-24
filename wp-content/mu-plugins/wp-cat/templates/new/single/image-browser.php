<?php
    $product = CAT()->product();

    $are_images = ( !empty($product->images) and is_array($product->images) );
    if (count($product->images) == 1 and !is_object($product->images[0]))
        $are_images = false;

    $are_videos = ( ( !empty($product->videos) and is_array($product->videos) ) or ( !empty($product->custom_videos) and is_array($product->custom_videos) ));
    $are_spinsets = ( !empty($product->spinsets) and is_array($product->spinsets) );
?>
<div class="product__media media-browser">
    <div class="media-browser__preview">
        <?php echo cat_sized_image( reset($product->images), array(376,252) ); ?>
    </div>

    <?php if ($are_images or $are_videos or $are_spinsets): ?>
    <div class="media-browser__tabs tabs">

        <ul class="tabs__nav clearfix">
        <?php if ($are_images): ?>
            <li class="tab-link active"><a href="#photos">Photos</a></li>
        <?php endif ?>
        <?php if ($are_videos): ?>
        <li class="tab-link <?php echo $are_images ? '' : 'active' ?>"><a href="#videos">Videos</a></li>
        <?php endif ?>
        <?php if ($are_spinsets): ?>
            <li class="tab-link <?php echo ($are_images or $are_videos) ? '' : 'active' ?>"><a href="#view">360 View</a></li>
        <?php endif ?>
        </ul>

        <div class="tabs__content">

            <?php if ( $are_images ): ?>
        	<!-- Photos Tab -->
            <div class="tab-item tab-item--padded active" id="photos">

                <div class="frame js-thumbnails-scroller">
    	            <ul class="product__thumbnails clearfix">

    	                <?php foreach($product->images as $image): ?>

                            <?php if (!is_object($image)) continue ?>

    	                	<li><a class="product__thumbnail" href="<?php echo $image->src; ?>"><?php echo cat_sized_image( $image, array(108,72), array('style'=>'max-width:100%;max-height:100%;') ); ?></a></li>

    	                <?php endforeach; ?>

    	            </ul>
                </div>
                <div class="scrollbar js-scrollbar">
                    <div class="handle"></div>
                </div>

            </div>
            <?php endif; ?>

            <?php if ( $are_videos ): ?>
            <!-- Videos Tab -->
            <div class="tab-item tab-item--padded <?php echo $are_images ? '' : 'active' ?>" id="videos">

                <div class="frame js-thumbnails-scroller">
    	            <ul class="product__thumbnails clearfix">

                    <?php if ( count($product->videos) > 0 ): ?>
    	                <?php foreach($product->videos as $video): ?>

    	                	<li><a class="product__video" href="<?php echo $video->src; ?>"><?php echo youtube_video_thumbnail( $video ); ?></a></li>

    	                <?php endforeach; ?>
                    <?php endif ?>
                    <?php if ( count($product->custom_videos) > 0 ): ?>
    	                <?php foreach($product->custom_videos as $video): ?>

    	                	<li><a class="product__video" href="<?php echo $video; ?>"><?php echo youtube_video_thumbnail( $video ); ?></a></li>

    	                <?php endforeach; ?>
                    <?php endif ?>

    	            </ul>
                </div>
                <div class="scrollbar js-scrollbar">
                    <div class="handle"></div>
                </div>

            </div>
            <?php endif; ?>

            <?php if( $are_spinsets ): ?>
            <!-- 360 View Tab -->
            <div class="tab-item <?php echo ($are_images or $are_videos) ? '' : 'active' ?>" id="view">


    	            <ul class="product__thumbnails clearfix">

    	                <li><a class="product__vpt" href="#product__vpts"><img class="clearfix" src="<?php echo get_stylesheet_directory_uri(); ?>/images/vpts-thumb.gif" alt=""/></a></li>

    	            </ul>

            </div>
            <?php endif; ?>

        </div>

        <!-- 360 View Content -->
        <?php if( !empty($product->spinsets) ): ?>

    	    <div class="mfp-hide product__vpts" id="product__vpts">

    	        <div class="tabs tabs--justified two-up">

    	            <nav class="tabs__nav">
    	                <ul>
    	                    <li class="tab-link active"><a href="#spinset-exterior">Exterior View</a></li>
    	                    <li class="tab-link"><a href="#spinset-interior">Interior View</a></li>
    	                </ul>
    	            </nav>

    	            <div class="tabs__content">

    	                <?php foreach($product->spinsets as $i => $vpt): ?>

    	                    <div class="tab-item <?php echo ($i == 0) ? 'active' :'';?>" id="<?php echo $vpt->type; ?>">

    	                        <iframe class="clearfix" src="<?php echo $vpt->src; ?>" frameborder="0" scrolling="no" class="responsive-video" style="height:400px; width:500px;"></iframe>

    	                    </div>

    	                <?php endforeach; ?>

    	            </div>

    	        </div>

    	    </div>

        <?php endif; ?>

    </div>
    <?php endif; // are media of some sort ?>
</div>
