<?php if( get_field('image_position') == 'Left' ) { ?>
<!-- left-image-right-text -->
<section class="half-and-half clearfix">
    <div class="half-image left">
        <?php echo fx_get_image_tag( get_field( 'image' ), 'img-responsive' ); ?>
    </div>
    <div class="half-text right">
        <div class="half-text-wrapper">
            <?php the_field('content'); ?>
        </div>
    </div>
</section>
<!-- left-image-right-text -->
<?php } ?>
<?php if( get_field('image_position') == 'Right' ) { ?>
<!-- right-image-left-text -->
<section class="half-and-half clearfix right-image">
    <div class="half-image right-img">
        <?php echo fx_get_image_tag( get_field( 'image' ), 'img-responsive' ); ?>
    </div>
    <div class="half-text left">
        <div class="half-text-wrapper">
           <?php the_field('content'); ?>
        </div>
    </div>
</section>
<!-- right-image-left-text -->
<?php } ?>