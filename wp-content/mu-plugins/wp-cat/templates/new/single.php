<?php if( have_posts() ): while( have_posts() ): the_post(); ?>

    <?php
        $product = CAT()->product();
    ?>



    	<div class="flexbox">

            <?php get_sidebar() ?>

            <article class="page-article flexbox__item">

                <div class="col-md-6">
                    <?php cat_template('new/single/image-browser.php'); ?>
                </div>

    	    	<div class="col-md-6">

                    <div class="product__overview clearfix">
                        <h1 class="flush"><?php the_title(); ?></h1>
                        <?php the_content(); ?>

                        <div class="product__actions">
                            <form action="<?php echo site_url('/machine-quote-request/' ); ?>" method="post">
                                <input type="hidden" name="machine" value="<?php echo $product->id; ?>">
                                <input type="submit" class="button button--secondary button--block" value="Request a Quote" />
                            </form>

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
                	<?php if (is_array($specs)): foreach($specs as $spec): ?>
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

            <div class="row">
                <div class="col-xxs-12">
                    <ul class="link-bar clearfix">
                        <li><a href="<?php echo site_url(get_option('cat_financing_url', '#')); ?>">Get Financing Now</a></li>
                        <li><a href="<?php echo site_url(get_option('cat_demo_url', '#')); ?>">Schedule A Demo</a></li>
                        <li><a href="<?php echo site_url(get_option('cat_em_solutions_url', '#')); ?>">EM Solutions</a></li>
                    <?php
                        $rental_url = $product->rental_url();
                        if ($rental_url != "#"):
                    ?>
                        <li><a href="<?php echo $rental_url; ?>">Rent</a></li>
                    <?php endif ?>
                    </ul>
                </div>
            </div>

            <section class="product__details tabs">
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
        </article>
    </div>


<?php endwhile; endif; ?>
