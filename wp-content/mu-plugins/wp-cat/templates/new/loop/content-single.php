<?php $machine = CAT()->product(get_the_id()); ?>

<div class="col-xs-6 col-sm-4 product-card-details-block">
    <div class="product-card">
        <a href="<?php the_permalink(); ?>">
            <div class="product-card-detail-info">
                <h3 class="product-card__title text--center flush"><?php the_title(); ?></h3>
                <figure class="product-card__thumb text--center">
                    <?php echo cat_sized_image( reset($machine->images), array(312,233) ); ?>
                </figure>
                <dl class="product-card__stats flush">
                	<?php $specs = $machine->specs(3); ?>

                	<?php if (is_array($specs)): foreach($specs as $spec): ?>
                    <div class="product-card__stat-row">
                        <dt><?php echo $spec->name; ?></dt>
                        <dd data-english="<?php echo $spec->value_english.' '.$spec->unit_english; ?>"
                            data-metric="<?php echo $spec->value_metric.' '.$spec->unit_metric; ?>">
                            <?php echo $spec->value_english.' '.$spec->unit_english; ?>
                        </dd>
                    </div>
                    <?php endforeach; endif ?>
                </dl>
            </div>
            <button class="button button--secondary button--block text--left">View Full Specs</button>
        </a>
    </div>
</div>
