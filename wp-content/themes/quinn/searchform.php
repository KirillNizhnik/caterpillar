<?php
/**
 * If your form is generated using get_search_form() you do not need to do this,
 * as SearchWP Live Search does it automatically out of the box
 */
?>
<form action="<?php bloginfo('url'); ?>" method="get" class="searh__form">
    <input type="text" placeholder="<?php echo (is_home() || is_singular('post') || is_archive('post')) ? 'Search...' : 'Search...'; ?>" name="s" />

    <?php if(is_home() || is_singular('post')) : ?>
        <input type="hidden" value="post" name="post_type" id="post_type" />
    <?php endif; ?>

    <button class="hidden-xs-down" type="submit"><i class="icon-search"></i></button>
</form>
