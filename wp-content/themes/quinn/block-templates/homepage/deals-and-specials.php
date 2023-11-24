 <section class="home-deals">
    <div class="container">
        <h2>Deals & Specials</h2>
        <div class="home-deals-wrapper">
            <div class="home-deals-slider">
                <?php if( !empty( $featured_posts = get_field('add_deals_and_specials') ) ): ?>
                    <?php foreach( $featured_posts as $featured_post ): ?>
                        <?php
                            $permalink      = get_permalink( $featured_post->ID );
                            $title          = get_the_title( $featured_post->ID );
                            $custom_field   = wp_trim_words( get_the_excerpt( $featured_post->ID ) );
                            $image          = get_field('deals_and_specials_image', $featured_post->ID );

                            if( empty( $image ) ) {
                                $image = get_post_thumbnail_id( $featured_post->ID );
                            }
                        ?>
                        <div class="home-deals-slider-item">
                            <div class="row">
                                <div class="col-lg-7">
                                    <div class="home-deals-image">
                                        <?php echo fx_get_image_tag( $image, 'full' ); ?>
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="home-deals-content">
                                        <h4><?php echo esc_html( $title ); ?></h4>
                                        <p class="hidden-sm-down"><?php echo $custom_field; ?></p>
                                        <a class="btn btn-primary" href="<?php echo esc_url( $permalink ); ?>">VIEW DEAL</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php
                $link_text      = 'View all deals &amp; specials';
                $link_url       = get_permalink( 708 );
                $link_target    = '';

                if( !empty( $link = get_field( 'dealers_specials_link' ) ) ) {
                    $link_text      = $link['title'];
                    $link_url       = $link['url'];
                    $link_target    = '';
                }
            ?>
            <div class="home-deals-view-button hidden-sm-up">
                <a href="<?php echo esc_url( $link_url ); ?>" class="btn btn-primary" <?php if( !empty( $link_target ) ) printf( 'target="%s"', esc_attr( $link_target ) ); ?>><?php echo wp_kses_post( $link_text ); ?></a>
            </div>
            <div class="home-deals-view-button hidden-xs-down">
                <a href="<?php echo esc_url( $link_url ); ?>" class="btn btn-primary" <?php if( !empty( $link_target ) ) printf( 'target="%s"', esc_attr( $link_target ) ); ?>><?php echo wp_kses_post( $link_text ); ?></a>
            </div>
        </div>
    </div>
</section>
