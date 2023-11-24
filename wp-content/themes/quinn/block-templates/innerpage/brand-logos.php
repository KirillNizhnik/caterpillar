<?php
    $sell_heading = get_field('sell_heading') ?? '';
    $sell_logos = get_field('sell_logos') ?? '';

    $show_service = get_field('show_service_section');
    $service_heading = get_field('service_heading') ?? '';
    $service_logos = get_field('service_logos') ?? '';
?>

<!-- Brand Logos -->
<section class="brand-logos section-padding">
    <div class="container">
        <div class="row">
            <div class="col-xxs-12">

                <?php if( $sell_logos ): ?>

                <div class="brand-logos__sell">

                    <?php if( $sell_heading): ?>
                    <h3 class="brand-logos__heading"><?php echo $sell_heading; ?></h3>
                    <?php endif; ?>

                    <div class="brand-logos__wrapper">

                        <?php foreach($sell_logos as $sell): ?>
                            <div class="brand-logos__item">
                                <div class="brand-logos__item-inner">
                                    <div class="brand-logos__item-image">
                                        <?php echo fx_get_image_tag( $sell['sell_logo_image'] ); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                    </div>
                </div>

                <?php endif; ?>

                <?php if ( $show_service ): ?>

                <div class="brand-logos__service">

                    <?php if( $service_heading): ?>
                    <h3 class="brand-logos__heading"><?php echo $service_heading; ?></h3>
                    <?php endif; ?>

                    <div class="brand-logos__wrapper">

                        <?php foreach($service_logos as $service): ?>
                        <div class="brand-logos__item">
                            <div class="brand-logos__item-inner">
                                <div class="brand-logos__item-image">
                                    <?php echo fx_get_image_tag( $service['service_logo_image'] ); ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>

                    </div>
                </div>

                <?php endif; ?>

            </div>
        </div>
    </div>
</section>
<!-- Brand Logos -->