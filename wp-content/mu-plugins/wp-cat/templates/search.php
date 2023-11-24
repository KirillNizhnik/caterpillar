<?php 
/*Template Name: Used Equipment Search */
// CAT USED SEARCH PAGE 
/**
 * Include assets
 * Contains logic for enqueuing styles and scripts
 */
//require_once get_template_directory() . '/inc/theme/assets.php';


?>
<main class="page-body" id="page-body">

    <section class="masthead masthead--image" id="masthead">
        <img src="<?php //echo get_template_directory_uri(); ?>/assets/img/banner-about.jpg" class="img-responsive" alt="" />
        <div class="wrapper"><h1 class="pageTitle">Search</h1></div>
    </section>

    <section class="page-content">

        <div class="wrapper">

            <?php if ( function_exists('yoast_breadcrumb') ) {  yoast_breadcrumb('<div class="breadcrumbs">','</div>'); }?>

            <div class="row">
                <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 col-xxs-12">

                    <?php //echo category_description( $cat_family->id ); ?>

                    <div class="cat-taxonomy-page">
                        <div class="toolbar clearfix">
                            <ul class="toolbar-view pull-left">
                                <li class="toolbar-view-title">Select View</li>
                                <li class="view-btn active js-view" data-view="grid">
                                    <span class="icon-block-view"></span>
                                </li>
                                <li class="view-btn js-view" data-view="list">
                                    <span class="icon-list-view"></span>
                                </li>
                            </ul>
                            <div class="product-filter pull-right">
                                <label class="bg--yellow" for="product-filter__dropdown">Sort By:</label>
                                <select name="product-filter__dropdown" id="product-filter__dropdown" class="product-filter__dropdown bg--black">
                                    <option value="price - DESC"><strong class="color--yellow">Price:</strong> Highest to Lowest</option>
                                    <option value="price - ASC"><strong class="color--yellow">Price:</strong> Lowest to Highest</option>
                                    <option value="year - DESC"><strong class="color--yellow">Year:</strong> Newest to Oldest</option>
                                    <option value="year - ASC"><strong class="color--yellow">Year:</strong> Oldest to Newest</option>
                                    <option value="hours - DESC"><strong class="color--yellow">Hours:</strong> Highest to Lowest</option>
                                    <option value="hours - ASC"><strong class="color--yellow">Hours:</strong> Lowest to Highest</option>
                                </select>
                            </div>
                        </div>

                        <div class="js-equipment-view" id="search-inject">
                            <div class="row">
                                <?php
                                    $query_fix = null;
                                    if ( ! isset( $_POST[ 'action' ] ) || $_POST[ 'action' ] !== 'equipment_search_post' ) {
                                        $args = array(
                                            'post_type' => 'cat_used_machine',
                                            'posts_per_page' => -1
                                        );
                                        $query_fix = new WP_Query( $args );
                                    }
                                    $js_machines = array();
                                    if ( $query_fix ) {
                                        if ( $query_fix->have_posts() ) {
                                            while ( $query_fix->have_posts() ) {
                                                $query_fix->the_post();
                                                $machine = CAT()->product(get_the_id());
                                                cat_template('used/loop/content-single', array('machine' => $machine));
                                                $js_machines[] = cat_js_object($machine);
                                            }
                                        } else {
                                            echo '<p><b>Sorry, nothing matched your search.</b></p>';
                                        }
                                    } else {
                                        if ( have_posts() ) {
                                            while ( have_posts() ) {
                                                the_post();
                                                $machine = CAT()->product(get_the_id());
                                                cat_template('used/loop/content-single', array('machine' => $machine));
                                                $js_machines[] = cat_js_object($machine);
                                            }
                                        } else {
                                            echo '<p><b>Sorry, nothing matched your search.</b></p>';
                                        }
                                    }
                                ?>

                            </div> <!-- row end -->
                        </div>
                        <script>
                            var Machines = <?php echo json_encode($js_machines); ?>;
                        </script>
                        <?php cat_template('used/loop/content-js'); ?>


                    </div>
                </div> <!-- col end -->

                <!--<div class="col-lg-3 col-md-3 hidden-sm hidden-xs hidden-xxs">-->
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 col-xxs-12">

                    <aside class="page-sidebar">
                        <?php get_sidebar(); ?>
                    </aside>

                </div> <!-- col end -->

            </div> <!-- row end -->

        </div>

    </section>

</main>
