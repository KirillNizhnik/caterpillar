<?php $machine = CAT()->product(get_the_id()); ?>
<div class="row listview product-card-details-block used-product-family">
    <div class="col-lg-3 col-md-5 col-sm-5 col-xs-6 col-xxs-11">
        <div class="">
            <div class="listview-info-thumb">
                <figure class="product-card__thumb">
                    <a href="<?php the_permalink(); ?>"> <?php echo cat_sized_image( reset($machine->images), array(312,233) ); ?></a>
                </figure>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-1 col-sm-1 col-xs-1 col-xxs-6 listview-info">
        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 col-xxs-6 listview-info">
        <span><?php if(!isset($machine->city)){ $machine->city = ""; } ?>
                   <?php if(!isset($machine->state)){ $machine->state = ""; } ?>
                    <?php if (isset($machine->city) || isset($machine->state) ) { echo $machine->city .", " . $machine->state; }
                        else { echo '<span class="unavailable">N/A</span>'; }
                        ?>
                    </span>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 col-xxs-6 listview-info">
        <!-- <span><%= model %></span> -->
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 col-xxs-6 listview-info">
        <span><?php echo isset($machine->hours) ? $machine->hours : '<span class="unavailable">N/A</span>'; ?></span>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-1 col-xxs-6 listview-info">
        <span><?php if ( !empty($machine->price ) ) echo '$' . number_format($machine->price); else echo 'Request a Quote for Pricing'; ?></span>
    </div>
</div>
