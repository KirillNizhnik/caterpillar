<?php /* Template Name: Landing Page */ ?>

<?php require 'header-landing.php'; ?>

<section class="content-section">
    <div class="container">
        <div class="row">

        <?php if ( have_posts() ) : while( have_posts() ) : the_post(); ?>

            <div class="col-sm-8 col-md-7 col-lg-6">
                <div class="white-bk">

                    <?php the_content(); ?>

                </div>
            </div>

        <?php endwhile; endif; ?>

        </div>
    </div>
</section>

<section class="site-form">
    <div class="container">
        <div class="row">
            <div class="col-sm-8">
                <div class="black-bk soft">

                    <h3>Contact Us Today</h3>

                    <?php /* echo do_shortcode('[--YOUR CF7 SHORTCODE HERE--]') */ ?>

                </div>
            </div>
        </div>
    </div>
</section>

<?php require 'footer-landing.php'; ?>
