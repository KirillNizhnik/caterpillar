 <section class="fullwidth-image-background">
    <div class="fullwidth-image">
        <?php echo fx_get_image_tag( get_field( 'background_image' ), 'img-responsive' ); ?>
    </div>
    <div class="fullwidth-overlay">
        <div class="container">
            <div class="fullwidth-overlay-content">
                <div class="fullwidth-overlay-content-wrapper">
                    <?php the_field('content'); ?>
                </div>
            </div>
        </div>
    </div>
</section>