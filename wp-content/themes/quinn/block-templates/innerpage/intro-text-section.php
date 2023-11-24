<?php if(get_field('intro_text_section_title') || get_field('intro_text_section_text_content')):?>

<section class="intro-image-left-content container">
        <div class="intro-image <?php the_field('intro_text_section_image_position'); ?>">
            <div class="intro-image-box">
                <?php 
                $image = get_field('intro_text_section_image');
                $size = 'full'; // (thumbnail, medium, large, full or custom size)
                if( $image ) {
                    echo fx_get_image_tag( $image, $size );
                }?>
            </div>
            <div class="intro-image-triangle">
                <?php echo fx_get_image_tag( 432 ); ?>
            </div>
        </div>
        <div class="intro-text-wrap">
            <div class="intro-content-container">
                <div class="intro-content">
                    <h2><?php the_field('intro_text_section_title'); ?></h2>
                    <?php the_field('intro_text_section_text_content'); ?>
                    <?php $btn  = get_field("intro_text_section_link"); ?>
                    <?php if($btn): ?><a class="btn btn-primary" href="<?php echo $btn['url']; ?>"><?php echo $btn['title']; ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

<?php endif; ?>