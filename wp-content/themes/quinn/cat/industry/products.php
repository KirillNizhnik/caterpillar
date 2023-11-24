<?php get_header(); ?>




<?php if( have_posts() ): while( have_posts() ): the_post();

    $industry = CAT()->industry();
?>
<div class="container-fluid">

    <article class="page-content">
        <h2>All Products</h2>

        <div class="product-item-card__list">
            <div class="row soft--sides">
                <?php foreach($industry->products() as $product): ?>

                    <?php
                        set_query_var( 'product', $product ); // passes family to template part
                        get_template_part( 'partials/loop', 'content');
                    ?>

                <?php endforeach; ?>
            </div>
        </div>

    </article>
</div>
<?php get_sidebar( ); ?>

<?php endwhile; endif; ?>



<?php get_footer();
