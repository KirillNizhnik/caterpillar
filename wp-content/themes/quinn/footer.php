<!-- footer -->
<?php $home_url = get_home_url(); ?>
<footer class="page-footer" id="page-footer">
    <div class="container">
        <div class="footer-top clearfix">
            <div class="footer-logo-address">
                <div class="footer-logo">
                    <a href="<?php echo esc_url($home_url); ?>">
                        <?php if (get_field('show_ag_header', get_queried_object()) == 'yes'): ?>
                            <?php echo fx_get_image_tag(15643); ?>
                        <?php else: ?>
                            <?php echo fx_get_image_tag(get_field('footer_logo', 'option'), [''], true, 'full'); ?>
                        <?php endif; ?>
                    </a>
                </div>
                <?php if (is_page(18341)): ?>
                    <div class="footer-address">
                        <a href="<?php the_field('footer_location', 'option'); ?>"><span
                                    class="icon-location-on"></span>VIEW OUR LOCATIONS</a>
                        <a href="951-823-8556">
                            <span class="icon-phone-alt"></span><strong>CALL US!</strong> 951.823.8556
                        </a>

                    </div>

                <?php else: ?>
                    <div class="footer-address">
                        <a href="<?php the_field('footer_location', 'option'); ?>"><span
                                    class="icon-location-on"></span>VIEW OUR LOCATIONS</a>
                        <?php $link = get_field('footer_phone', 'option'); ?>
                        <?php if ($link): ?>
                            <a href="<?php echo $link['url']; ?>">
                                <span class="icon-phone-alt"></span><strong>CALL
                                    US!</strong> <?php echo $link['title']; ?>
                            </a>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>
                <!-- <div class="language-button hidden-md-up push-bottom">
                    <?php // echo do_shortcode('[weglot_switcher]'); ?>
                </div> -->
                <div class="social-media hidden-xs-down hidden-md-up">
                    <a href="<?php the_field('facebook', 'option'); ?>"><span class="fa fa-facebook"></span></a>
                    <a href="<?php the_field('linkedin', 'option'); ?>"><span class="fa fa-linkedin"></span></a>
                    <a href="<?php the_field('twitter', 'option'); ?>"><span class="fa fa-twitter"></span></a>
                    <a href="<?php the_field('instagram', 'option'); ?>"><span class="fa fa-instagram"></span></a>
                </div>
                <div class="backToTop hidden-sm-up">
                    <a href="javascript:void(0);" class="btn btn-primary back-to-top">Back to top</a>
                </div>
            </div>
            <div class="footer-short-links clearfix hidden-sm-down">
                <div class="footer-short-links-box">
                    <h4>QUICK LINKS</h4>
                    <div class="footer-short-links-items">
                        <?php while (have_rows('quick_links_list', 'option')): the_row(); ?>
                            <?php $link = get_sub_field('quick_links_item', 'option'); ?>
                            <?php if ($link): ?><a
                                href="<?php echo $link['url']; ?>"><?php echo $link['title']; ?></a><?php endif; ?>
                        <?php endwhile; ?>

                        <?php
                        if ($archive_term = get_option('archive_category')) {
                            $term = get_term($archive_term, 'family');
                            if ($term instanceof WP_Term && $term->count > 0): ?>
                                <a href="<?php echo get_term_link($term->term_id) ?>"><?php echo $term->name ?></a>
                            <?php endif; ?>
                        <?php } ?>
                    </div>
                </div>
                <div class="footer-short-links-box">
                    <h4>RESOURCES</h4>
                    <div class="footer-short-links-items">
                        <?php while (have_rows('resources_list', 'option')): the_row(); ?>
                            <?php $link = get_sub_field('resources_list_item', 'option'); ?>
                            <?php if ($link): ?><a
                                href="<?php echo $link['url']; ?>"><?php echo $link['title']; ?></a><?php endif; ?>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
            <div class="footer-newsletter">
                <h4>SIGN UP FOR OUR NEWSLETTER</h4>
                <p>Signup to receive all the latest updates from Quinn!</p>
                <div class="footer-newsletter-form">

                    <div class="footer-newsletter-field">
                        <?php echo do_shortcode(get_field('footer_contact_form', 'option')); ?>
                    </div>
                </div>
                <div class="social-media hidden-sm-up">
                    <p>FOLLOW US</p>
                    <div class="">
                        <a href="<?php the_field('facebook', 'option'); ?>"><span class="fa fa-facebook"></span></a>
                        <a href="<?php the_field('linkedin', 'option'); ?>"><span class="fa fa-linkedin"></span></a>
                        <a href="<?php the_field('twitter', 'option'); ?>"><span class="fa fa-twitter"></span></a>
                        <a href="<?php the_field('instagram', 'option'); ?>"><span class="fa fa-instagram"></span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container clearfix">
            <div class="social-media hidden-sm-down">
                <a href="<?php the_field('facebook', 'option'); ?>"><span class="fa fa-facebook"></span></a>
                <a href="<?php the_field('linkedin', 'option'); ?>"><span class="fa fa-linkedin"></span></a>
                <a href="<?php the_field('twitter', 'option'); ?>"><span class="fa fa-twitter"></span></a>
                <a href="<?php the_field('instagram', 'option'); ?>"><span class="fa fa-instagram"></span></a>
            </div>
            <div class="footer-bottom-link">
                <ul>
                    <li><a href="<?php the_field('site_credits', 'option'); ?>">Site Credits</a></li>
                    <li><a href="<?php the_field('sitemap', 'option'); ?>">Sitemap</a></li>
                    <li><a href="https://www.quinncompany.com/legal-notices/">Legal Notices</a></li>
                    <li><?php echo do_shortcode('[cookies_revoke]'); ?></li>
                    <li>Copyright© 2023. All Rights Reserved</li>
                </ul>
            </div>
            <div class="backToTop hidden-xs-down">
                <a href="javascript:void(0);" class="btn btn-primary back-to-top">Back to top</a>
            </div>
        </div>
    </div>
