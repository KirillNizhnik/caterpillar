<?php get_header(); ?>



<?php
global $wp_query;
$new_family = CAT()->family();

$uri = $_SERVER['REQUEST_URI'];
$newFormLink = false;
$family_urls = ['/new-equipment/machines/backhoe-loaders/', '/new-equipment/machines/excavators/', '/new-equipment/machines/skid-steer-and-compact-track-loaders/', '/new-equipment/machines/compact-track-loaders/', '/new-equipment/machines/mini-excavators/', '/new-equipment/machines/skid-steer-loaders/', '/new-equipment/machines/medium-excavators/', '/new-equipment/machines/large-excavators/', '/new-equipment/machines/wheel-excavators/', '/new-equipment/machines/small-excavators/'];
foreach ($family_urls as $url) {
    if (str_contains($uri, $url)) {
        $newFormLink = true;
        echo do_shortcode('[fx_modal id=20325]');
        wp_reset_postdata();
        break;
    }
}

?>

<section class="masthead-inner">
    <div class="masthead-inner-texture-image">
        <!-- <img src="../wp-content/themes/quinn/assets/img/masthead-inner-texture-image.jpg" class="img-responsive" alt=""> -->
        <?php echo fx_get_image_tag(404); ?>
    </div>
    <div class="masthead-inner-overlay">
        <div class="container">
            <h1><?php echo $new_family->name; ?></h1>
            <?php
            if (function_exists('yoast_breadcrumb')) {
                yoast_breadcrumb('<div class="breadcrumbs hidden-sm-down">', '</div>');
            }

            $queried_object = get_queried_object();
            $taxonomy = $queried_object->taxonomy;
            $term_id = $queried_object->term_id;
            $parent = term_is_ancestor_of(648, $queried_object->term_id, $taxonomy);
            ?>
        </div>
    </div>
