<section class="image-button">
    <div class="container">
        <div class="image-button-header">
            <h2><?php the_field('image_button_section_heading'); ?></h2>
            <?php the_field('image_button_section_text_content'); ?>
        </div>
        <div class="image-button-wrapper">
            <div class="row">
                <?php if( have_rows('image_button') ): ?>
                    <?php while( have_rows('image_button') ): the_row(); ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="image-button-box">
                                <a href="<?php the_sub_field('link'); ?>">
                                    <div class="image-button-image">
                                        <?php echo fx_get_image_tag( get_sub_field( 'image' ), 'img-responsive' ); ?>
                                    </div>
                                    <div class="image-button-content">
                                        <h4><?php the_sub_field('image_button_label'); ?></h4>
                                        <span class="btn btn-tertiary"><?php echo get_sub_field('learn_more'); ?></span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>


