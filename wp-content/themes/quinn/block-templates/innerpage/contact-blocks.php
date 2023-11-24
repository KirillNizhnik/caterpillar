<?php
    $blocks = get_field('blocks') ?? '';
?>

<!-- Location Contact Blocks -->
<section class="contact-blocks section-padding">
    <div class="container">
        <div class="row">
            <div class="col-xxs-12">

                <div class="location-contact-items">
                    <?php if( $blocks ):
                        foreach( $blocks as $block ): ?>
                            <div class="lc-block__wrapper col-xxs-12 col-md-6">
                                <div class="lc-block">
                                   <?php if( $block['block_title'] ): ?><h5><?php echo $block['block_title']; ?></h5><?php endif; ?>
                                    <?php if( $block['block_hours'] ): ?><p><i class="icon icon-hours"></i><strong>Hours:</strong> <span><?php echo $block['block_hours']; ?></span></p><?php endif; ?>
                                    <div class="numbers-base">
                                        <?php if( $block['block_phone'] ): ?><p><i class="icon icon-phone"></i><strong>Phone:</strong> <a href="tel:<?php echo preg_replace( '/[^0-9]/', '', $block['block_phone'] ); ?>"><?php echo $block['block_phone']; ?></a></p><?php endif; ?>
                                        <?php if( $block['block_email'] ): ?><p><i class="icon icon-envelope"></i><a href="mailto:<?php echo $block['block_email']; ?>"><?php echo $block['block_email']; ?></a><?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach;
                    endif; ?>
                </div>

            </div>
        </div>
    </div>
</section>
<!-- Location Contact Blocks -->