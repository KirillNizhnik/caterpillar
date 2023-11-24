 <?php
    //$machine = CAT()->product();
?>
<!--
<div class='col-sm-12'>
<?php //echo cat_sized_image( reset($machine->images), array(600,400), array('class' => 'img-responsive') ); ?>
</div>

<?php //if( ! empty($machine->images) && is_object($machine->images[0]) ): ?>
<div class="product-detail__media-tabs tabs">
    <ul class="tabs__nav clearfix">
        <li class="active tab-photo"><a href="#photos">Photos</a></li>
    </ul>

    <div class="tabs__content">

        <div class="tabs__tab tab-item--padded active" id="photos">

            <div class="frame">
	            <ul class="product__thumbnails images js-thumbnails-scroller">

	                <?php //foreach($machine->images as $image): ?>

	                	<li><a class="product__thumbnail" href="<?php //echo $image->src; ?>"><?php //echo cat_sized_image( $image, full, array('class' => 'img-responsive')  ); ?></a></li>

	                <?php //endforeach; ?>

	            </ul>
            </div>
            <div class="scrollbar js-scrollbar">
                <div class="handle"></div>
            </div>

        </div>

    </div>
</div>
<?php //endif; ?>
 -->

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
        
        <?php if ( have_rows( 'youtube_videos' ) || have_rows('media_videos') ) : ?>
            <li class="tab-link <?php echo $are_images ? '' : 'active' ?>"><a href="#videos">Videos</a></li>       
                
        <?php endif;  ?>
         
        <?php if ($are_spinsets): ?>
            <li class="tab-link <?php echo ($are_images or $are_videos) ? '' : 'active' ?>"><a href="#view">360 View</a></li>
        <?php endif; ?>
        </ul>

        <div class="tabs__content">
             <div id="photos" class="tab-content-thumb active">
                <?php if ( $are_images ): ?>
                <!-- Photos Tab -->
                <div class="tab-item tab-item--padded active" id="photos">

                    <div class="frame js-thumbnails-scroller" >
                        <ul class="product__thumbnails images clearfix">

                            <?php foreach($product->images as $key => $image): ?>

                                <?php if (!is_object($image)) continue ?>

                                <li>
                                <a class="product__thumbnail js-image-thumbnail open-popup-link"
        	                	    href="#product-<?php echo $key; ?>">
        	                	        <?php echo cat_sized_image( $image, 'full', array('style'=>'width:100%; height: 100px; object-fit: cover; padding: 0 15px; border: 1px solid #f5f5f5;') ); ?>
        	                	  </a>

                                  <div id="product-<?php echo $key; ?>" class="white-popup mfp-hide" >
                                    <?php echo cat_sized_image( $image, 'full', array('style'=>'max-width:100%;max-height:600px;padding: 0 25px') );  ?>
                                  </div>
                            
                                </li>

                            <?php endforeach; ?>

                        </ul>
                    </div>
                    

                </div>
                <?php endif; ?>
            </div>
             <!-- Videos Tab -->
            <div id="videos" class="tab-content-thumb">
                <!-- youtube videos-->
                <?php if ( have_rows( 'youtube_videos' ) || have_rows('media_videos') ) :
                $rows = get_field( 'youtube_videos' );
                        $video_id = preg_replace( '/[^?]*\?v\=([^&]+).*/', '$1', $rows[ 0 ][ 'youtube_url' ] );
                
                ?>
               
                <div class="tab-item tab-item--padded <?php echo $are_images ? '' : 'active' ?>" id="videos">

                    <div class="frame js-thumbnails-scroller">
                        <ul class="product__thumbnails clearfix">
                                
                              
                                
                        <?php
                                        while ( have_rows( 'youtube_videos' ) ) : the_row();
                                            $video_id = preg_replace( '/[^?]*\?v\=([^&]+).*/', '$1', get_sub_field( 'youtube_url' ) );
                                    ?>
                                        <li>
                                            <a class="product__video js-video-thumbnail"
                                                href="<?php echo get_sub_field( 'youtube_url' ); ?>">
                                                <img src="https://img.youtube.com/vi/<?php echo $video_id; ?>/maxresdefault.jpg" class="img-responsive" />
                                            </a>
                                        </li>
                                    <?php endwhile;?>
                                    
                             <?php
                                    while ( have_rows( 'media_videos' ) ) : the_row();
                                           
                                    ?>
                                        <li>
                                            <a class="product__video js-video-thumbnail"
                                                href="<?php echo get_sub_field( 'video_url' ); ?>">
                                                <img src="<?php echo get_sub_field( 'video_url' ); ?>" class="img-responsive" />
                                            </a>
                                        </li>
                                    <?php endwhile;?>
                                    

                        </ul>
                    </div>
                    <div class="scrollbar js-scrollbar">
                        <div class="handle"></div>
                    </div>

                </div>
                <?php endif; ?>
                <!-- regular direct url videos-->
                
            </div>
            <div id="view" class="tab-content-thumb">
                <?php if( $are_spinsets ): ?>
                <!-- 360 View Tab -->
                <div class="tab-item <?php echo ($are_images or $are_videos) ? '' : 'active' ?>" id="view">


                        <ul class="product__thumbnails clearfix">

                            <li><a class="product__vpt" href="#product__vpts"><img class="clearfix" src="<?php echo cat_get_vpts_thumb() ?>" alt=""/></a></li>

                        </ul>

                </div>
                <?php endif; ?>
            </div>
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
