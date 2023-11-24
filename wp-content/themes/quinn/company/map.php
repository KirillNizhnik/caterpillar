<section class="locations">
	<div class="locations-map" id="locations-map" > </div>
</section>

<script type="text/template" id="markerTemplate">

    <div class="map-marker">

        <h3><%= title %></h3>

		<div class="card__metas">

			<address class="card__metas-address">
				<%= address %>
			</address>

			<div class="card__metas-services">
				<b>Services:</b><br> <%= service_list %>
			</div>

			<div class="card__metas-phone">
				<b>Main Phone:</b> <%= phone %>
			</div>

			<a target="_blank" href="<%= directions %>" class="btn-secondary btn-block"><small>Get Directions</small></a>
			<a href="<%= url %>" class="btn-secondary btn-block"><small>More Details</small></a>
		</div><!-- card metas end -->


    </div>
</script>
