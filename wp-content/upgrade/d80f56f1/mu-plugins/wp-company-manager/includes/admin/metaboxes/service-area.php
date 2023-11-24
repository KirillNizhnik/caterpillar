<?php
/*
Title: Service Area
Post Type: rep
Context: normal
Priority: default
*/


global $post, $wpdb;

$zipcodes = get_post_meta( $post->ID, 'zipcode', false );
$zipcodes = is_array($zipcodes) ? implode(', ', $zipcodes) : '';
?>

<p>
    <label for="post_meta_zipcodes"><?php _e( "Zip Codes", 'cmc' ); ?></label>
    <br />
    <textarea class="widefat" name="post_meta[zipcodes]" id="post_meta_zipcodes"><?php echo $zipcodes ?></textarea>
</p>