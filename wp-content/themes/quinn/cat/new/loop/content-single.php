<?php 
    $machine = CAT()->product(get_the_id());
    $uri = $_SERVER['REQUEST_URI'];
    $newFormLink = false;
    $family_urls = ['/new-equipment/machines/backhoe-loaders/', '/new-equipment/machines/excavators/', '/new-equipment/machines/skid-steer-and-compact-track-loaders/', '/new-equipment/machines/compact-track-loaders/', '/new-equipment/machines/mini-excavators/', '/new-equipment/machines/skid-steer-loaders/', '/new-equipment/machines/medium-excavators/', '/new-equipment/machines/large-excavators/', '/new-equipment/machines/wheel-excavators/', '/new-equipment/machines/small-excavators/'];
    foreach($family_urls as $url){
        if(str_contains($uri, $url)){
            $newFormLink = true;
            break;
        }
    }
?>
<div class="product-card-details-block">
    <div class="product-card">
        <div class="product-card-detail-info">
            
            <figure class="product-card__thumb text--center">
            <?php
            //var_dump($machine); die();
            if(get_field('is_image_blurry', get_the_id()) == 'yes') {
                echo cat_sized_image( 7339, array(600,400)  ); 
            } elseif (!empty(get_the_post_thumbnail_url()) ){
                       
                        echo cat_sized_image(get_the_post_thumbnail_url(),  array(600,400)  );
            } elseif (!empty($machine->images) ){
                       
                        echo cat_sized_image(reset($machine->images),  array(600,400)  );
            }  else {
                echo cat_sized_image( 7339, array(600,400)  );
            }
             ?>
            </figure>
            <div class="product-detail hidden-sm-down">
                <a class="btn btn-primary" href="<?php the_permalink(); ?>">View Product Details</a>
            </div>
            <div class="product-quote hidden-sm-down">
                <?php if($newFormLink){ ?>
                   <!-- <a class="btn btn-secondary" href="<?php //echo get_permalink(7122) . '?yourmachine=' . $machine->post->post_title; ?>">Request a Quote</a>-->
                   <?php //echo do_shortcode('[fx_modal id=19864]'); wp_reset_postdata(); ?>
                   <a class="fx-modal-open-button btn btn-primary" id="20325" data-product-title="<?php echo get_the_title(); ?>">Request a Quote</a>
                <?php } else { ?>
                    <a class="btn btn-secondary" href="<?php echo get_permalink(9682) . '?yourmachine=' . $machine->post->post_title; ?>">Request a Quote</a>
                <?php } ?>
            </div>
            <h4 class="product-card__title"><?php the_title(); ?></h4>
            <dl class="product-card__stats flush">
            	<?php $specs = $machine->specs(3); ?>

            	<?php if (is_array($specs)): foreach($specs as $spec):  if($spec->name == "Note" || $spec->name == "Note (1)" || $spec->name == "Note (2)" | $spec->name == "Note (3)" | $spec->name == "Note (4)") continue;?>
                <div class="product-card__stat-row">
                    <dt><?php echo $spec->name; ?></dt>
                    <dd data-english="<?php echo $spec->value_english.' '.$spec->unit_english; ?>"
                        data-metric="<?php echo $spec->value_metric.' '.$spec->unit_metric; ?>">
                        <?php echo $spec->value_english.' '.$spec->unit_english; ?>
                    </dd>
                </div>
                <?php endforeach; endif ?>
            </dl>
            <div class="product-buttons--mobile hidden-md-up">
                <div class="product-detail">
                    <a class="btn btn-primary" href="<?php the_permalink(); ?>">View Product Details</a>
                </div>
                <div class="product-quote">
                    <?php if($newFormLink){ ?>
                        <!--<a class="btn btn-secondary" href="<?php //echo get_permalink(7122) . '?yourmachine=' . $machine->post->post_title; ?>">Request a Quote</a>-->
                        <a class="fx-modal-open-button btn btn-primary" id="19864">Request a Quote</a>
                    <?php } else { ?>
                        <a class="btn btn-secondary" href="<?php echo get_permalink(9682) . '?yourmachine=' . $machine->post->post_title; ?>">Request a Quote</a>
                    <?php } ?>
                </div>
            </div>
        </div>
        <!-- <button class="button button--secondary button--block text--left">View Full Specs</button> -->
    </div>
</div>
