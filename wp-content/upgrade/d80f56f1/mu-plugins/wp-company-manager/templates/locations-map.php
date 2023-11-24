<?php
    $user_zipcode = null;
    if( ! empty( $_POST[ 'zipcode' ] ) ) {
        $user_zipcode = filter_var( $_POST[ 'zipcode' ], FILTER_SANITIZE_STRING );
    }
?>
<?php if ( ! is_singular( 'location' ) ): ?>
<!-- Zipcode Search -->
<div class="col-sm-4">
    <div class="wpcm-zip" id="wpcm_zip"><?php // do not change "wpcm-zip" class or "wpcm_zip" ID ?>
        <form class="wpcm-zip__form" method="POST"><?php // do not change "wpcm-zip__form" class ?>
            <div class="container-fluid">
                <div class="row">
                    <div class="input-field input-field--zip">
                        <input type="text" name="zipcode" class="wpcm-zip__zipcode" value="<?php echo $user_zipcode; ?>" placeholder="Enter Your Zip Code"><?php // do not rename input or change "wpcm-zip__zipcode" class ?>
                    </div>          
                    <div class="input-field input-field--submit">
                        <input type="hidden" name="distance" class="wpcm-zip__distance" value="25"><?php // do not rename input or change "wpcm-zip__distance" class ?>
                        <input type="submit" name="submit" class="wpcm-zip__submit" value="Submit"><?php // do not rename input or change "wpcm-zip__submit" class ?>
                        <input type="hidden" name="imahuman" id="imahuman" value=""><?php // do not rename input or change "imahuman" id ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- /Zipcode Search -->
<?php endif; ?>

<!-- Map -->
<div id="locations_map" style="width:100% !important;height:500px !important;"></div><?php // do not change "locations_map" ID ?>
<!-- /Map -->

<script type="text/template" id="markerTemplate"><?php // do not change "markerTemplate" ID ?>
    <div class="map-marker">
        <h6><%= title %></h6>
        <p><%= address %></p>
        <a class="btn-secondary" target="_blank" href="<%= directions %>">Get Directions</a><br>
    <?php if ( ! is_singular( 'location' ) ): // Only show this button on main map page ?>
        <a class="btn" href="<%= url %>">View Location Details</a>
    <?php endif; ?>
    </div>
</script>
