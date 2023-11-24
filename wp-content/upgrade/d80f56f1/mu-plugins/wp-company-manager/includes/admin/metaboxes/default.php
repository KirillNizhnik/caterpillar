<?php
/*
Title: Default Location
Post Type: location
Context: side
Priority: default
*/


global $post, $wpdb;
?>

<p>
    <input class="post_meta_default" id="post_meta_default" name="post_meta[default]" type="checkbox" value="1" <?php echo checked(get_post_meta( $post->ID, 'default', true ), 1, false) ?>>
    <label for="post_meta_default"><?php _e( "Default Location", 'cmc' ); ?></label>
</p>
