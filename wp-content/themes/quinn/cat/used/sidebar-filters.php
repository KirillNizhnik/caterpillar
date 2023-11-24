<?php 

/*
* Search partial for used equipment
* 
*/



?>

<aside class="page-sidebar">
                     <!--   <form name="equipment-search" id="js-search-form-used" class="search-equipment-form js-search-form" action="/equipment/search" method="post">
                            <input type="hidden" name="action" value="<?php //echo (! is_equipment_search()) ? 'equipment_search_post' : 'equipment_search_refresh'; ?>" />
                            <input type="hidden" name="source" value="used"> -->

                            <h3 class="search-equipment-form-title">Search Equipment</h3>
                            <div class="sidebar-form-elements">
                                <label for="family">Family:</label>
                                <div name="family" id="family" class="">
                                <?php
                                    echo do_shortcode('[facetwp facet="used_families"]'); 
                                ?>
                                </div>

                                <label for="price">Price:</label>
                                <div name="price" id="price" class="">
                                <?php
                                    echo do_shortcode('[facetwp facet="used_price"]');
                                ?>
                                </div>

                                <label for="hours">Hours:</label>
                                <div name="hours" id="hours" class="">
                                <?php
                                    echo do_shortcode('[facetwp facet="used_hours"]');
                                ?>
                                </div>
                                
                                <label for="year">Year</label>
                                <div name="year" id="year">
                                <?php
                                    echo do_shortcode('[facetwp facet="used_year"]');
                                ?>
                                </div>
                                

                                <label for="model">Model:</label>
                                <div name="model" id="model" class="">
                                <?php
                                    echo do_shortcode('[facetwp facet="used_model"]');
                                ?>
                                </div>

                                <label for="make">Manufacturer:</label>
                                <div name="manufacturer" id="manufacturer" class="">
                                <?php
                                    echo do_shortcode('[facetwp facet="used_manufacturer"]');
                                ?>
                                </div>

                                <!-- <input type="submit" class="btn btn-primary search-submit" value="Search"> -->
                            </div>
                        <!--</form>-->
                    </aside>
                    
                