<!-- Static Locations List Template -->
<section id="locations_list__static">
    <div class="row">
    <?php foreach ( $locations as $location ): ?>

        <!-- Location Loop Template -->
        <div class="col-sm-6 col-md-4">
            <section class="location-listing">
                <h4 class="location-listing__name flush"><?php echo $location->title; ?></h4>
                <div class="location-listing__address">
                    <?php echo $location->address; ?>
                </div>
            <?php if ( ! empty( $location->email ) ): ?>
                <div class="location-listing__section">
                    <?php echo $location->email; ?>
                </div>
            <?php endif; ?>
            <?php if ( ! empty( $location->phone ) ): ?>
                <div class="location-listing__section">
                    <?php echo $location->phone; ?>
                </div>
            <?php endif; ?>
            <?php if ( ! empty( $location->hours ) ): ?>
                <div class="location-listing__section">
                    <?php echo $location->hours; ?>
                </div>
            <?php endif; ?>
                <div class="location-listing__section">
                    <a class="btn btn-tertiary" target="_blank" href="<?php echo $location->directions; ?>">Get Directions</a>
                    <a class="btn btn-primary" href="<?php echo $location->url; ?>">View Location Details</a>
                </div>
            </section>
        </div>
        <!-- /Location Loop Template -->

    <?php endforeach; ?>
    </div>
</section>
<!-- /Static Locations List Template -->
