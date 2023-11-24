<?php if( have_posts() ): while( have_posts() ): the_post();

    $industry = CAT()->industry();
?>
<div class="container-fluid">

    <article class="page-content">
        <h2>All Applications</h2>

        <div class="product-item-card__list">
            <div class="row soft--sides">
                <?php foreach($industry->applications() as $application): ?>

                    <?php
                        set_query_var( 'item', $application );
                        cat_template('new/loop/content');
                    ?>

                <?php endforeach; ?>
            </div>
        </div>


    </article>
</section>
<?php get_sidebar( ); ?>

<?php endwhile; endif; ?>
</div>