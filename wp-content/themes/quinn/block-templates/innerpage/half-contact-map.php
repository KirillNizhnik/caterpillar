<?php
    $location_phone = get_field('phone') ?? '';
    $location_hours = get_field('hours') ?? '';
    $location_address = get_field('address') ?? '';
    $location_directions = get_field('directions_link') ?? '';
    $location_maillist = get_field('email_addresses') ?? '';

?>

<!-- Location Half Contact And Map -->
<section class="half-contact-map clearfix">
    <div class="half-contact__background">
        <?php echo fx_get_image_tag( 431, '', 'half-contact__bg' ); ?>
    </div>
    <div class="flex-row">
        <div class="half-contact-information col-xxs-12 col-md-6">
            <div class="half-contact__wrapper section-padding">
            <?php if($location_phone): ?>
                <p class="half-contact__phone"><i class="icon icon-phone"></i><strong>Phone: </strong><a href="tel:<?php echo preg_replace( '/[^0-9]/', '', $location_phone ); ?>"><?php echo $location_phone; ?></a></p>
            <?php endif; ?>

            <?php if($location_maillist): ?>
                <dl class="half-contact__maillist clearfix">
                    <?php foreach($location_maillist as $mail): ?>
                        <dt class="half-contact__email-title"><p><strong><?php echo $mail['email_title']; ?></strong></p></dt>
                        <dd class="half-contact__email-address"><p><a href="mailto:<?php echo $mail['email_address']; ?>"><?php echo $mail['email_address']; ?></a></p></dd>
                    <?php endforeach; ?>
                </dl>
            <?php endif; ?>

            <?php if($location_hours): ?>
                <p class="half-contact__hours"><i class="icon icon-hours"></i><strong>Hours:</strong> <?php echo $location_hours; ?></p>
            <?php endif; ?>

            <?php if($location_address): ?>
                <p class="half-contact__address"><i class="icon icon-location-on"></i><strong>Address:</strong> <?php echo $location_address; ?></p>
            <?php endif; ?>

            <?php if($location_directions):
                $dir_url = $location_directions['url'];
                $dir_title = $location_directions['title'];
                $dir_target = $location_directions['target'] ? $location_directions['target'] : '_self';
            ?>
                <p class="half-contact__directions"><a class="btn btn-primary" href="<?php echo esc_url( $dir_url ); ?>" target="<?php echo esc_attr( $dir_target ); ?>"><span class="icon-location-on"></span><?php echo esc_html( $dir_title ); ?></a></p>
            <?php endif; ?>

            </div>
        </div>
        <div class="half-map right col-xxs-12 col-md-6">
            <div class="half-map__wrapper">
                <?php the_field('map_iframe'); ?>
            </div>
        </div>
    </div>
</section>
<!-- Location Half Contact And Map -->