<?php
/*
Title: Industry Template
Post Type: cat_used_machine
Context: side
Priority: default
*/

global $post, $wpdb;

$_template = get_post_meta( $post->ID, '_template', true );
?>

<select name="page_template" id="page_template">
    <option value="">Default Template</option>
    <option value="single-custom.php" <?php selected( 'single-custom.php', $_template); ?>>
        Custom Template
    </option>
</select>