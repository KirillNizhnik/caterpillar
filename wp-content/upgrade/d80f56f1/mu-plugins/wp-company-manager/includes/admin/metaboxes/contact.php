<?php
/*
Title: Contact
Post Type: rep
Context: normal
Priority: default
*/


global $post, $wpdb;
?>
<p>
    <label for="post_meta_title"><?php _e( "Title", 'cmc' ); ?></label>
    <br />
    <input class="widefat" type="text" name="post_meta[title]" id="post_meta_title" value="<?php echo esc_attr( get_post_meta( $post->ID, 'title', true ) ); ?>">
</p>

<p>
    <label for="post_meta_phone"><?php _e( "Phone Number", 'cmc' ); ?></label>
    <br />
    <input class="widefat" type="text" name="post_meta[phone]" id="post_meta_phone" value="<?php echo esc_attr( get_post_meta( $post->ID, 'phone', true ) ); ?>">
</p>

<p>
    <label for="post_meta_email"><?php _e( "Email", 'cmc' ); ?></label>
    <br />
    <input class="widefat" type="email" name="post_meta[email]" id="post_meta_email" value="<?php echo esc_attr( get_post_meta( $post->ID, 'email', true ) ); ?>">
</p>
