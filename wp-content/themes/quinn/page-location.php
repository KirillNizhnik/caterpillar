<?php /*Template Name: Location */ ?>


<main class="page-body" id="page-body">

    <section class="page-content">

        <div class="wrapper">

            <?php if ( function_exists('yoast_breadcrumb') ) {  yoast_breadcrumb('<div class="breadcrumbs">','</div>'); }?>

                <h1 class="page-title"><?php the_title(); ?></h1>

            	<div class="row">

                	<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 col-xxs-12" >

                    	<div class="closest-location">
                        	<div class="row">

                            	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-5 col-xxs-12"> <h3>Find a Location: </h3> </div>

                                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-7 col-xxs-12">
									<?php
									    $zipcode  = ( isset($_POST['zipcode']) ) ? intval($_POST['zipcode']) : '';
									    $distance = ( isset($_POST['distance']) ) ? intval($_POST['distance']) : '';
									?>


    								<div class="wpcm_search_by_zip" id="wpcm_search_by_zip-1">
                                        <form id="closest-location-form" class="js-widget-search-by-zip form-box closest-location--form">
                                            <div class="row">
                                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 col-xxs-6">
                                                    <input type="text" name="zipcode"
                                                        id="sbz-zipcode"
                                                        class="loc-zipcode js-sbz-zipcode"
                                                        value="<?php echo $zipcode; ?>"
                                                        placeholder="Enter Zip Code"
                                                    />
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 col-xxs-6">
                                                    <input type="submit" name="submit" class="submit loc-submit" id="loc-submit" value="Search" />
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                </div>

                            </div>
                        </div><!-- closest location end -->

                        <article class="post">

                             <?php the_content(); ?>


                        </article>

                    </div> <!-- col end -->



                    <div class="col-lg-3 col-md-3 hidden-sm hidden-xs hidden-xxs" >

                    	<aside class="page-sidebar">
                        	<div class="sidebar clearfix">

                            	<?php dynamic_sidebar('location-sidebar'); ?>

                            </div>
                        </aside>

                    </div> <!-- col end -->

             	</div> <!-- row end -->


				<section class="locations">

                    <h2>All of Our Locations</h2>

                    <?php echo do_shortcode('[locations]'); ?>

                </section>


        	</div><!-- wrapper end -->

        </section>

        <div class="location-bottom-widgets">
            <div class="sidebar clearfix">

                <?php dynamic_sidebar('location-sidebar'); ?>

            </div>
        </div>


    </main>
