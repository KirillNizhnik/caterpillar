<?php if( have_posts() ): while( have_posts() ): the_post(); ?>

    <?php
        $product = CAT()->product();
    ?>


    <div class="flexbox">

        <?php get_sidebar() ?>

        <article class="page-article flexbox__item">

            <div class="col-md-6">
                <?php cat_template('new/single/image-browser.php'); ?>

                <br>
                <h4>List Rates</h4>

                <?php $rates_displayed = false ?>
                <?php if (!empty($product->rental_rates)): ?>
                    <?php $rates = $product->rental_rates ?>
                    <table class="product__specs specs specs--table specs--table-small clearfix">
                    <?php foreach($rates as $rate_data) : ?>
                        <?php
                            $rate = $rate_data['currency_value'];
                            $name = $rate_data['period'];
                        ?>
                        <?php if ( floatval($rate) != 0 ): ?>
                            <?php $rates_displayed = true ?>
                            <tr>
                                <td>
                                    <dt><?php echo ucwords($name); ?></dt>
                                    <dd><?php echo '$'. number_format($rate); ?></dd>
                                </td>                        
                            </tr>
                        <?php endif ?>

                    <?php endforeach; ?>

                    </table>
                <?php endif; ?>

                <?php if (!$rates_displayed): ?>
                    Online rates are currently unavailable. Please contact this store for more details.
                <?php endif; ?>

            </div>

            <div class="col-md-6">

                <div class="product__overview clearfix">
                    <h1 class="flush"><?php the_title(); ?></h1>
                    <?php the_content(); ?>

                    <div class="product__actions">

                        <?php if( ! empty($product->documents) ): ?>
                        <?php $doc = array_shift($product->documents); ?>
                        <a href="<?php echo $doc->src; ?>" class="product__brochure" target="_blank" rel="nofollow">Download Product Brochure</a>
                        <?php endif; ?>

                        <div class="col-xxs-6 hard--left">
                            <a href="mailto:?subject=CAT: <?php the_title(); ?>&body=<?php the_permalink(); ?>" class="button button--primary button--block">
                                <span class="icon-envelope"></span> Email/Share
                            </a>
                        </div>
                        <div class="col-xxs-6 hard--right">
                            <a href="javascript:window.print()" class="button button--primary button--block hidden-xxs hidden-xs">
                                <span class="icon-printer"></span> Print
                            </a>
                        </div>
                    </div>
                </div>

                <?php $specs = $product->specs(4); ?>

                <table class="product__specs specs specs--table specs--table-small clearfix">
                <?php if(is_array($specs)): foreach($specs as $spec): ?>
                    <tr>
                        <td>
                            <dt><?php echo $spec->name; ?></dt>
                            <dd data-english="<?php echo $spec->value_english.' '.$spec->unit_english; ?>"
                                data-metric="<?php echo $spec->value_metric.' '.$spec->unit_metric; ?>">
                                <?php echo $spec->value_english.' '.$spec->unit_english; ?>
                            </dd>
                        </td>
                    </tr>
                <?php endforeach; endif ?>
                </table>

            </div>

            <section class="product__details tabs col-md-12 soft--top">
                <div class="tabs__nav-wrapper clearfix">
                    <ul class="tabs__nav clearfix">
                    <?php if (!empty($product->specs)): ?>
                        <li class="tab-link active"><a href="#specs">Specifications</a></li>
                    <?php endif ?>
                    <?php if (!empty($product->features)): ?>
                        <li class="tab-link"><a href="#features">Benefits and Features</a></li>
                    <?php endif ?>
                    <?php if (!empty($product->standard_equipment)): ?>
                        <li class="tab-link"><a href="#standard">Standard/Optional Features</a></li>
                    <?php endif ?>
                    </ul>
                </div>
                <div class="tabs__content">
                    <?php cat_template('new/single/specs'); ?>
                    <?php cat_template('new/single/features'); ?>
                    <?php cat_template('new/single/equipment'); ?>
                </div>

            </section>
            <div class="clearfix col-md-12" style="font-size:.8em">
                <small>
                    *This estimate is for the base rental charge only. Other fees and charges may apply and will be calculated at the time of rental. Does not include delivery fees.<br/>
                    *Images are for general reference only. Individual product models may vary. Please consult with your dealer to verify machine configuration.
                </small>
            </div>
        </article>
    </div>

<?php endwhile; endif; ?>
