<?php if( !empty( get_field( 'industries_home_title' ) ) ): ?>
<section class="home-industry">
        <div class="container">
            <div class="home-industry-header">
                <h2><?php the_field('industries_home_title'); ?></h2>
                <?php $btn  = get_field("industries_home_link"); ?>
				<?php if($btn): ?><a class="btn btn-primary hidden-sm-down" href="<?php echo $btn['url']; ?>">VIEW ALL INDUSTRIES</a>
				<?php endif; ?>
            </div>
            <div class="home-industry-list">
                <!-- for tab to desktop -->
                <div class="home-industry-list-slider hidden-xs-down">
                    <div class="home-industry-list-slider-box">
                        <div class="row">
                        	<?php if( have_rows('industries_home_cta_slider') ): ?>
							    <?php while( have_rows('industries_home_cta_slider') ): the_row();
							        $p_object = get_sub_field('individual_industry');
							        $image = get_post_thumbnail_id($p_object->ID);
							        $permalink = get_permalink( $p_object->ID );
							        $title = get_the_title( $p_object->ID );
							        $excerpt = apply_filters( 'the_excerpt', get_the_excerpt( $p_object->ID) );
							        ?>
							        <div class="col-md-4 col-sm-6">
		                                <div class="home-industry-column">
		                                	<?php if($p_object): ?>
		                                    <a href="<?php echo $permalink; ?>">
		                                        <div class="home-industry-image">
		                                            <?php echo fx_get_image_tag( $image, 'full' ); ?>
		                                        </div>
		                                        <div class="home-industry-content">
		                                            <span class="btn btn-primary">VIEW INDUSTRY</span>
		                                            <div class="home-industry-content-clippy">
		                                                <h5><?php echo $title ?></h5>
		                                                <?php // echo $excerpt; ?>
		                                            </div>
		                                        </div>
		                                    </a>
		                                    <?php endif; ?>
		                                </div>
		                            </div>
							    <?php endwhile; ?>
							<?php endif; ?>
                        </div>
                    </div>
                    <div class="home-industry-list-slider-box">
                        <div class="row">
                        	<?php if( have_rows('industries_cta_second_group') ): ?>
							    <?php while( have_rows('industries_cta_second_group') ): the_row();
							        $p_object = get_sub_field('individual_industry');
							        $image = get_post_thumbnail_id($p_object->ID);
							        $permalink = get_permalink( $p_object->ID );
							        $title = get_the_title( $p_object->ID );
							        $excerpt = apply_filters( 'the_excerpt', get_the_excerpt( $p_object->ID) );
							        ?>
							        <div class="col-md-4 col-sm-6">
		                                <div class="home-industry-column">
		                                	<?php if($p_object): ?>
		                                    <a href="<?php echo $permalink; ?>">
		                                        <div class="home-industry-image">
		                                            <?php echo fx_get_image_tag( $image, 'full' ); ?>
		                                        </div>
		                                        <div class="home-industry-content">
		                                            <span class="btn btn-primary">VIEW INDUSTRY</span>
		                                            <div class="home-industry-content-clippy">
		                                                <h5><?php echo $title ?></h5>
		                                                <?php echo $excerpt; ?>
		                                            </div>
		                                        </div>
		                                    </a>
		                                    <?php endif; ?>
		                                </div>
		                            </div>
							    <?php endwhile; ?>
							<?php endif; ?>
                        </div>
                    </div>
                </div>
                <!-- for mobile -->
                <div class="home-industry-list-slider hidden-sm-up">
                    <div class="home-industry-list-slider-box">
                        <div class="row">
                            <?php if( have_rows('industries_home_cta_slider') ): ?>
							    <?php while( have_rows('industries_home_cta_slider') ): the_row();
							        $p_object = get_sub_field('individual_industry');
							        $image = get_post_thumbnail_id($p_object->ID);
							        $permalink = get_permalink( $p_object->ID );
							        $title = get_the_title( $p_object->ID );
							        $excerpt = apply_filters( 'the_excerpt', get_the_excerpt( $p_object->ID) );
							        ?>
							        <div class="col-md-4 col-sm-6 mobile-slides">
		                                <div class="home-industry-column">
		                                	<?php if($p_object): ?>
		                                    <a href="<?php echo $permalink; ?>">
		                                        <div class="home-industry-image">
		                                            <?php echo fx_get_image_tag( $image, 'full' ); ?>
		                                        </div>
		                                        <div class="home-industry-content">
		                                            <span class="btn btn-primary">VIEW INDUSTRY</span>
		                                            <div class="home-industry-content-clippy">
		                                                <h5><?php echo $title ?></h5>
		                                                <?php echo $excerpt; ?>
		                                            </div>
		                                        </div>
		                                    </a>
		                                    <?php endif; ?>
		                                </div>
		                            </div>
							    <?php endwhile; ?>
							<?php endif; ?>
                        </div>
                    </div>
                    <div class="home-industry-list-slider-box">
                        <div class="row">
                            <?php if( have_rows('industries_cta_second_group') ): ?>
							    <?php while( have_rows('industries_cta_second_group') ): the_row();
							        $p_object = get_sub_field('individual_industry');
							        $image = get_post_thumbnail_id($p_object->ID);
							        $permalink = get_permalink( $p_object->ID );
							        $title = get_the_title( $p_object->ID );
							        $excerpt = apply_filters( 'the_excerpt', get_the_excerpt( $p_object->ID) );
							        ?>
							        <div class="col-md-4 col-sm-6 mobile-slides">
		                                <div class="home-industry-column">
		                                	<?php if($p_object): ?>
		                                    <a href="<?php echo $permalink; ?>">
		                                        <div class="home-industry-image">
		                                            <?php echo fx_get_image_tag( $image, 'full' ); ?>
		                                        </div>
		                                        <div class="home-industry-content">
		                                            <span class="btn btn-primary">VIEW INDUSTRY</span>
		                                            <div class="home-industry-content-clippy">
		                                                <h5><?php echo $title ?></h5>
		                                                <?php echo $excerpt; ?>
		                                            </div>
		                                        </div>
		                                    </a>
		                                    <?php endif; ?>
		                                </div>
		                            </div>
							    <?php endwhile; ?>
							<?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="home-industry-list-button hidden-md-up">
                    <?php $btn  = get_field("industries_home_link"); ?>
					<?php if($btn): ?><a class="btn btn-primary" href="<?php echo $btn['url']; ?>">VIEW ALL INDUSTRIES</a>
					<?php endif; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>