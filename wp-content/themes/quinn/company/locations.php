


<section class="locations">
    <div id="location-list">

        <?php foreach($locations as $state => $location_list): ?>

            <div class="location-state">

                <h4 class="locations-listings__state"><?php echo $state; ?></h4>

                <?php foreach($location_list as $location): ?>

                <?php wpcm_template('location-loop', array('location' => $location)); ?>
                <?php endforeach; ?>

            </div>

        <?php endforeach; ?>

    </div>
</section>



<script type="text/template" id="listItemTemplate">

	<div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 col-xxs-12 location-item">

		<article class="card card--location" itemscope itemtype="http://schema.org/LocalBusiness">
			<div class="card__primary-info js-location" data-id="<%= id %>">
    			<div class="card__wrap">

    				<h4 class="card__title" itemprop="name"><span class="icon-map-pin-alt"></span> <span class="card__title-text"> <%= title %> </span></h4>

    				<div class="card__metas">

    					<address class="card__metas-address">
    						<p><%= address %></p>
    					</address>

    					<div class="card__metas-phone">
    						<b>Main Phone:</b> <%= phone %>
    					</div>

    					<div class="card__metas-services">
    						<b>Services:</b><br> <%= service_list %>
    					</div>

    				</div><!-- card metas end -->

			    </div>
			</div><!-- card__primary-info end -->


			<div class="card__secondary-info">
				<a class="btn-secondary btn-block" target="_blank" href="<%= directions %>">Get Directions</a>
			</div>

            <div class="card__secondary-info">
                <a class="btn-secondary btn-block" target="_blank" href="<%= url %>">More Info</a>
            </div>

		</article>

		<meta itemprop="branchof" content="Quinn Company">
		<meta itemprop="url" content="<%= url %>">


	</div><!-- col end -->

</script>
