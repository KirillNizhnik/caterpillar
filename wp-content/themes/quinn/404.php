<?php get_header(); ?>

<?php get_template_part('partials/masthead'); ?>


<!-- Use https://blendedwaxes.com/404 as an example -->

<!-- Add image buttons below for top ~4 pages - one should be the homepage, PM to specify the others in specs -->
<section class="imgbtns-404">
    <div class="container">
        <h3>Explore one of these pages instead:</h3>
        <div class="row">
            <div class="col-xxs-12 col-sm-6 col-md-3">
                <div class="image-button-box">
                    <a href="<?php the_permalink(27); ?>">
                        <div class="image-button-image">
                            <?php echo fx_get_image_tag( 11051, ['img-responsive'], true, 'full'); ?>
                        </div>
                        <div class="image-button-content">
                            <h4>Company</h4>
                            <span class="btn btn-tertiary">Learn more</span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-xxs-12 col-sm-6 col-md-3">
                <div class="image-button-box">
                    <a href="<?php the_permalink(22); ?>">
                        <div class="image-button-image">
                            <?php echo fx_get_image_tag( 11049, ['img-responsive'], true, 'full'); ?>
                        </div>
                        <div class="image-button-content">
                            <h4>Services</h4>
                            <span class="btn btn-tertiary">Learn more</span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-xxs-12 col-sm-6 col-md-3">
                <div class="image-button-box">
                    <a href="<?php the_permalink(708); ?>">
                        <div class="image-button-image">
                            <?php echo fx_get_image_tag( 11695, ['img-responsive'], true, 'full'); ?>
                        </div>
                        <div class="image-button-content">
                            <h4>Deals & Specials</h4>
                            <span class="btn btn-tertiary">Learn more</span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-xxs-12 col-sm-6 col-md-3">
                <div class="image-button-box">
                    <a href="<?php the_permalink(626); ?>">
                        <div class="image-button-image">
                            <?php echo fx_get_image_tag( 359, ['img-responsive'], true, 'full'); ?>
                        </div>
                        <div class="image-button-content">
                            <h4>Blog</h4>
                            <span class="btn btn-tertiary">Learn more</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="links-404">
    <div class="container">
        <div class="row">
            <div class="col-xxs-12 col-md-6">
                <div class="search-404">
                    <h4>Or, try searching our site:</h4>
                    <?php get_search_form(); ?>
                </div>
            </div>
            <div class="col-xxs-12 col-md-6">
                <div class="contact-404">
                    <h4>Still can't find what you're looking for?</h4>
                    <a href="<?php the_permalink(575); ?>" class="btn btn-secondary">Contact Us Today!</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer();
