<?php get_header(); ?>



<?php
global $wp_query;
$cat_family = CAT()->family();


$queried_object = get_queried_object();
$taxonomy = $queried_object->taxonomy;
$term_id = $queried_object->term_id;
?>

<section
        class="masthead-inner <?php if (get_field('show_ag_header', $taxonomy . '_' . $term_id) == 'yes'): ?> ag-header <?php endif; ?>">
    <div class="masthead-inner-texture-image">
        <?php
        // var_dump($queried_object);


        if (get_field('show_ag_header', $taxonomy . '_' . $term_id) == 'yes') { //check for tax terms
            $image_id = get_field('ag_header_background', $taxonomy . '_' . $term_id);

            if ($image_id):

                echo fx_get_image_tag($image_id);

            endif;
        } else {
            echo fx_get_image_tag(404);
        }
        ?>
    </div>
    <div class="masthead-inner-overlay">
        <div class="container">
            <h1><?php if ($cat_family->name == "Machinery") {
                    echo "Used Machinery";
                } else {
                    echo $cat_family->name;
                } ?></h1>
            <?php
            if (function_exists('yoast_breadcrumb')) {
                yoast_breadcrumb('<div class="breadcrumbs hidden-sm-down">', '</div>');
            };
            ?>
        </div>
    </div>
</section>

<div class="flexbox <?php the_field('cover_or_contained_images', $taxonomy . '_' . $term_id); ?>">

    <div class="container flexbox__item page-article ">


        <?php

        $image_id = "";
        $url_string = $cat_family->thumbnail->src;
        $url_string_fixed = str_replace('-150x150', "", $url_string);
        if (attachment_url_to_postid($url_string_fixed) && $url_string !== "/wp-content/mu-plugins/wp-cat/assets/images/default.jpg") {
            $image_id = attachment_url_to_postid($url_string_fixed);
        }
        ?>

        <div class="row">
            <section class="intro-text">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="intro-text-image">
                                <div class="intro-text-image-clippy">
                                    <?php
                                    if (isset($image_id) && $image_id !== "") {
                                        echo fx_get_image_tag($image_id);
                                        //echo "hit";
                                    } else {
                                        if (is_array($cat_family->header) && cat_sized_image(reset($cat_family->header), array(312, 233))) :
                                            echo cat_sized_image(reset($cat_family->header), array());
                                        else:
//                                                         echo cat_sized_image( reset($machine->images), array() );
                                            echo fx_get_image_tag(432);
                                        endif;
                                    }
                                    ?>

                                </div>
                                <div class="intro-text-image-angle">
                                    <?php echo fx_get_image_tag(432); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="intro-text-info">
                                <!-- <h2><?php //echo $cat_family->name; ?></h2> -->
                                <?php echo $cat_family->description; //var_dump($cat_family); ?>

                                <a href="<?php the_permalink($post = 7122); ?>?yourmachine=1994%20Caterpillar%203512"
                                   class="btn btn-primary">request a quote for a used machine</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
    <?php if ($cat_family->before_content): ?>
        <section class="wysiwyg wysiwyg-grey-background">
            <div class="container">
                <div class="wysiwyg-content">
                    <?php echo $cat_family->before_content; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <div class="flexbox <?php the_field('cover_or_contained_images', $taxonomy . '_' . $term_id); ?>">

        <div class="container flexbox__item page-article ">
            <div class="row">

                <section class="machine-cards">
                    <div class="container">

                        <?php echo cat_template('used/partial-page-filters'); ?>

                        <?php get_template_part('view-machines-filter'); ?>

                        <div class="flex-image-cards">

                            <?php if ($cat_family->parent === 0 && $cat_family->slug !== 'power-systems' && $cat_family->slug !== 'used-rentals'): ?>

                                <?php
                                $child_families = get_terms('used-family', array(
                                        'parent' => $cat_family->id,
                                        'orderby' => 'name',
                                        'order' => 'ASC',
                                        'hide_empty' => false
                                    )
                                );

                                foreach ($child_families as $family) {
                                    if ($family->count == 0) {
                                        continue;
                                    }
                                    cat_template('used/loop/content-family', array('family' => $family));
                                }
                                ?>
                                <?php $should_hide_cta_one = false; ?>
                            <?php else: ?>

                                <?php if (have_posts()): while (have_posts()): the_post(); ?>
                                    <?php cat_template('used/loop/content-single'); ?>
                                    <?php $should_hide_cta_one = false; ?>
                                <?php endwhile; else: ?>
                                    <p><b>Sorry, there aren't currently any listings for this family.</b></p>
                                    <?php $should_hide_cta_one = true; ?>
                                <?php endif; ?>

                            <?php endif; ?>

                        </div>
                    </div>
                </section>
            </div>

        </div>
    </div>

    <?php //if( $cat_family->parent === 0 && $cat_family->slug !== 'power-systems' ): ?>
    <!-- <section class="wysiwyg wysiwyg-grey-background">
            <div class="container">
                <div class="wysiwyg-content">
                    <?php //echo $cat_family->after_content; ?>
                </div>
            </div>
        </div>
    </section>
    <section class="inner-cta">
        <div class="inner-cta-background">
            <? php// echo fx_get_image_tag( 340 ); ?>
        </div>


    </section>-->
    <?php //else: ?>



    <?php //endif; ?>

    <section class="wysiwyg wysiwyg-grey-background">
        <div class="container">
            <div class="wysiwyg-content">
                <?php echo $cat_family->after_content; ?>
            </div>
        </div>
</div>
</section>
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
                            <!-- <form>
                                <div class="home-cta-location-search">
                                    <input type="text" name="" placeholder="ENTER YOUR ZIPCODE">
                                    <button class="btn btn-secondary">Find a location</button>
                                </div>
                            </form> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<?php get_footer(); ?>
