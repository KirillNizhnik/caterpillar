<section class="accordion">
    <div class="container">
        <div class="accordion-wrapper">
            <?php the_field('accordion_section_top_content'); ?>
            <div class="accordion-box">

                <?php if( have_rows('wysiwyg_accordion_section_repeater') ): ?>
                    <?php while( have_rows('wysiwyg_accordion_section_repeater') ): the_row();
                        ?>
                        <div class="accordion-item">
                            <h5 class="accordion-title"><?php the_sub_field('wysiwyg_accordion_section_title'); ?></h5>
                            <div class="accordion-content">
                                <?php the_sub_field('wysiwyg_accordion_section_content_text'); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>