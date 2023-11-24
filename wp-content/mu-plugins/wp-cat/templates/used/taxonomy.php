<?php
    $cat_family = CAT()->family();
?>


    <div class="flexbox">
        <?php get_sidebar(); ?>
        <div class="container flexbox__item page-article">

        	<?php echo $cat_family->before_content; ?>

        	<div class="row">
                <?php if( $cat_family->parent === 0 && $cat_family->slug !== 'power-systems' ): ?>

                    <?php
                        $child_families = get_terms('used-family', array(
                            'parent'     => $cat_family->id,
                            'orderby'    => 'name',
                            'order'      => 'ASC',
                            'hide_empty' => false
                            )
                        );
                        foreach($child_families as $family) {
                            cat_template('used/loop/content-family', array('family' => $family));
                        }
                    ?>

                <?php else: ?>

        		<?php if( have_posts() ): while( have_posts() ): the_post(); ?>
        			<?php cat_template('used/loop/content-single'); ?>
        		<?php endwhile; else: ?>
                    <p><b>Sorry, there aren't currently any listings for this family.</b></p>
                <?php endif; ?>

                <?php endif; ?>
            </div>

		<?php echo $cat_family->after_content; ?>

    </div>
</div>
