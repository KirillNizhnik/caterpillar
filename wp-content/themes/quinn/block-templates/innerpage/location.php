<section class="machine-cards location-card fx_test__machine-cards">
    <div class="container-fluid">
        <div class="row">
            <div class="filter-category__top">
                <div class="container">
                    <h2 class="filter-category__heading">Find Your Nearest Branch</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="container machine-cards__container section-padding">
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="filter-category clearfix">

                    <div class="inner-location-map container">
                        <?php echo do_shortcode( '[locations-map]' ); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="machine-cards-wrapper search-card-machine">
            <?php echo do_shortcode('[locations-list]'); ?>
        </div>
    </div>
</section>