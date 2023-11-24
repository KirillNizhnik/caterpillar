<?php get_header(); ?>
<?php echo get_template_part('partials/masthead');


?>

<?php if (have_posts()): while (have_posts()): the_post(); ?>

    <?php
    $product = CAT()->product();
    $afterContent = get_field('text_after_content');
    $uri = $_SERVER['REQUEST_URI'];
    $newFormLink = false;
    $family_urls = ['/new-equipment/machines/backhoe-loaders/', '/new-equipment/machines/excavators/', '/new-equipment/machines/skid-steer-and-compact-track-loaders/', '/new-equipment/machines/compact-track-loaders/', '/new-equipment/machines/mini-excavators/', '/new-equipment/machines/skid-steer-loaders/', '/new-equipment/machines/medium-excavators/', '/new-equipment/machines/large-excavators/', '/new-equipment/machines/wheel-excavators/', '/new-equipment/machines/small-excavators/'];
    foreach ($family_urls as $url) {
        if (str_contains($uri, $url)) {
            $newFormLink = true;

            echo do_shortcode('[fx_modal id=20325]');
            wp_reset_postdata();

            echo do_shortcode('[fx_modal id=19864]');
            wp_reset_postdata();

            break;
        }
    }

    ?>
    <?php if (isset($product->status) && $product->status === 'not_available'): ?>
        <div class="not-avaliable-tag" style="color: red; text-align: center; padding-top: 20px">Model Discontinued,
            please see current model
        </div>
    <?php endif; ?>

    <div class="container cat-new-container">

        <div class="flexbox">

            <article class="page-article flexbox__item">
                <div class="row">
                    <div class="col-sm-6  col-md-5">
                        <?php cat_template('new/single/image-browser.php');
                        ?>
                    </div>

                    <?php //var_dump($product); ?>

                    <div class="col-sm-6 col-md-offset-1 col-md-6 ">

                        <div class="product__overview clearfix">
                            <?php the_content(); ?>
                            <?php //var_dump($product->attachments()); ?>
                            <?php if ($product->specs): ?>
                                <?php $specs = $product->specs(4); ?>
                                <table class="product__specs specs specs--table specs--table-small clearfix">
                                    <?php if (is_array($specs)): foreach ($specs as $spec): ?>
                                        <tr>
                                            <td>
                                                <dt><?php echo $spec->name; ?></dt>
                                                <dd data-english="<?php echo $spec->value_english . ' ' . $spec->unit_english; ?>"
                                                    data-metric="<?php echo $spec->value_metric . ' ' . $spec->unit_metric; ?>">
                                                    <?php echo $spec->value_english . ' ' . $spec->unit_english; ?>
                                                </dd>
                                            </td>
                                        </tr>
                                    <?php endforeach; endif ?>
                                </table>


                            <?php endif; ?>

                            <div class="product__actions">
                                <div class="quote-cont">
                                    <?php if ($newFormLink) { ?>
                                        <a class="fx-modal-open-button btn btn-primary" id="20325"
                                           data-product-title="<?php echo $product->post->post_title; ?>">Request a
                                            Quote</a>
                                    <?php } else { ?>
                                        <a href="<?php the_permalink(7122); ?>" class="btn btn-primary">Get a free
                                            quote</a>
                                    <?php } ?>
                                    <!-- <form action="<?php //echo get_permalink(9682) . '?yourmachine=' . $product->post->post_title; ?>" method="post">
                                    <input type="hidden" name="machine" value="<?php //echo $product->id; ?>">
                                    <input type="submit" class="btn btn-primary" value="Request a Quote" />
                                </form> -->
                                    <br>
                                    <br>
                                    <?php if (get_field('cat_buy_now_button')): ?>
                                        <a class="btn btn-primary"
                                           href="<?php echo get_field('cat_buy_now_button'); ?>">Buy Now</a>
                                    <?php endif; ?>
                                </div>


                                <div class="col-xxs-12 hard--left email-print">
                                    <a href="mailto:?subject=CAT: <?php the_title(); ?>&body=<?php the_permalink(); ?>"
                                       class="button button--primary button--block email">
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

                    </div>
                </div>

                <?php
                $hide_sections = get_field('cat_hide_sections');
                $hide_sections = empty($hide_sections) ? array() : $hide_sections;
                ?>

                <!-- start desktop -->
                <section class="product__details tabs fx-mobile-hide">
                    <div>
                        <div>
                            <div class="tabs__nav-wrapper clearfix">
                                <ul class="tabs__nav clearfix" id="tabs-nav-main">
                                    <?php if (!empty($product->specs)): ?>
                                        <li class="tab-link active"><a href="#tab1">Specifications</a></li>
                                    <?php endif; ?>
                                    <?php if (!empty($product->features)): ?>
                                        <li class="tab-link"><a href="#tab2">Benefits and Features</a></li>
                                    <?php endif; ?>
                                    <?php if (!empty($product->standard_equipment)): ?>
                                        <li class="tab-link"><a href="#tab3">Standard/Optional Features</a></li>
                                    <?php endif; ?>
                                    <?php if (!in_array("attachments", $hide_sections) && !$product->isAttachment()): ?>
                                        <?php if ($product->attachments()->have_posts()): ?>
                                            <li class="tab-link"><a href="#tab4">Attachments</a></li>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="tabs__content">
                        <div id="tab1" class="tab-content-main active">
                            <?php cat_template('new/single/specs'); ?>
                        </div>
                        <div id="tab2" class="tab-content-main">
                            <?php cat_template('new/single/features'); ?>
                        </div>
                        <div id="tab3" class="tab-content-main">
                            <?php cat_template('new/single/equipment'); ?>
                        </div>
                        <?php if ($product->attachments()->have_posts()): ?>
                            <div id="tab4" class="tab-content-main">
                                <?php cat_template('new/single/attachments'); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                </section>
                <!-- end desktop -->

                <div class="cta-section desktop--only">
                    <?php if ($newFormLink) { ?>
                        <a class="fx-modal-open-button btn btn-primary" id="19864"
                           data-product-title="<?php echo $product->post->post_title; ?>">Request a Quote</a>
                    <?php } else { ?>
                        <a href="<?php the_permalink($post = 7122); ?>" class="btn btn-primary">Get a free
                            quote</a>
                    <?php } ?>
                </div>

                <!-- start mobile -->
                <section class="product__details tabs fx-mobile-show">
                    <section class="accordion">
                        <div class="container">
                            <div class="accordion-wrapper">

                                <div class="accordion-box">


                                    <div class="accordion-item">
                                        <?php if (!empty($product->specs)): ?>
                                            <h2 class="accordion-title">Specifications</h2>
                                        <?php endif; ?>

                                        <div class="accordion-content">
                                            <?php cat_template('new/single/specs'); ?>

                                            <div class="cta-section">
                                                <?php if ($newFormLink) { ?>
                                                    <a class="fx-modal-open-button btn btn-primary" id="19864"
                                                       data-product-title="<?php echo $product->post->post_title; ?>">Request
                                                        a Quote</a>
                                                <?php } else { ?>
                                                    <a href="<?php the_permalink($post = 7122); ?>"
                                                       class="btn btn-primary">Get a free
                                                        quote</a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <?php if (!empty($product->features)): ?>
                                            <h2 class="accordion-title">Benefits and Features</h2>
                                        <?php endif; ?>

                                        <div class="accordion-content">
                                            <?php cat_template('new/single/features'); ?>

                                            <div class="cta-section">
                                                <?php if ($newFormLink) { ?>
                                                    <a class="fx-modal-open-button btn btn-primary" id="19864"
                                                       data-product-title="<?php echo $product->post->post_title; ?>">Request
                                                        a Quote</a>
                                                <?php } else { ?>
                                                    <a href="<?php the_permalink($post = 7122); ?>"
                                                       class="btn btn-primary">Get a free
                                                        quote</a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <?php if (!empty($product->standard_equipment)): ?>
                                            <h2 class="accordion-title">Standard/Optional Features</h2>
                                        <?php endif; ?>

                                        <div class="accordion-content">
                                            <?php cat_template('new/single/equipment'); ?>

                                            <div class="cta-section">
                                                <?php if ($newFormLink) { ?>
                                                    <a class="fx-modal-open-button btn btn-primary" id="19864"
                                                       data-product-title="<?php echo $product->post->post_title; ?>">Request
                                                        a Quote</a>
                                                <?php } else { ?>
                                                    <a href="<?php the_permalink($post = 7122); ?>"
                                                       class="btn btn-primary">Get a free
                                                        quote</a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if (!in_array("attachments", $hide_sections)): ?>
                                        <?php if ($product->attachments()->have_posts()): ?>
                                            <div class="accordion-item">

                                                <h2 class="accordion-title">Attachments</h2>


                                                <div class="accordion-content">
                                                    <?php cat_template('new/single/attachments'); ?>

                                                    <div class="cta-section">
                                                        <?php if ($newFormLink) { ?>
                                                            <a class="fx-modal-open-button btn btn-primary" id="19864"
                                                               data-product-title="<?php echo $product->post->post_title; ?>">Request
                                                                a Quote</a>
                                                        <?php } else { ?>
                                                            <a href="<?php the_permalink($post = 7122); ?>"
                                                               class="btn btn-primary">Get a free
                                                                quote</a>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </section>

                </section>
                <!-- end mobile -->


                <!--                 -->
                <?php //echo cat_related_shortcode( array('product_id' => $product->id, 'posts_per_page' => 3) )?><!-- -->

            </article>
        </div>

    </div>


    <?php
    $similar = get_field('similar_products', $product->id);
    $similarProducts = [];
    if (isset($product->status) && $product->status === 'not_available' && !empty($similar)) {
        foreach ($similar as $item) {
            $product = CAT()->product($item);
            if (isset($product->status) && $product->status === 'available') {
                $similarProducts[] = $product;
            }
        }
    } ?>

    <?php if (count($similarProducts) > 0): ?>
        <section class="related-equipment clearfix">
            <img class="bg-image"
                 src="https://quinn.webpagefxstage.com/wp-content/themes/quinn/assets/img/about-texture.jpg"
                 class="img-responsive" alt="">
            <div class="container">
                <div class="h2">Similar Products</div>
                <div class="flex-image-cards">
                    <?php foreach ($similarProducts as $item): ?>
                        <article class="product-item-block">
                            <div class="product-card-details-block">
                                <div class="product-card">
                                    <div class="product-card-detail-info">
                                        <a href="<?php the_permalink($product->id); ?>" class="product-item-card">
                                            <div class="product-item-card__thumb">
                                                <?php //echo fx_get_image_tag( 417 );
                                                $image = reset($product->images);
                                                if (get_field('is_image_blurry', $product->id) == 'yes') {
                                                    $image = 7339;
                                                }
                                                ?>
                                                <?php echo cat_sized_image($image, array(436, 276), array('class' => 'img-responsive')); ?>
                                                <span class="btn btn-tertiary family-btn">View Details</span>
                                            </div>
                                            <h4 class="family-name"><?php
                                                echo get_the_title($product->id);

                                                ?></h4>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

    <?php endif; ?>

    <?php if ($afterContent): ?>
        <section class="wysiwyg section-margins wysiwyg-textured-background">
            <div class="container"><?= $afterContent ?></div>
        </section>
    <?php endif; ?>

<?php endwhile;
endif; ?>


<?php get_footer();
