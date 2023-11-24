<?php
    $family = CAT()->family();
    global $wp_query;
?>
    <div class="flexbox">
        <?php get_sidebar(); ?>
        <div class="container flexbox__item page-article ">

        	<?php echo $family->before_content; ?>

        	<div class="row">

        		<?php if( have_posts() ): while( have_posts() ): the_post(); ?>

        			<?php cat_template('new/loop/content-single'); ?>

        		<?php endwhile; else: ?>
                    <p><b>Sorry, there aren't currently any listings for this family.</b></p>
                <?php endif ?>

            </div>

		<?php echo $family->after_content; ?>
    </div>
 </div>
