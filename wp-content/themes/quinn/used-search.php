<?php /*Template Name: Used Equipment Search */ ?>

<?php get_header();


  ?>
<main class="page-body" id="page-body">

    <section class="page-content product-search-page">
    <div class="container">
        <div class="wrapper">

            <div class="row">
                <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 col-xxs-12 product-filter-list">


                    <div class="cat-taxonomy-page">
                        <div class="toolbar clearfix">
                            <h5 class="toolbar-view-title">Select View:</h5>
                            <div class="toolbar-view">

                                <!--<li class="view-btn active js-view" data-view="grid">
                                    <span class="icon-block-view">BLOCK VIEW</span>
                                </li>
                                <li class="view-btn js-view" data-view="list">
                                    <span class="icon-list-view">LIST VIEW</span>
                                </li> -->
                                <?php
                                $template = CAT()->session['template'];
                            ?>
                                <button class="template-type <?php if( empty($template) || $template == 'grid') { echo 'active'; } ?> js-template-type" data-type="grid">Grid</button>
                                <button class="template-type <?php if($template == 'list') { echo 'active'; } ?> js-template-type" data-type="list">List</button>


                            </div>
                            <div class="product-filter">
                                <!-- <label class="bg--yellow" for="product-filter__dropdown">Sort By:</label> -->

                                <?php echo do_shortcode('[facetwp sort="true"]');

                                ?>
                            </div>
                        </div>
                         <b>Items Per Page</b><br>
                                     <?php        echo do_shortcode('[facetwp facet="per_page"]'); ?> 

                            <div class="">

                                <div class="facetwp-template">
                                   <?php switch( CAT()->session['template'] )
                                                {
                                                    case 'list':
                                                        ?>
                                                        <div class="row listview-title">
                                                            <div class="col-lg-6 col-md-5 col-sm-5 col-xs-6 col-xxs-11">Product Name</div>
                                                            <!-- <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 col-xxs-6">Year</div> -->
                                                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 col-xxs-6">Manufacturer</div>
                                                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 col-xxs-6">Model</div>
                                                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 col-xxs-6">Hours</div>
                                                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-1 col-xxs-6">Price</div>
                                                        </div>
                                                        <?php
                                                        break;
                                                    default:
                                                    case 'grid':
                                                        break;
                                                } ?>
                              <div class="flex-image-cards machine-cards">

                                <?php


                                        $args = array(
                                            'post_type' => 'used-equipment',
                                            'posts_per_page' => -1
                                        );
                                        $query_fix = new WP_Query( $args );

                                        if ( $query_fix->have_posts() ) {

                                            ?><br><br> <?php
                                            while ( $query_fix->have_posts() ) {
                                                $query_fix->the_post();
                                                $machine = CAT()->product(get_the_id());
                                                //cat_template('used/loop/content-single', array('machine' => $machine));
                                                switch( CAT()->session['template'] )
                                                {
                                                    case 'list':
                                                        ?> <?php
                                                        cat_template('used/loop/content-list', array('machine' => $machine));
                                                        break;

                                                    default:
                                                    case 'grid':
                                                        cat_template('used/loop/content-single', array('machine' => $machine));
                                                        break;
                                                }
                                            }

                                        } else {
                                            echo '<p><b>Sorry, nothing matched your search.</b></p>';
                                        }


                                ?>
                                </div>
                            </div>
                                    <?php echo do_shortcode('[facetwp facet="pager_"]');  ?>
                            </div> <!-- row end -->




                    </div>
                </div> <!-- col end -->

                <!--<div class="col-lg-3 col-md-3 hidden-sm hidden-xs hidden-xxs">-->
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 col-xxs-12">
                    <div class="sidebar">
                        <?php cat_template('used/sidebar-filters'); ?>
                    </div>
                </div> <!-- col end -->

            </div> <!-- row end -->

        </div>
    </div>
    </section>

    <article class="post">

                             <?php the_content(); ?>


                        </article>

</main>


<?php

//custom logic for the sort by


get_footer();
