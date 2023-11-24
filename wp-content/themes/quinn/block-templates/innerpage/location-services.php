<?php
    $heading = get_field('heading') ?? '';
    $services = get_field('services_boxes') ?? '';
?>

<!-- Services Boxes -->
<section class="location-services section-padding">
    <div class="container">
        <div class="row">
            <div class="col-xxs-12">

                <?php if( $heading): ?>
                    <h2 class="location-services__heading"><?php echo $heading; ?></h2>
                <?php endif; ?>

                <?php if( $services): ?>
                <div class="location-services__wrapper">

                    <?php foreach($services as $service): ?>
                    <div class="location-services__box col-xxs-12 col-md-4">
                        <div class="location-services__box-inner">
                            <p class="location-services__text"><?php echo $service['services_description']; ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>

                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>
<!-- Services Boxes -->