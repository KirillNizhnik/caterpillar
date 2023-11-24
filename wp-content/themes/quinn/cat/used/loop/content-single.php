<?php $machine = CAT()->product(get_the_id()); ?>
<div class="product-card-details-block">
    <div class="product-card">
        <div class="product-card-detail-info">

            <figure class="product-card__thumb text--center">
                <?php echo cat_sized_image( reset($machine->images), array(600,400) ); ?>
            </figure>
            <div class="product-detail hidden-sm-down">
                <a class="btn btn-primary" href="<?php the_permalink(); ?>">View Product Details</a>
            </div>
            <div class="product-quote hidden-sm-down">
                <a class="btn btn-secondary" href="<?php echo get_permalink(9677) . '?yourmachine=' . $machine->post->post_title; ?>">Request a Quote</a>
            </div>
            <h4 class="product-card__title"><?php the_title(); ?></h4>
            <dl class="product-card__stats flush">
                <div class="product-card__stat-row">
                    <dt>Price:</dt>
                    <dd><?php if ( !empty($machine->price ) && is_numeric($machine->price) ) {
                    echo '$' . number_format($machine->price);
                    }
                    elseif( !empty($machine->price ) && is_string($machine->price) ) {
                        echo $machine->price;
                    }
                    else  {
                    echo 'Request a Quote for Pricing';
                    } ?>

                    </dd>
                </div>
                <div class="product-card__stat-row">
                    <dt>Hours:</dt>
                    <dd><?php echo !empty($machine->hours) ? $machine->hours : '<span class="unavailable">Contact us for Info</span>'; ?></dd>
                </div>

                <div class="product-card__stat-row">
                    <dt>Location:</dt>
                   <?php if(!isset($machine->city)){ $machine->city = ""; } ?>
                   <?php if(!isset($machine->state)){ $machine->state = ""; } ?>
                    <dd><?php if (!empty($machine->city) || !empty($machine->state) ) { echo $machine->city .", " . $machine->state; }
                        else { echo '<span class="unavailable">Contact us for Info</span>'; }
                        ?>
                    </dd>
                </div>
            </dl>
            <div class="product-buttons--mobile hidden-md-up">
                <div class="product-detail">
                    <a class="btn btn-primary" href="<?php the_permalink(); ?>">View Product Details</a>
                </div>
                <div class="product-quote">
                    <a class="btn btn-secondary" href="<?php echo get_permalink(9682) . '?yourmachine=' . $machine->post->post_title; ?>">Request a Quote</a>
                </div>
            </div>
        </div>
        <!-- <button class="button button--secondary button--block text--left">View Full Specs</button> -->
    </div>
</div>
