<!-- Locations List Template -->
<section id="locations_list" class="row"><?php // do not change "locations_list" ID ?>
<?php foreach ( $locations as $location ): ?>

    <?php // NOTE: any edits made to this section should also be made to the JS template below ?>
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
</section>
<!-- /Locations List Template -->

<?php // NOTE: any edits made to this section should also be made to the PHP template above ?>
<script type="text/template" id="listItemTemplate">
    <div class="col-sm-6 col-md-4">
        <section class="location-listing">
            <h4 class="location-listing__name flush"><%= title %></h4>
            <div class="location-listing__address">
                <%= address %>
            </div>
        <% if ( email ) { %>
            <div class="location-listing__section">
                <%= email %>
            </div>
        <% } %>
        <% if ( phone ) { %>
            <div class="location-listing__section">
                <%= phone %>
            </div>
        <% } %>
        <% if ( hours ) { %>
            <div class="location-listing__section">
                <%= hours %>
            </div>
        <% } %>
            <div class="location-listing__section">
                <a class="btn btn-tertiary" target="_blank" href="<%= directions %>">Get Directions</a>
                <a class="btn btn-primary" href="<%= url %>">View Location Details</a>
            </div>
        </section>
    </div>
</script>
