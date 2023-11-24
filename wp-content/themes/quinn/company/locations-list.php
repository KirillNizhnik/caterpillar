<!-- Locations List Template -->
<section id="locations_list" class="row"><?php // do not change "locations_list" ID ?>

<?php foreach ( $locations as $location ): ?>


    <?php // NOTE: any edits made to this section should also be made to the JS template below ?>
    <!-- Location Loop Template -->
    <div class="col-xxs-12 col-md-3 col-sm-6">
        <div class="search-card" id="list-<?php echo $location->id; ?>">
            <div class="search-card-info">
                <h4><?php echo $location->title; ?></h4>
                <div class="search-card-gray-info">
                    <div class="search-card-gray-info-item">
                        <div class="location-listing__address">
                            <?php echo $location->address; ?>
                        </div>
                    </div>
                    <div class="search-card-gray-info-item">
                        <h5>Phone</h5>
                        <a href="tel:<?php echo $location->phone; ?>"><?php echo $location->phone; ?></a>
                    </div>
                    <div class="search-card-gray-info-item">
                        <h5 class="location-service-toggle">Services</h5>
                        <div class="location-service-info">
                            <?php
                            $services = get_the_terms($location->id, 'service');
                            $services_pretty = array();
                            foreach($services as $service ) {
                                $services_pretty[] = $service->name;
                            }
                     
                            if( is_array($services_pretty) ) {
                                $services_real = implode(", ", $services_pretty);
                            } else if( is_string($services_pretty) ){
                                $services_real = $services_pretty;
                            } else {
                                $services_real = "";
                            }
                         
                            echo $services_real;
                            
                            ?>
                        </div>
                    </div>
                    <div class="search-card-info-button">
                        <a class="btn btn-primary" target="_blank" href="<?php echo $location->directions; ?>">Get Directions</a>
                        <a class="btn btn-secondary" target="_blank" href="<?php echo $location->url; ?>">More info</a>
                    </div>  
                </div>
            </div>
        </div>
    </div>
    <!-- /Location Loop Template -->

<?php endforeach; ?>

</section>

<!-- /Locations List Template -->

<?php // NOTE: any edits made to this section should also be made to the PHP template above ?>
<script type="text/template" id="listItemTemplate">
    <div class="col-xxs-12 col-md-3 col-sm-6">
        <div class="search-card" id="list-<?php echo $location->id; ?>">
            <div class="search-card-info">
                <h4><%= title %></h4>
                <div class="search-card-gray-info">
                    <div class="search-card-gray-info-item">
                        <div class="location-listing__address">
                            <%= address.replace('USA', '') %> <%= postal_code %>
                        </div>
                    </div>
                    <div class="search-card-gray-info-item">
                        <h5>Phone</h5>
                        <a href="tel:<%= phone %>"><%= phone %></a>
                    </div>
                    <div class="search-card-gray-info-item">
                        <h5 class="location-service-toggle">Services</h5>
                        <div class="location-service-info">
                            <%= service_list %>
                        </div>
                    </div>
                    <div class="search-card-info-button">
                        <a class="btn btn-primary" target="_blank" href="<%= directions %>">Get Directions</a>
                        <a class="btn btn-secondary" target="_blank" href="<%= url %>">More info</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>
