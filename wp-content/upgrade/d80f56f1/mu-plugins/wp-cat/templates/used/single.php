<?php if( have_posts() ): while( have_posts() ): the_post(); ?>

	<?php
        $machine = CAT()->product();
        //var_dump($machine);
    ?>

    <div class="flexbox">

        <?php get_sidebar('product'); ?>
        <article class="page-article flexbox__item">

    	   <div class="row product-detail__top-section">

                <section class="product-detail__media-area col-sm-6  col-md-6">
                        <div class="row">
                            <?php cat_template('used/single/image-browser.php'); ?>
                        </div>
                </section>

            	<section class="col-sm-6 col-md-6 ">
                	<div class="product-detail__details-box product__overview clearfix">                    	

                        <h3 class="product-detail__price">Price: <span class="used-price"><?php if ( isset( $machine->price ) ) echo '$' . number_format($machine->price); else echo 'Request a Quote for Pricing'; ?></span></h3>
                        <dl class="clearfix">
                        	<dt>Hours:</dt>
                            <dd><?php echo isset($machine->hours) ? $machine->hours : '<span class="unavailable">N/A</span>'; ?></dd>

                            <dt>Rating:</dt>
                            <dd><?php echo isset($machine->rating) ? $machine->rating : '<span class="unavailable">N/A</span>'; ?></dd>

                            <dt>Serial Num:</dt>
                            <dd><?php echo isset($machine->serial_number) ? $machine->serial_number : '<span class="unavailable">N/A</span>'; ?></dd>

                            <dt>Phone:</dt>
                            <dd><?php echo isset($machine->contact->phone) ? $machine->contact->phone : '<span class="unavailable">N/A</span>'; ?></dd>

                            <dt>Contact:</dt>
                            <dd><?php echo $machine->contact->first_name . ' ' . $machine->contact->last_name ?></dd>

                            <dt>Location:</dt>
                            <dd><?php echo $machine->city .", " . $machine->state; ?></dd>
                        </dl>

                        <form action="<?php echo site_url('/machine-quote-request/' ); ?>" method="post">
                            <input type="hidden" name="machine" value="<?php echo $machine->id; ?>">
                            <input type="submit" class="button button--secondary button--block" value="Request a Quote" />
                        </form>

                        <div class="product-detail__secondary-buttons">
                            <a href="#" class="product__brochure" target="_blank" rel="nofollow">Download Product Brochure</a>
                            <div class="col-xxs-6 hard--left">
    						      <a href="mailto:?subject=CAT: <?php the_title(); ?>&body=<?php the_permalink(); ?>" class="button button--primary button--block"><span class="icon-envelope"></span> Email/Share</a>
                            </div>
                            <div class="col-xxs-6 hard--left">
    						      <a href="javascript:window.print()" class="button button--primary button--block hidden-xxs hidden-xs"><span class="icon-printer"></span> Print</a>
                            </div>
                        </div>
                    </div>
                </section>

            </div>

            <div class="row">
                <div class="col-xxs-12">
                    <ul class="link-bar clearfix">
                        <li><a href="<?php echo site_url(get_option('cat_financing_url', '#')); ?>">Get Financing Now</a></li>
                        <li><a href="<?php echo site_url(get_option('cat_demo_url', '#')); ?>">Schedule A Demo</a></li>
                        <li><a href="<?php echo site_url(get_option('cat_em_solutions_url', '#')); ?>">EM Solutions</a></li>
                        <li><a href="<?php echo site_url(get_option('cat_rent_url', '#')); ?>">Rent</a></li>
                    </ul>
                </div>
            </div>

            <section class="product-detail__tabs tabs">
                <div class="tabs__nav-wrapper clearfix">
                    <ul class="tabs__nav clearfix">
                        <?php if( ! empty($machine->post->post_content) ): ?>
                        <li class="tab-link active">
                            <a href="#desc">Description</a>
                        </li>
                        <?php endif; ?>

                        <?php if( ! empty($machine->features) ): ?>
                        <li class="tab-link<?php if( empty($machine->post->post_content) ) echo ' active'; ?>">
                            <a href="#features">Features</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="tabs__content">
                    <?php if( ! empty($machine->post->post_content) ): ?>
                    <div class="tab-item active clearfix" id="desc">
                        <?php the_content(); ?>
                    </div>
                    <?php endif; ?>

                    <?php if ( count($machine->features) > 0 ): ?>

                        <div class="product__features tab-item<?php if( empty($machine->post->post_content) ) echo ' active'; ?>" id="features">

                            <ul class="specs specs--list">
                                <?php foreach($machine->features as $feature): ?>

                                <li><?php echo $feature; ?>

                                <?php endforeach; ?>
                            </ul>

                        </div>

                    <?php endif; ?>

                </div>
            </section>
        </article>
	</div>

<?php endwhile; endif; ?>
