<?php get_header(); ?>


<?php echo get_template_part('partials/masthead') ?>

<?php if (have_posts()): while (have_posts()): the_post(); ?>

    <?php
    $machine = CAT()->product();

    ?>
    <?php if ($machine->status === 'not_available'): ?>
        <div class="not-avaliable-tag" style="color: red; text-align: center; padding-top: 20px">Model Discontinued,
            please see current model
        </div>
    <?php endif; ?>
    <div class="container cat-used-container">

        <div class="flexbox">

            <!-- <?php get_sidebar('product'); ?> -->
            <article class="page-article flexbox__item">

                <div class="row product-detail__top-section">

                    <section class="product-detail__media-area col-sm-6  col-md-5">
                        <div class="row">
                            <?php cat_template('used/single/image-browser.php');

                            //var_dump($machine);?>
                        </div>
                    </section>

                    <section class="col-sm-6 col-md-offset-1 col-md-6 ">
                        <div class="product-detail__details-box product__overview clearfix">

                            <h3 class="product-detail__price">Price: <span
                                        class="used-price"><?php if (!empty($machine->price)) echo '$' . number_format($machine->price); else echo 'Request a Quote for Pricing'; ?></span>
                            </h3>
                            <dl class="clearfix">

                                <div class="product-details-cont">
                                    <dt>Hours:</dt>
                                    <dd><?php if (!empty($machine->hours) && $machine->hours !== 0) {
                                            echo $machine->hours;
                                        } else {
                                            echo '<span class="unavailable">N/A</span>';
                                        }
                                        ?></dd>
                                </div>
                                <div class="product-details-cont">
                                    <dt>Rating:</dt>
                                    <dd><?php echo isset($machine->rating) ? $machine->rating : '<span class="unavailable">N/A</span>'; ?></dd>
                                </div>
                                <div class="product-details-cont">
                                    <dt>Serial Num:</dt>
                                    <dd><?php echo isset($machine->serial_number) ? $machine->serial_number : '<span class="unavailable">N/A</span>'; ?></dd>
                                </div>
                                <div class="product-details-cont">
                                    <dt>Phone:</dt>
                                    <dd><?php echo isset($machine->contact->phone) ? $machine->contact->phone : '<span class="unavailable">N/A</span>'; ?></dd>
                                </div>
                                <div class="product-details-cont">
                                    <dt>Contact:</dt>
                                    <dd><?php
                                        if (!empty($machine->contact)) {
                                            $first_name = $machine->contact->first_name ?? '';
                                            $last_name = $machine->contact->last_name ?? '';
                                            echo "$first_name $last_name";
                                        }


                                        ?></dd>
                                </div>
                                <div class="product-details-cont">
                                    <dt>Location:</dt>
                                    <dd><?php echo $machine->city . ", " . $machine->state; ?></dd>
                                </div>
                            </dl>


                            <div class="quote-cont">
                                <!-- <form action="<?php echo site_url('/machine-quote-request/'); ?>" method="post"> -->
                                <form action="<?php echo get_permalink(9677) . '?yourmachine=' . $machine->post->post_title; ?>"
                                      method="post">
                                    <input type="hidden" name="machine" value="<?php //echo $product->id; ?>">
                                    <input type="submit" class="btn btn-primary" value="Request a Quote"/>
                                </form>
                                <br>
                                <a href="https://app.dcrportal.com/oca/?vendorGUID=fec66ea1-c0c6-e311-90d0-005056a20000"
                                   target="_blank" class="btn btn-secondary">Apply for Financing </a>

                            </div>


                            <div class="product-detail__secondary-buttons">

                                <div class="col-xxs-12 hard--left email-print">
                                    <a href="mailto:?subject=CAT: <?php the_title(); ?>&body=<?php the_permalink(); ?>"
                                       class=" button button--primary button--block email">
                                        Share: <span class="fa fa-share"></span>
                                    </a>
                                    <a href="javascript:window.print()"
                                       class="button button--primary button--block hidden-xxs hidden-xs print">
                                        Print: <span class="fa fa-print"></span>
                                    </a>
                                    <?php if (!empty($product->documents)): ?>
                                        <?php $doc = array_shift($product->documents); ?>
                                        <a href="<?php echo $doc->src; ?>" class="product__brochure" target="_blank"
                                           rel="nofollow">Download PDF: <span class="fa fa-download"></span></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>

                <!-- <div class="row">
                <div class="col-xxs-12">
                    <ul class="link-bar clearfix">
                        <li><a class="btn btn-primary" href="<?php echo site_url(get_option('cat_financing_url', '#')); ?>">Get Financing Now</a></li>
                        <li><a class="btn btn-primary" href="<?php echo site_url(get_option('cat_demo_url', '#')); ?>">Schedule A Demo</a></li>
                        <li><a class="btn btn-primary" href="<?php echo site_url(get_option('cat_em_solutions_url', '#')); ?>">EM Solutions</a></li>
                        <li><a class="btn btn-primary" href="<?php echo site_url(get_option('cat_rent_url', '#')); ?>">Rent</a></li>
                    </ul>
                </div>
            </div> -->


        </div>
        </section>

        <section class="product__details tabs">
            <div class="">
                <div class="">
                    <div class="tabs__nav-wrapper clearfix">
                        <ul class="tabs__nav clearfix" id="tabs-nav-main">
                            <li class="tab-link active"><a href="#tab1">Specifications</a></li>
                            <?php if (!empty($machine->features)): ?>
                                <li class="tab-link "><a href="#tab2">Features</a></li>
                            <?php endif; ?>
                            <?php if (!empty($machine->condition)): ?>
                                <li class="tab-link"><a href="#tab3">Conditions</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="tabs__content">
                <div id="tab1" class="tab-content-main active">
                    <div class="specs-infos">
                        <dt>Contact</b></dt>
                        <dd data-english="<?php //echo isset($machine->contact) ? $machine->contact : 'N/A'; ?>"
                            data-metric=""><?php //echo isset($machine->contact) ? $machine->contact : 'N/A'; ?></dd>
                    </div>
                    <div class="specs-infos">
                        <dt>Unit Number</b></dt>
                        <dd data-english="<?php echo isset($machine->unit_number) ? $machine->unit_number : 'N/A'; ?>"
                            data-metric=""><?php echo isset($machine->unit_number) ? $machine->unit_number : 'N/A'; ?></dd>
                    </div>
                    <div class="specs-infos">
                        <dt>Model</b></dt>
                        <dd data-english="<?php echo isset($machine->model) ? $machine->model : 'N/A'; ?>"
                            data-metric=""><?php echo isset($machine->model) ? $machine->model : 'N/A'; ?></dd>
                    </div>
                    <div class="specs-infos">
                        <dt>Serial Number</b></dt>
                        <dd data-english="<?php echo isset($machine->serial_number) ? $machine->serial_number : 'N/A'; ?>"
                            data-metric=""><?php echo isset($machine->serial_number) ? $machine->serial_number : 'N/A'; ?></dd>
                    </div>
                    <div class="specs-infos">
                        <dt>Year</b></dt>
                        <dd data-english="<?php echo isset($machine->year) ? $machine->year : 'N/A'; ?>"
                            data-metric=""><?php echo isset($machine->year) ? $machine->year : 'N/A'; ?></dd>
                    </div>
                    <div class="specs-infos">
                        <dt>Hours</b></dt>
                        <dd data-english="<?php echo isset($machine->hours) ? $machine->hours : 'N/A'; ?>"
                            data-metric=""><?php echo isset($machine->hours) ? $machine->hours : 'N/A'; ?></dd>
                    </div>
                    <div class="specs-infos">
                        <dt>City</b></dt>
                        <dd data-english="<?php echo isset($machine->city) ? $machine->city : 'N/A'; ?>"
                            data-metric=""><?php echo isset($machine->city) ? $machine->city : 'N/A'; ?></dd>
                    </div>
                    <div class="specs-infos">
                        <dt>Postal Code</b></dt>
                        <dd data-english="<?php echo isset($machine->postal_code) ? $machine->postal_code : 'N/A'; ?>"
                            data-metric=""><?php echo isset($machine->postal_code) ? $machine->postal_code : 'N/A'; ?></dd>
                    </div>
                    <div class="specs-infos">
                        <dt>State</b></dt>
                        <dd data-english="<?php echo isset($machine->state) ? $machine->state : 'N/A'; ?>"
                            data-metric=""><?php echo isset($machine->state) ? $machine->state : 'N/A'; ?></dd>
                    </div>
                    <div class="specs-infos">
                        <dt>Country</b></dt>
                        <dd data-english="<?php echo isset($machine->country) ? $machine->country : 'N/A'; ?>"
                            data-metric=""><?php echo isset($machine->country) ? $machine->country : 'N/A'; ?></dd>
                    </div>
                    <div class="specs-infos">
                        <dt>Price</b></dt>
                        <dd data-english="<?php echo isset($machine->price) ? '$' . number_format($machine->price) : 'N/A'; ?>"
                            data-metric=""><?php echo isset($machine->price) ? '$' . number_format($machine->price) : 'N/A'; ?></dd>
                    </div>
                </div>
                <?php if (!empty($machine->features)): ?>
                    <div id="tab2" class="tab-content-main">
                        <ul class="specs specs--list">
                            <?php foreach ($machine->features as $feature): ?>
                                <li><?php echo $feature; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($machine->condition)): ?>
                    <div id="tab3" class="tab-content-main">
                        <section class="accordion">
                            <div class="accordion-wrapper">

                                <div class="accordion-box">
                                    <?php foreach ($machine->condition as $title => $values): ?>
                                        <?php if ($values): ?>
                                            <div class="accordion-item">
                                                <h3 class="accordion-title"><?php echo $title; ?></h3>
                                                <div class="accordion-content">
                                                    <dl class="clearfix">
                                                        <?php foreach ($values as $item => $condition): ?>
                                                            <div class="specs-infos">
                                                                <dt><?php echo $item; ?></dt>
                                                                <dd data-english="<?php echo $condition; ?>"
                                                                    data-metric=""><?php echo $condition; ?></dd>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </dl>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </section>
                    </div>
                <?php endif; ?>

            </div>
        </section>

        <div class="vtitem"></div>
        <?php
        //        dd($machine);
        $make = $machine->manufacturer->name ?? '';
        $model = $machine->model ?? '';
        $postal = $machine->postal_code ?? '';
        $dimensions = array(
            'lengthFt' => intval(get_field('length_ft')),
            'lengthIn' => intval(get_field('length_in')),
            'widthFt' => intval(get_field('width_ft')),
            'widthIn' => intval(get_field('width_in')),
            'heightFt' => intval(get_field('height_ft')),
            'heightIn' => intval(get_field('height_in'))
        );
        $custom = false;

        foreach ($dimensions as $val) {
            if (!empty($val)) {
                $custom = true;
                break;
            }
        }

        $dimensions = json_encode($dimensions);

        ?>
        <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/veritread/js/templates.js?150902"></script>
        <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/veritread/js/main.js?150902"></script>
        <script>
            var veritreadAppSettings = {
                    partnerId: 4,
                    env: 'production',
                    assetsDir: '/wp-content/themes/quinn/assets/veritread/',  // if not passed in, will default to 'veritread/'; sets path to dir with widget assets
                    embedCss: 'yes',
                    elementToUse: '.vtitem',
                    templates: new VTTemplates()
                },
                veritread = new VTApp(veritreadAppSettings);


            var config = {
                make: '<?php echo $make; ?>',
                originCountryISO: 'US',
                originPostal: '<?php echo $postal; ?>',
            };

            config.model = '<?php echo $model; ?>';

            <?php if( $custom ): ?>
            config.specs = <?php echo $dimensions; ?>;
            <?php endif; ?>

            // Create an estimator display and add it to an existing (unique) element on the page
            veritread.addItemToElement('.vtitem', config);
        </script>
        </article>
    </div>

    </div>

    <?php if (isset($machine->family->name)) : ?>
        <section class="related-equipment clearfix">
            <img class="bg-image" src="https://quinncompany.com/wp-content/themes/quinn/assets/img/about-texture.jpg"
                 class="img-responsive" alt="">
            <div class="container">
                <h2>Related Equipment</h2>
                <?php
                $related_family_title = $machine->family->name;
                if ($machine->family->name == "Machinery") {
                    $related_family_title = "Used Machines";
                }
                ?>
                <a class="btn btn-primary" href="<?php echo get_term_link($machine->family); ?>">View
                    all <?php echo $related_family_title; ?></a>
                <div class="flex-image-cards">
                    <?php echo fx_get_related_equipment(); //see helper functions for ref ?>

                </div>
            </div>
        </section>
    <?php endif;

    $afterContent = get_field('text_after_content');
    if ($afterContent): ?>
        <section class="wysiwyg section-margins wysiwyg-textured-background">
            <div class="container"><?= $afterContent ?></div>
        </section>
    <?php endif; ?>

<?php endwhile; endif; ?>

<?php get_footer();
