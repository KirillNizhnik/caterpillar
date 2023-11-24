<?php
    $user_zipcode = null;
    if( ! empty( $_POST[ 'zipcode' ] ) ) {
        $user_zipcode = filter_var( $_POST[ 'zipcode' ], FILTER_SANITIZE_STRING );
    }
    //setting some variables
$args = array( 'hide_empty' => 1 );
$service_terms = get_terms( 'service', $args );
?>
<?php if ( ! is_singular( 'location' ) ): ?>


    <div class="row fx_test">

        <!-- Zipcode Search -->
        <div class="location-filter-side">
            <div class="wpcm-zip" id="wpcm_zip"><?php // do not change "wpcm-zip" class or "wpcm_zip" ID ?>
                <form class="wpcm-zip__form" method="POST"><?php // do not change "wpcm-zip__form" class ?>
                        <div class="location-search-zip col-xxs-12 col-sm-6">
                            <h5>Search by Zip Code</h5>
                            <div class="input-field input-field--zip">
                                <input type="text" name="zipcode" class="wpcm-zip__zipcode" value="<?php echo $user_zipcode; ?>" placeholder="Enter Your Zip Code"><?php // do not rename input or change "wpcm-zip__zipcode" class ?>
                            </div>
                            <div class="input-field input-field--submit">
                                <input type="hidden" name="distance" class="wpcm-zip__distance" value="25"><?php // do not rename input or change "wpcm-zip__distance" class ?>
                                <input type="submit" name="submit" class="wpcm-zip__submit btn btn-secondary" value="Search" ><?php // do not rename input or change "wpcm-zip__submit" class ?>
                                <input type="hidden" name="imahuman" id="imahuman" value=""><?php // do not rename input or change "imahuman" id ?>
                            </div>
                        </div>
                        <div class="filter-search-category col-xxs-12 col-sm-6">
                            <h5>Filter by Service</h5>
                            <div class="filter-search-category-select">
                                <select class="wpcm-service__dropdown">
                                    <option value="">ANY TYPE</option>
                                    <?php foreach ( $service_terms as $term ) : ?>
                                    <option value="<?php echo $term->slug; ?>"><?php echo $term->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                               <!-- <select class="wpcm-service__dropdown">
                                    <option value="">ANY TYPE</option>
                                    <option value="new">New</option>
                                    <option value="used">Used</option>
                                    <option value="rental">Rental</option>
                                </select> -->
                            </div>
                            <div class="filter-search-category-button">
                                <button type="submit" name="submit" class="wpcm-zip__submit btn btn-secondary" value="Submit" >Search</button>
                            </div>
                        </div>
                </form>
            </div>
        </div>
        <!-- /Zipcode Search -->
    </div>

<?php endif; ?>



<script type="text/template" id="markerTemplate"><?php // do not change "markerTemplate" ID ?>
    <div class="map-marker">
        <h5><%= title %></h5>
        <p><%= address.replace('USA', '') %> <%= postal_code %></p>
        <p><b>SERVICES:</b><%= service_list %></p>
        <p><span class="icon-phone-alt"></span><%= phone %> </p>
        <!-- <a class="btn btn-secondary" target="_blank" href="<%= directions %>">Get Directions</a><br> -->
    <?php if ( ! is_singular( 'location' ) ): // Only show this button on main map page ?>
        <a class="btn btn-tertiary" href="<%= url %>">More Details</a>
    <?php endif; ?>
    </div>
</script>
