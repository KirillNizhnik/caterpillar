<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<?php 
    $uri = $_SERVER['REQUEST_URI'];
    $newFormLink = false;
    $family_urls = ['/new-equipment/machines/backhoe-loaders/', '/new-equipment/machines/excavators/', '/new-equipment/machines/skid-steer-and-compact-track-loaders/', '/new-equipment/machines/compact-track-loaders/', '/new-equipment/machines/mini-excavators/', '/new-equipment/machines/skid-steer-loaders/', '/new-equipment/machines/medium-excavators/', '/new-equipment/machines/large-excavators/', '/new-equipment/machines/wheel-excavators/', '/new-equipment/machines/small-excavators/'];
    foreach($family_urls as $url){
        if(str_contains($uri, $url)){
            $newFormLink = true;
            break;
        }
    }
?>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
    <link rel="icon" href="../wp-content/themes/quinn/assets/img/favicon.png" type="image/x-icon"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <?php // Insert Google Fonts <link> here. Please use &display=swap in your URL!
    ?>

    <?php wp_head(); ?>

	<script>var $newFormLink = <?php echo $newFormLink ? 'true' : 'false'; ?>;</script>
</head>

<body <?php body_class(); ?>>

    <?php wp_body_open(); ?>

    <?php
        $logo_id    = fx_get_client_logo_image_id();
        $home_url   = get_home_url();
    ?>
    <div class="top-header">
        <div class="container">
            <div class="top-header-main clearfix hidden-xs-down">
                <div class="desktop-search-area">
                    <div class="search-controll">
                        <?php get_search_form(); ?>
                    </div>
                </div>
                <div class="language-button">
                    <?php echo do_shortcode('[weglot_switcher]'); ?>
                </div>
                <div class="top-header-menu">
                    <?php $btn = get_field('account', 'option'); ?>
                    <?php if($btn): ?><a class="hidden-sm-down" href="<?php echo $btn['url']; ?>"><?php echo $btn['title']; ?></a><?php endif; ?>

                    <?php $btn = get_field('careers', 'option'); ?>
                    <?php if($btn): ?><a href="<?php echo $btn['url']; ?>"><?php echo $btn['title']; ?></a><?php endif; ?>
                    
                    <?php if(is_page(18341)): ?>
                        <a href="tel:562-463-4000" class="call-us">
                            <strong class="call-us__text">CALL US!</strong>
                            (562) 463-4000
                        </a>
                    <?php else:?>

                        <?php if(get_field('phone', 'option')): ?>
                        <a href="tel:<?php echo get_field('phone', 'option'); ?>" class="call-us">
                            <strong class="call-us__text">CALL US!</strong>
                            <?php echo get_field('phone', 'option'); ?>
                        </a>
                        <?php endif; ?>
                    <?php endif;?>
                    <?php $btn = get_field('locations', 'option'); ?>
                    <?php if($btn): ?><a href="<?php echo $btn['url']; ?>"><?php echo $btn['title']; ?></a><?php endif; ?>

                    <?php $btn = get_field('find', 'option'); ?>
                    
                </div>
            </div>
           <div class="mobile-menu">
                <?php
                wp_nav_menu(
                    [
                        'container'         => 'nav',
                        'container_class'   => 'tablet-nav',
                        'depth'             => 1,
                        'theme_location'    => 'my-custom-menu',
                    ]
                );
                ?>
            </div>
        </div>
    </div>

    <header class="page-header page-header--sticky" id="page-header">
        <div class="bottom-header">
            <div class="container clearfix flex-container">
                <a class="site-logo" href="<?php echo esc_url( $home_url ); ?>">

                    <?php if( get_field('show_ag_header', get_queried_object()) == 'yes' ): ?>
                        <?php echo fx_get_image_tag( 15643, 'logo' ); ?>
                    <?php else: ?>
                        <?php echo fx_get_image_tag( $logo_id, 'logo' ); ?>

                    <?php endif; ?>
                </a>
                <div class="tablet-menu">
                    <?php
                        // Output the footer navigation
                        wp_nav_menu(
                            [
                                'container'         => 'nav',
                                'container_class'   => 'tablet-nav',
                                'depth'             => 1,
                                'theme_location'    => 'my-custom-menu',
                            ]
                        );
                    ?>
                </div>
                <div class="bottom-header-right clearfix">
                    <div class="main-menu-wrapper flex-container">
                        <button class="ubermenu-responsive-toggle close ubermenu-responsive-toggle-main ubermenu-skin-none ubermenu-loc- ubermenu-responsive-toggle-content-align-left ubermenu-responsive-toggle-align-full menu-is-active ubermenu-responsive-toggle-open" data-ubermenu-target="ubermenu-main-2">
                            <i class="icon-close"></i>
                            close
                        </button>
                        <?php ubermenu( 'main' , array( 'menu' => 2 ) ); ?>
                        <?php
                            // Output the footer navigation
                            // wp_nav_menu(
                            //     [
                            //         'container'         => 'nav',
                            //         'container_class'   => 'nav-primary',
                            //         'depth'             => 3,
                            //         'theme_location'    => 'main_menu',
                            //     ]
                            // );
                        ?>
                        <div class="mobile-header-language-button hidden-sm-up">
                            <?php echo do_shortcode('[weglot_switcher]'); ?>
                        </div>
                        <div class="mobile-search-toggle hidden-sm-up">
                            <span class="icon-search"></span>
                        </div>
                        <div class="header-button">
                            <?php if($newFormLink){
                                echo do_shortcode('[fx_modal id=19770]'); wp_reset_postdata(); ?>
                                <a class="fx-modal-open-button btn btn-primary" id="19770">Contact Us</a>
                            <?php } else { ?>
                            <?php $btn  = get_field('contact_us', 'option'); ?>
                            <?php if($btn): ?><a class="btn btn-primary" href="<?php echo $btn['url']; ?>"><?php echo $btn['title']; ?></a><?php endif; ?>
                            <?php } ?>
                        </div>
                        <!-- <div class="mobile-menu-toggle hidden-md-up">
                            <span class="icon-menu"></span>
                            menu
                        </div> -->

                    </div>
                </div>
            </div>
        </div>
        <div class="fixed-navbar" >
            <div class="site-notification" id="featured-products-banner-fixed">
                <a>View our featured products</a>
            </div>
            <div class="fixed-div">
                <!-- <h2><?php //the_field('featured_products_title', 'option') ?></h2> -->
                <div class="container">
                    <div class="selected-products">
                        <?php
                        //specifically check if we have slick - call if not
                        if (!wp_script_is('fx_slick')) {
                            wp_enqueue_script('fx_slick');
                        }
                        if(!wp_style_is('fx_slick')) {
                            wp_enqueue_style('fx_slick');
                        }

                        if (!wp_script_is('fx_choices')) {
                            wp_enqueue_script('fx_choices');
                        }
                        if (!wp_script_is('fx_choices_plugin')) {
                            wp_enqueue_script('fx_choices_plugin');
                        }
                        if (!wp_style_is('fx_choices_custom')) {
                            wp_enqueue_style('fx_choices_custom');
                        }

                        $featured_posts = get_field('select_featured_products', 'option');
                        $featured_posts = get_posts([
                            'post_type' => 'cat_used_machine',
                            'posts_per_page' => -1,
                            'meta_key' => 'featured',
                            'meta_value' => 1,
                        ]);
                        if( $featured_posts ): ?>
                            <?php foreach( $featured_posts as $featured_post ):
                                $permalink = get_permalink( $featured_post->ID );
                                $title = get_the_title( $featured_post->ID );
                                $product_id = $featured_post->ID;
                                $machine = CAT()->product($product_id);
                                if(is_array($machine->images) && $machine->images[0]):

                                        $image = cat_sized_image( reset($machine->images), array(312,233), array( 'class' => 'img-responsive' )  );

                                else:
                                    $image = fx_get_image_tag(7339);
                                endif;
                                ?>

                                <div class="selected-item">
                                    <div class="selected-image">
                                        <?php echo $image; ?>
                                    </div>
                                    <div class="selected-content">
                                        <h4><?php echo esc_html( $title ); ?></h4>
                                        <?php if ( !empty($machine->price ) ) echo '$' . number_format($machine->price); else echo 'Request a Quote for Pricing'; ?><br>
                                        <a class="btn btn-primary" href="<?php echo esc_url( $permalink ); ?>">VIEW DEALS</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="fixed-navbar-control hidden-sm-up">

                <a href="<?php the_permalink(529) ?>">
                    <span class="icon-location-on"></span>
                    Locations
                </a>

                <?php if(is_page(18341)): ?>
                    <a href="tel:<951.823.8556">
                        <span class="icon-phone-alt"></span>
                        Call us
                    </a>
                <?php else:?>

                    <a href="tel:<?php echo get_field('phone', 'option');?>">
                        <span class="icon-phone-alt"></span>
                        Call us
                    </a>
                <?php endif;?>

                <?php if($newFormLink){ ?>
                    <a class="fx-modal-open-button" id="19770">
                        <span class="icon-chat"></span>
                        Contact Us
                    </a>
                <?php } else { ?>
                    <a href="<?php the_permalink(575) ?>">
                        <span class="icon-chat"></span>
                        Contact Us
                    </a>
                <?php } ?>
                
                <a href="<?php the_permalink(535) ?>">
                    <span class="icon-suitcase"></span>
                    Careers
                </a>
            </div>
        </div>
        <div class="search-mobile">
            <form class="search-mobile-form" action="<?php bloginfo('url'); ?>" method="get">
                 <?php get_search_form(); ?>
            </form>
        </div>


    </header>
