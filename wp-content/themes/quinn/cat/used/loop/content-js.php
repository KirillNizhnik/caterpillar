
<script type="text/template" id="machineGridItem">
<div class="flex-image-cards grid-item">
    <div class="product-card-details-block">
        <div class="product-card">
            <div class="product-card-detail-info">

            <div class="product-card product-card-detail-info">
                <div class="product-card__thumb">
                    <%= image %>
                </div>
                <div class="product-detail">
                    <a class="btn btn-primary" href="<%= url %>">View Product Details</a>
                </div>
                <div class="product-quote">
                    <a class="btn btn-secondary" href="<?php echo get_permalink( $post = 7122 ); ?>">Request a Quote</a>
                </div>
                <h4 class="product-card__title"><%= title %></h4>
                <div class="product-card__metas">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col-xxs-6 meta-title">
                            Hours
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col-xxs-6 meta-value">
                            <%= hours %>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col-xxs-6 meta-title">
                            Rating
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col-xxs-6 meta-value">
                            <%= rating %>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col-xxs-6 meta-title">
                            Serial Number
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col-xxs-6 meta-value">
                            <%= serialnumber %>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col-xxs-6 meta-title">
                            Price
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col-xxs-6 meta-value">
                            <%= price %>
                        </div>
                    </div>
                </div><!-- metas end -->
            </div>
           </div>
            <!-- <button class="button button--secondary button--block text--left">View Full Specs</button> -->
        </div>
    </div>
</div>
</script>
<script type="text/template" id="machineListTemplate">
<div class="row listview-title">
    <div class="col-lg-6 col-md-5 col-sm-5 col-xs-6 col-xxs-11">Product Name</div>
    <!-- <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 col-xxs-6">Year</div> -->
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 col-xxs-6">Manufacturer</div>
    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 col-xxs-6">Model</div>
    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 col-xxs-6">Hours</div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-1 col-xxs-6">Price</div>
</div>
</script>

<script type="text/template" id="machineListItem">
<div class="row listview product-card-details-block used-product-family">
    <div class="col-lg-3 col-md-5 col-sm-5 col-xs-6 col-xxs-11">
        <div class="">
            <div class="listview-info-thumb">
                <figure class="product-card__thumb">
                    <a href="<%= url %>"><%= image %></a>
                </figure>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-1 col-sm-1 col-xs-1 col-xxs-6 listview-info">
        <a href="<%= url %>"><%= title %></a>
    </div> 
    <!-- <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 col-xxs-6 listview-info">
        <span><%= year %></span>
    </div> -->
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 col-xxs-6 listview-info">
        <span><%= manufacturer %></span>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 col-xxs-6 listview-info">
        <span><%= model %></span>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 col-xxs-6 listview-info">
        <span><%= hours %></span>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-1 col-xxs-6 listview-info">
        <span><%= price %></span>
    </div>
</div>
</script>



