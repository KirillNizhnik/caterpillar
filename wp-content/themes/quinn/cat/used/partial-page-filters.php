<?php
/*
* Search partial sidebar to pull into used equipment searches
*
*/


?>


<aside class="page-sidebar">
    <h5 class="search-equipment-form-title">Search Equipment</h5>
    <div class="sidebar-form-elements row hard-sides">
        <div class="col-md-3">
            <label for="family">Family:</label>
            <div name="family" id="family" class="">
                <?php echo do_shortcode('[facetwp facet="used_families"]'); ?>
            </div>
        </div>
        <div class="col-md-3">
            <label for="year">Year</label>
            <div name="year" id="year">
                <?php echo do_shortcode('[facetwp facet="used_year"]');?>

            </div>
        </div>
        <div class="col-md-3">
            <label for="model">Model:</label>
            <div name="model" id="model" class="">
                <?php echo do_shortcode('[facetwp facet="used_model"]');?>
            </div>
        </div>
        <div class="col-md-3">
            <label for="make">Manufacturer:</label>
            <div name="manufacturer" id="manufacturer" class="">
                <?php echo do_shortcode('[facetwp facet="used_manufacturer"]'); ?>
            </div>
        </div>
        <div class="col-md-4">
            <label for="price">Price:</label>
            <div name="price" id="price" class="">
                <?php echo do_shortcode('[facetwp facet="used_price"]');?>
            </div>
        </div>
        <div class="col-md-4">
            <label for="hours">Hours:</label>
            <div name="hours" id="hours" class="">
                <?php echo do_shortcode('[facetwp facet="used_hours"]');?>
            </div>
        </div>
        <div class="col-md-4">
            <button class="fx-machine-search-reset" style="display:none" onclick="FWP.reset()">Reset</button>
            <div style="display:none"><?php  echo facetwp_display( 'template', 'used_equipment_all' ); //important this stays hidden ?></div>
            <button class="fwp-submit btn btn-secondary btn-block" data-href="/equipment/search/">Submit</button> <!-- fwp-submit class and data-href need to stay -->
        </div>
    </div>
</aside>






<?php

?>
