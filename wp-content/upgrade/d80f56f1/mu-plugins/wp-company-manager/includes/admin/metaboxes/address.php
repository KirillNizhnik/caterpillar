<?php
/*
Title: Address
Post Type: location
Context: normal
Priority: default
*/


global $post, $wpdb;
?>


<label style="margin-bottom: -30px;" for="post_meta_address"><?php _e( "Address", 'cmc' ); ?></label>
<?php
    wp_editor(
    get_post_meta( $post->ID, 'address', true )
    ,'post_meta_address'
    ,array(
        'teeny' => true
        ,'drag_drop_upload' => true
        ,'textarea_name' => 'post_meta[address]'
        ,'media_buttons' => false
        ,'textarea_rows' => 5
    )
)
?>

<p>
    <label for="post_meta_hours"><?php _e( "Phone Number", 'cmc' ); ?></label>
    <?php
    wp_editor(
        get_post_meta( $post->ID, 'phone', true )
        ,'post_meta_phone'
        ,array(
            'teeny' => true
            ,'drag_drop_upload' => true
            ,'textarea_name' => 'post_meta[phone]'
            ,'media_buttons' => false
            ,'textarea_rows' => 5
        )
    )
    ?>
</p>

<p>
    <label for="post_meta_email"><?php _e( "Email", 'cmc' ); ?></label>
    <input class="widefat" type="email" name="post_meta[email]" id="post_meta_email" value="<?php echo esc_attr( get_post_meta( $post->ID, 'email', true ) ); ?>" style="margin-bottom: 20px">
</p>

<p>
    <label for="post_meta_hours"><?php _e( "Hours", 'cmc' ); ?></label>
    <?php
    wp_editor(
        get_post_meta( $post->ID, 'hours', true )
        ,'post_meta_hours'
        ,array(
            'teeny' => true
            ,'drag_drop_upload' => true
            ,'textarea_name' => 'post_meta[hours]'
            ,'media_buttons' => false
            ,'textarea_rows' => 5
        )
    )
    ?>
</p>

<p>
    <label for="post_meta_postal_code"><?php _e( "Postal Code", 'cmc' ); ?></label>
    <input class="widefat" type="text" name="post_meta[postal_code]" id="post_meta_postal_code" value="<?php echo esc_attr( get_post_meta( $post->ID, 'postal_code', true ) ); ?>">
</p>

<p>
    <label for="post_meta_lat"><?php _e( "Latitude", 'cmc' ); ?></label>
    <input class="widefat" type="text" name="post_meta[lat]" id="post_meta_lat" value="<?php echo esc_attr( get_post_meta( $post->ID, 'lat', true ) ); ?>">
</p>

<p>
    <label for="post_meta_lng"><?php _e( "Longitude", 'cmc' ); ?></label>
    <input class="widefat" type="text" name="post_meta[lng]" id="post_meta_lng" value="<?php echo esc_attr( get_post_meta( $post->ID, 'lng', true ) ); ?>">
</p>

<input type="hidden" name="post_meta[state]" value="<?php echo esc_attr( get_post_meta( $post->ID, 'state', true ) ); ?>">

<?php /*

Note: on post save/update all the hidden fields should be updated dynamically

*/ ?>