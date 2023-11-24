<?php
    $product = CAT()->product();

    $are_images = ( !empty($product->images) and is_array($product->images) );
    if (count($product->images) == 1 and !is_object(reset($product->images)))
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

        <ul class="tabs__nav clearfix" id="tabs-nav-thumb">
        <?php if ($are_images): ?>
            <li class="tab-link active"><a href="#photos">Photos</a></li>
        <?php endif; ?>
        <?php if ($are_videos): ?>
        <li class="tab-link <?php echo $are_images ? '' : 'active' ?>"><a href="#videos">Videos</a></li>
        <?php endif; ?>
        <?php if ($are_spinsets): ?>
            <li class="tab-link <?php echo ($are_images or $are_videos) ? '' : 'active' ?>"><a href="#view">360 View</a></li>
        <?php endif; ?>
        </ul>

        <div class="tabs__content">
             <div id="photos" class="tab-content-thumb active">
                <?php if ( $are_images ): ?>
            	<!-- Photos Tab -->
                <div class="tab-item tab-item--padded active" id="photos">
                <?php //var_dump($product->images); ?>
                    <div class="frame js-thumbnails-scroller">
        	            <ul class="product__thumbnails images clearfix">

        	                <?php foreach($product->images as $key=>$image): ?>
                                <?php if (!is_object($image)) continue ?>
        	                	<li>
        	                	    <a class="product__thumbnail js-image-thumbnail open-popup-link"
        	                	    href="#product-<?php echo $key; ?>">
        	                	        <?php echo cat_sized_image( $image, 'full', array('style'=> 'width:100%; height: 100px; object-fit: cover; padding: 0 15px;  border: 1px solid #f5f5f5;') ); ?>
        	                	  </a>

                                  <div id="product-<?php echo $key; ?>" class="white-popup mfp-hide" >
                                    <?php echo cat_sized_image( $image, 'full', array('style'=>'max-width:100%;max-height:600px;padding: 0 25px') );  ?>
                                  </div>
        	                	</li>

        	                <?php endforeach; ?>

        	            </ul>
                    </div>
                   <!-- <div class="scrollbar js-scrollbar">
                        <div class="handle"></div>
                    </div> -->
                </div>
                <?php endif; ?>
            </div>
            <div id="videos" class="tab-content-thumb">
                <?php if ( $are_videos ): ?>
                <!-- Videos Tab -->
                <div class="tab-item tab-item--padded <?php echo $are_images ? '' : 'active' ?>" id="videos">

                    <div class="frame js-thumbnails-scroller">
        	            <ul class="product__thumbnails clearfix">

                        <?php if ( count($product->videos) > 0 ): ?>
        	                <?php foreach($product->videos as $video): ?>

        	                	<?php if($video!=""):?><li><a class="product__video js-video-thumbnail" href="<?php echo $video->src; ?>"><?php echo youtube_video_thumbnail( $video ); ?></a></li><?php endif; ?>

        	                <?php endforeach; ?>
                        <?php endif ?>
<!--                        --><?php //if ( count($product->custom_videos) > 0 ): ?>
<!--        	                --><?php //foreach($product->custom_videos as $video): ?>
<!---->
<!--        	                --><?php //if($video!=''):?><!--	<li><a class="product__video js-video-thumbnail" href="--><?php //echo $video; ?><!--">--><?php //echo youtube_video_thumbnail( $video ); ?><!--</a></li>--><?php //endif; ?>
<!---->
<!--        	                --><?php //endforeach; ?>
<!--                        --><?php //endif; ?>

        	            </ul>
                    </div>
                    <div class="scrollbar js-scrollbar">
                        <div class="handle"></div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
             <div id="view" class="tab-content-thumb">
                <?php if( $are_spinsets ): ?>
                <!-- 360 View Tab -->
                 <div class="tab-item <?php echo ($are_images or $are_videos) ? '' : 'active' ?>" id="view">

        	            <div class="product__vpts" id="product__vpts">
                            <ul class="product__thumbnails clearfix">
                                <?php foreach($product->spinsets as $i => $vpt): ?>
                                    <li>
                                        <a class="product__vpt js-vpt-thumbnail" href="<?php echo $vpt->src; ?>">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/<?php echo $vpt->type; ?>.jpg" />
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <?php foreach($product->spinsets as $i => $vpt): ?>
                            <div class="hidden vpt" id="<?php echo $vpt->type; ?>">
                                <iframe class="clearfix" src="<?php echo $vpt->src; ?>" frameborder="0" scrolling="no" class="responsive-video" style="height:90px; width:100px;"></iframe>
                            </div>
                            <?php endforeach; ?>

                        </div><!-- product__vpts end -->





        	            <!-- <ul class="product__thumbnails clearfix">

                            <li>
                                <a class="product__vpt" href="#product__vpts">
                                    <img class="clearfix" src="<?php echo get_template_directory_uri(); ?>/assets/img/<?php echo $vpt->type; ?>.jpg" alt=""/>
                                </a>
                            </li>

        	            </ul> -->

                </div>
                <?php endif; ?>
            </div>
        </div>


    </div>
    <?php endif; // are media of some sort ?>
</div>