</section>
<!--  --><?php //var_dump($new_family ); ?><!-- -->
<div class="flexbox <?php the_field('cover_or_contained_images', $taxonomy . '_' . $term_id); ?>">

    <div class="container flexbox__item page-article ">

        <div class="row">
            <?php if ($new_family->parent === 0 && $new_family->slug !== 'power-systems'): ?>
                <section class="intro-text">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="intro-text-image">
                                    <div class="intro-text-image-clippy">
                                        <?php
                                        if ($new_family->header !== false) {
                                            $thumbnail = $new_family->header->src;
                                        } else {
                                            $thumbnail_raw = $new_family->thumbnail($new_family->term_id, array(227, 220));
                                            $thumbnail = $thumbnail_raw->src;
                                        }
                                        ?>
                                        <img src="<?php echo $thumbnail; ?>" itemprop="image" alt=""/>
                                    </div>
                                    <div class="intro-text-image-angle">
                                        <?php echo fx_get_image_tag(432); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <div class="intro-text-info">
                                    <h2 hidden><?php echo $new_family->name; ?></h2>
                                    <p><?php echo $new_family->description; ?></p>
                                    <?php echo $new_family->before_content; ?>
                                    <?php if ($newFormLink) {
                                        echo do_shortcode('[fx_modal id=20325]');
                                        wp_reset_postdata(); ?>
                                        <a class="fx-modal-open-button btn btn-primary" id="20325"
                                           data-product-title="<?php echo $new_family->name; ?>">Get a free quote</a>
                                    <?php } else { ?>
                                        <a href="<?php echo get_permalink($post = 7122) . "?yourmachine=" . urlencode($new_family->name); ?>"
                                           class="btn btn-primary">Get a free
                                            quote</a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </section>
            <?php else: ?>
                <section class="intro-text">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="intro-text-image">
                                    <div class="intro-text-image-clippy">
                                        <?php if (!empty($new_family->header)) :
                                            echo cat_sized_image(reset($new_family->header), array(550, 340));

                                        elseif (!empty($new_family->images)):
                                            echo cat_sized_image(reset($new_family->images), array(550, 340));

                                        else:
                                            echo fx_get_image_tag(432);
                                        endif; ?>
                                    </div>
                                    <div class="intro-text-image-angle">
                                        <?php echo fx_get_image_tag(432); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <div class="intro-text-info">
                                    <h2><?php echo $new_family->name; ?></h2>
                                    <p><?php echo $new_family->description; ?></p>

                                    <?php echo $new_family->before_content; ?>
                                    <?php if ($newFormLink) {
                                        echo do_shortcode('[fx_modal id=20325]');
                                        wp_reset_postdata(); ?>
                                        <a class="fx-modal-open-button btn btn-primary" id="20325"
                                           data-product-title="<?php echo $new_family->name; ?>">Get a free
                                            quote</a>
                                    <?php } else { ?>
                                        <a href="<?php echo get_permalink($post = 7122) . "?yourmachine=" . urlencode($new_family->name); ?>"
                                           class="btn btn-primary">Get a free
                                            quote</a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <section class="machine-cards">
                <div class="container">
                    <?php get_template_part('view-machines-filter'); ?>


                    <div name="family" id="family" class="">

                        <?php if (get_field('show_subfamilies', get_queried_object()) == 'OFF' && get_field('add_filters', get_queried_object()) == 'ON') : ?>
                            <br>
                            <br>
                            <h3>Select type to filter results below</h3>
                            <?php
                            if ($taxonomy == "family") {
                                echo do_shortcode('[facetwp facet="new_familes_ezuse"]');
                            } elseif ($taxonomy == "cat_new_power_family") {
                                echo do_shortcode('[facetwp facet="power_families"]');

                            } else {
                                //do-nothing
                            }
                            ?>

                        <?php endif;

                        ?>
                    </div>


                    <div class="flex-image-cards">

                        <?php
                        $subfamilies = get_terms($new_family->wp_term->taxonomy, array('parent' => $new_family->id, 'hide_empty' => true));

                        ?>

                        <?php if (count($subfamilies) > 0 && $new_family->slug !== 'backhoe-loaders' && get_field('show_subfamilies', get_queried_object()) == 'ON'): ?>


                            <?php


                            $child_families = get_terms($new_family->wp_term->taxonomy, array('parent' => $new_family->id, 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => true));

                            foreach ($child_families as $family) {

                                cat_template('new/loop/content-family', array('family' => $family));
                            }


                            ///ADDITION
                            if (have_rows('families_selection', get_queried_object())) :
                                while (have_rows('families_selection', get_queried_object())) : the_row();
                                    if (get_row_layout() == 'families_or_pages_selection') :

                                        $industry_families = array();
                                        if (get_sub_field('new_machine_families')) {
                                            $industry_families[] = get_sub_field('new_machine_families');
                                        }
                                        if (get_sub_field('new_attachment_families')) {
                                            $industry_families[] = get_sub_field('new_attachment_families');
                                        }
                                        if (get_sub_field('power_systems')) {
                                            $industry_families[] = get_sub_field('power_systems');
                                        }
                                        if (get_sub_field('site_support_products')) {
                                            $industry_families[] = get_sub_field('site_support_products');
                                        }
                                        if (get_sub_field('home_and_outdoor_power')) {
                                            $industry_families[] = get_sub_field('home_and_outdoor_power');
                                        }

                                        ?>

                                        <?php
                                        //tax loopthrough
                                        foreach ($industry_families as $families) {
                                            foreach ($families as $family) {
                                                //var_dump($family);
                                                cat_template('new/loop/content-family', array('family' => $family));

                                            }

                                        }

                                        //pages loopthrough
                                        if (have_rows('pages')) :
                                            while (have_rows('pages')) : the_row();
                                                if (get_sub_field('individual_page')) {
                                                    $family = get_sub_field('individual_page');
                                                    //var_dump(get_the_post_thumbnail_url($family));
                                                    cat_template('new/loop/content-page', array('family' => $family));
                                                }

                                            endwhile;
                                        endif;
                                    endif;
                                endwhile;
                            endif;


                            ?>

                        <?php else: ?>

                            <?php if (have_posts()): while (have_posts()): the_post(); ?>

                                <?php cat_template('new/loop/content-single'); ?>

                            <?php endwhile;
                                echo paginate_links();
                            else: ?>
                                <?php

                                $queried_object = get_queried_object();
                                $taxonomy = $queried_object->taxonomy;
                                $term_id = $new_family->term_id;

                                $custom_link = get_field('no_results_custom_link', $taxonomy . '_' . $term_id);
                                //var_dump($custom_link);
                                if (!empty($custom_link)): ?>
                                    <p><a href="<?php echo $custom_link; ?>">
                                            Click here to view all <?php echo $new_family->name; ?> from Cat.
                                        </a></p>
                                <?php else: ?>
                                    <p><b>Sorry, there aren't currently any listings for this family.</b></p>
                                <?php endif; ?>


                            <?php endif; ?>


                        <?php endif;

                        ?>
                    </div>
                </div>
            </section>
        </div>

    </div>
</div>

<?php //if (!empty($new_family->after_content)): ?>
<!--<section class="wysiwyg section-margins wysiwyg-textured-background"> -->
<section class="wysiwyg section-margins wysiwyg-textured-background">
    <div class="container">
        <?php if ($newFormLink) { ?>
            <a class="after-equipment-button fx-modal-open-button btn btn-primary" id="20325"
               data-product-title="<?php echo $new_family->name; ?>">Request a Quote</a>
        <?php } else { ?>
            <a class="after-equipment-button btn btn-secondary"
               href="<?php echo get_permalink(7122) . "?yourmachine=" . urlencode($new_family->name); ?>">Request a
                Quote</a>
        <?php } ?>
        <div class="wysiwyg-content">
            <?php echo $new_family->after_content; ?>
        </div>
    </div>
</section>
<?php // endif; ?>
<section class="home-cta">
    <div class="home-cta-background">
        <?php echo fx_get_image_tag(461); ?>
    </div>
    <div class="home-cta-overlay">
        <div class="container">
            <div class="home-cta-wrapper">
                <div class="row">
                    <div class="col-md-5">
                        <div class="home-cta-content">
                            <h4>Find a Quinn location near you</h4>
                            <p>Our machinery and equipment company serves central & southern California, with customized
                                solutions that drive progress. Contact the Quinn Company division you need for a free
                                quote.</p>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="home-cta-find-location">
                            <h5>FIND A LOCATION</h5>

                            <?php echo do_shortcode("[search-by-zip]"); ?>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<?php get_footer(); ?>
