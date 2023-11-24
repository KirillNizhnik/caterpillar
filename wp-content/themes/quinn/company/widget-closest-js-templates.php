<script type="text/template" id="markerTemplate">
    <div class="map-marker">
        <h3><%= title %></h3>
        <p><%= address %></p>
        <a class="bold-link" target="_blank" href="<%= directions %>">Get Directions</a>
    </div>
</script>

<script type="text/template" id="closestCompactTemplate">
    <span><b>Closest Location:</b> <%=  address  %></span>
</script>

<script type="text/template" id="closestExpandedTemplate">
    <div class="row">
        <div class="col-xs-12 text--center">
            <h3 class="bordered-headline--small">Your Closest Location:</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="location-information-group">
                <!--<span class="icon-location"></span>-->
                <p><!--<strong><%= title %></strong>-->
                <%= address %></p>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="location-information-group">
                <span class="icon-phone"></span>
                <%= phone %>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="closestFormTemplate">
    <form id="closest-location-form">
        <input type="text" name="zipcode" value="">
        <input type="submit" class="button" value="Get Location" />
    </form>
</script>