<?php
global $wpdb;
$product = CAT()->product();

?>
<section class="machine-cards">
    <div class="container">
        <div class="row">
            <div class="flex-image-cards hard-top">
                <?php 
                try {
                    while($product->attachments()->have_posts()): $product->attachments()->the_post();
                        cat_template('new/loop/content-single');
                    endwhile; wp_reset_postdata();
                } catch (Exception $exc) {
                    
                } catch (Error $err) {
                    
                }
                ?>
            </div>
        </div>

    </div>
</section>
