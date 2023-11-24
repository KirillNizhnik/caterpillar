<?php echo $before_widget; ?>

<!-- Closest Location Widget -->
<div class="row">
<?php if ( $title ): ?>
	<div class="col-sm-12 text--center">
    	<h3 class="bordered-headline--small"><?php echo $title; ?></h3>
    </div>
<?php endif; ?>
    <div id="closest_location"><?php // do not change "closest_location" ID ?>
        <div class="col-sm-12">
            <a href="#" id="get_geolocation"><?php echo $prompt; ?></a><?php // do not change "get_geolocation" ID ?>
        </div>
    </div>
</div>
<!-- /Closest Location Widget -->

<script type="text/template" id="closestLocationTemplate">
    <div class="col-sm-12 location-info">
        <div class="location-info__group">
            <span class="icon-location"></span>
            <p><strong><%= title %></strong><br/>
            <%= address %></p>
        </div>
        <div class="location-info__group">
            <span class="icon-phone"></span>
            <p><%= phone %></p>
        </div>
        <a target="_blank" href="<%= directions %>">Directions</a>
    </div>
</script>

<?php echo $after_widget; ?>