</footer>


<div class="popup-wrapper-fx" style="">
    <div id="open-btn-fx" class="open-btn-fx">
        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 16 16">
            <title>form</title>
            <path fill="#fff"
                  d="M13.812 4.507l-3.681-3.861c-0.068-0.073-0.163-0.115-0.26-0.115h-5.827c-1.075 0-1.963 0.884-1.963 1.959v11.019c0 1.075 0.888 1.959 1.963 1.959h7.909c1.075 0 1.963-0.884 1.963-1.959v-8.749c0-0.094-0.043-0.184-0.105-0.253zM10.238 1.805l2.464 2.587h-1.601c-0.476 0-0.862-0.382-0.862-0.859v-1.728zM13.196 13.51c0 0.679-0.563 1.238-1.241 1.238h-7.909c-0.674 0-1.241-0.559-1.241-1.238v-11.019c0-0.674 0.562-1.238 1.241-1.238h5.47v2.28c0 0.876 0.708 1.581 1.584 1.581h2.097v8.396z"></path>
            <path fill="#fff"
                  d="M5.030 12.258c-0.199 0-0.361 0.163-0.361 0.361s0.163 0.361 0.361 0.361h5.943c0.199 0 0.361-0.163 0.361-0.361s-0.163-0.361-0.365-0.361h-5.939z"></path>
            <path fill="#fff"
                  d="M9.678 5.29c-0.141-0.141-0.368-0.141-0.509 0l-3.229 3.218c-0.039 0.039-0.073 0.090-0.090 0.148l-0.65 2.049c-0.039 0.13-0.007 0.267 0.090 0.365 0.068 0.068 0.163 0.105 0.256 0.105 0.036 0 0.073-0.007 0.109-0.018l2.049-0.645c0.054-0.018 0.105-0.047 0.148-0.090l3.219-3.229c0.141-0.141 0.141-0.368 0-0.509l-1.392-1.392zM6.098 10.262l0.26-0.823 0.562 0.563-0.823 0.26zM7.596 9.656l-0.891-0.891 2.717-2.71 0.884 0.884-2.71 2.717z"></path>
        </svg>
    </div>
    <div class="popup-form-fx" id="popup-form-fx">
        <div class="close-btn-fx" id="close-btn-fx"></div>
        <iframe class="ctm-call-widget"
                src="https://413439.tctm.co/form/FRT472ABB2C5B9B141A5D73B7F955D50C4DB67C5EB905EC71C297EDF55DC2F0EFB9.html"
                style="width:100%;height:488px;border:none"></iframe>
        <script defer async src="https://413439.tctm.co/formreactor.js"></script>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var isDesktop = window.matchMedia("(min-width: 768px)").matches;

        function openForm() {
            var popupForm = document.querySelector('#popup-form-fx');
            popupForm.classList.add('open');
            localStorage.setItem('isFormOpened', 'true');
        }

        var isFormOpened = localStorage.getItem('isFormOpened');
        if (!isFormOpened && isDesktop) {
            setTimeout(openForm, 5000);
        }
        document.querySelector('#open-btn-fx').onclick = function () {
            openForm();
        };
        document.querySelector('#close-btn-fx').onclick = function () {
            var popupForm = document.querySelector('#popup-form-fx');
            popupForm.classList.remove('open');
        };
    });
</script>


<?php //echo do_shortcode('[cookies_revoke]'); ?>
<!-- footer -->

<!-- Back To Top Icon area
<button class="back-to-top js-back-to-top" type="button">
    <span class="back-to-top__label">Back to top</span>
    <i class="icon-arrow-up"></i>
</button>
-->

<?php wp_footer(); ?>
<?php
/*       add_action(
'wp_enqueue_scripts',
function () {
if (!wp_script_is('contact-form-7')) {
                        wp_enqueue_script('contact-form-7');
                    }
}, 	PHP_INT_MAX


);  */
?>
</body>
</html>
