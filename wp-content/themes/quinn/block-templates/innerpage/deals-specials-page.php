<section class="image-cards" >
    <div class="container">
        <div class="row">
            <?php
                $featured_post_ids = [];
                $homepage_post = get_post( get_option( 'page_on_front' ) );
                $homepage_blocks = parse_blocks( $homepage_post->post_content );

                foreach( $homepage_blocks as $block ) {
                    if( 'acf/homepage-block' === $block['blockName'] ) {
                        $inner_blocks = $block['innerBlocks'];
                        foreach( $inner_blocks as $inner_block ) {
                            if( 'acf/homepage-deals-and-specials' === $inner_block['blockName'] ) {
                                $featured_post_ids = $inner_block['attrs']['data']['add_deals_and_specials'] ?? [];
                            }
                        }
                    }
                }

                $args = array( 
                    'post_type' => 'deals-specials', 
                    'posts_per_page' => 99, 
                    'post_status' => 'publish',
                    // 'post__in' => $featured_post_ids,
                    'fields' => 'ids',
                    'orderby'   => 'menu_order',
	                'order'     => 'ASC',
                );
                $post_ids = get_posts( $args );
            ?>
            <?php
                // $post_ids = array_merge( $featured_post_ids, $post_ids );
                // $post_ids = array_map( 'absint', $post_ids );
                // $post_ids = array_unique( $post_ids );

                $thumb_id = get_post_thumbnail_id();
            ?>
            
            <?php if( empty( $post_ids ) ): ?>
                <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
            
            <?php else: ?>
                <?php global $post; ?>
                <?php foreach( $post_ids as $post_id ): ?>
                    <?php 
                        $post = get_post( $post_id );
                        setup_postdata( $post );

                        $image_id = get_field('deals_and_specials_image', get_the_ID()); 
                        if( empty( $image_id ) ) {
                            $image_id = get_post_thumbnail_id();
                        }
                    ?>
                    <div class="col-sm-6 image-cards-block-load-content">
                        <div class="image-cards-block">
                            <a href="<?php the_permalink(); ?>">
                                <div class="image-cards-block-image">
                                    <?php
                                    echo fx_get_image_tag($image_id); ?>
                                </div>
                                <div class="image-cards-block-content">
                                    <h6 class="image-cards-category-name">
                                        <?php
                                            $empty = get_the_terms( get_the_ID(),'deals_category' );
        
                                            if (!empty($empty)) {
                                                    foreach ( get_the_terms( get_the_ID(), 'deals_category' ) as $tax ) {
                                                    echo $tax->name . '<span class="comma">,</span> ';
                                                }
                                            }
                                        ?>
                                        <?php
                                            $empty = get_the_terms( get_the_ID(),'specials_category' );
        
                                            if (!empty($empty)) {
                                                foreach ( get_the_terms( get_the_ID(),'specials_category' ) as $tax ) {
                                                    echo $tax->name . '<span class="comma">,</span> ';
                                                }
                                            }
        
        
                                        ?>
                                    </h6>
                                    <h4><?php the_title(); ?></h4>
                                    <p><?php echo wp_trim_words( get_the_excerpt( get_the_ID() ) ); ?></p>
                                    <span class="btn btn-primary">Read more</span>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php wp_reset_postdata(); ?>
            <?php endif; ?>
        </div>
        <?php echo do_shortcode('[facetwp facet="deals_specials_pager"]'); ?>
    </div>

</section>
