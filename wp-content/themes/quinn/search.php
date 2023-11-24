<?php get_header(); ?>
<?php echo get_template_part('partials/masthead') ?>
<?php
    $search_query   = get_search_query();
    dd($search_query);
    $paged          = get_query_var( 'paged' );
    if( empty( $paged ) ) {
        $paged = 1;
    }

    $search_results = FX_Load_More()->search->get_tabbed_results( $search_query, $paged );

    $tabbed_content = [];
    $active_id      = null;
    foreach( $search_results as $post_type_key => $data ) {
        $tab_id = sanitize_title( $post_type_key );
        $label  = sprintf( '%s (%s)', $data['tab_title'], $data['tab_count'] );
        if(strpos($label,'New Powers') !== false) {
            $label = str_replace('New Powers', 'New Power', $label);
        }
        // if( '50.205.49.193' === $_SERVER['REMOTE_ADDR'] ) {
        // 	var_dump($label);
        // }

        // set tab to be active on page load
        if( empty( $active_id ) ) {
            $active_id = $tab_id;
        }

        $data['label'] = $label;
        $tabbed_content[ $tab_id ] = $data;
    }
?>

<section class="section-margins">
    <div class="container">
        <div class="row">
            <div class="col-xxs-12">
            
                <div class="tab-accordion js-tab-accordion">

                    <div class="tab-accordion__tabs hidden-sm-down">
                        <?php 
                        
                        foreach( $tabbed_content as $id => $data ): 
                        
                        //if ($data['tab_count'] == 0) { continue; }
                        ?>
                            <button 
                                class="tab-accordion__tab js-tab-accordion-btn <?php if( $id === $active_id ) echo 'is-active'; ?>" 
                                type="button"
                                data-tab-id="<?php echo esc_attr( $id ); ?>"
                                fx-count="<?php echo $data['tab_count']; ?>"
                            ><?php echo $data['label']; ?></button>
                        <?php endforeach; ?>
                    </div>

                    <div class="tab-accordion__panels">
                        <?php foreach( $tabbed_content as $tab_id => $data ): ?>
                        <?php //if ($data['tab_count'] == 0) { continue; } ?>
                            <article 
                                class="tab-accordion__panel js-tab-accordion-panel js-load-more-block <?php if( $tab_id === $active_id ) echo 'is-active'; ?>" 
                                data-tab-id="<?php echo esc_attr( $tab_id ); ?>"
                                data-load-more-post-type="<?php echo esc_attr( $data['post_type_key'] ); ?>"
                                data-load-more-total="<?php echo esc_attr( $data['tab_count'] ); ?>"
                                data-load-more-current-page="<?php echo esc_attr( $paged ); ?>"
                                data-load-more-search-query="<?php echo esc_attr( $search_query ); ?>">
                                <button 
                                        class="tab-accordion__panel__toggle js-tab-accordion-btn hidden-md-up <?php if( $tab_id === $active_id ) echo 'is-active'; ?>"
                                        type="button"
                                        data-tab-id="<?php echo esc_attr( $tab_id ); ?>">
                                        <?php echo $data['label']; ?>
                                </button>

                                <div class="tab-accordion__panel__content">
                                    <?php if( !empty( $data['posts'] ) ): ?>
                                        <div class="js-load-more-posts">
                                            <?php 
                                                foreach( $data['posts'] as $post ) {
                                                    setup_postdata( $post );
                                                    get_template_part(
                                                        'partials/search-result',
                                                        null,
                                                        [
                                                            'query' => $search_query // used for highlighting search term
                                                        ]
                                                    );
                                                }
                                            ?>
                                        </div>
                                        <div class="blog-listing__pagination">
                                            <div class="col-xxs-12">
                                                <?php get_template_part( 'partials/pagination' ); ?> 
                                            </div>
                                        </div> 

                                    <?php else: ?>
                                        <div class="search-respond">No results for "<?php echo esc_html( $search_query ); ?>."</div>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                        <?php wp_reset_postdata(); ?>
                    </div>

                </div>

            </div>  
        </div>  
    </div>                                     
</section>
<?php get_footer(); 
