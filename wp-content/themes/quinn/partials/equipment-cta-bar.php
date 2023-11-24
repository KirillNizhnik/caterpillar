<section class="inner-cta">
    <div class="inner-cta-background">
        <?php echo fx_get_image_tag( get_field( 'background_image', 'option' ), 'img-responsive' ); ?>
    </div>
    <div class="inner-cta-overlay">
        <div class="container">
            <div class="innercta-content">
                <h4><?php the_field('heading', 'option'); ?></h4>
                <p><?php the_field('text_content', 'option'); ?></p>
                <?php $link  = get_field('secondary_button', 'option'); ?>
<?php if($link): ?><a class="btn btn-secondary" href="<?php echo $link['url']; ?>"><?php echo $link['title']; ?></a><?php endif; ?>
                <?php $link  = get_field('tertiary_button', 'option'); ?>
<?php if($link): ?><a class="btn btn-tertiary" href="<?php echo $link['url']; ?>"><?php echo $link['title']; ?></a><?php endif; ?>
            </div>
        </div>
    </div>
</section>
