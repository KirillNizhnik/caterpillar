<?php
/*
Title: Images
Post Type: cat_used_machine
Context: normal
Priority: default
*/

global $post;

$field  = ( $post->post_type == 'cat_used_machine' ) ? 'images' : 'additional-images';
$images = get_post_meta( $post->ID, $field, true);

if(! is_array($images))
    $images = maybe_unserialize($images);



?>

<div class="cat-admin-bar cat-images-meta">
    <button type="button" class="button button-primary add-image js-add-meta-image">Add Image</button>
    <small>Drag images to reorder.<?php if($post->post_type !== 'cat_used_machine' ): ?> Feed images do not appear below.<?php endif; ?> Suggested image size: 600px by 400px</small>
</div>

<ul class="cat-images js-cat-images js-sortable">

<?php //var_dump($images); ?>

<?php if(! empty($images) ): foreach($images as $i => $image): ?>
    <?php if (is_object($image)) continue; ?>

    <li class="cat-image">
        <?php echo wp_get_attachment_image( $image, 'thumbnail' ); ?>
        <input type="hidden" name="post_meta[<?php echo $field; ?>][]" value="<?php echo $image; ?>" />
        <button type="button" class="button remove-meta-image js-remove-meta-image">Remove</button>
    </li>

<?php endforeach; endif; ?>

</ul>

<script type="text/template" id="imageTemplate">
    <li class="cat-image">
        <img src="<%= src %>" alt="" />
        <input type="hidden" name="post_meta[<?php echo $field; ?>][]" value="<%= id %>" />
        <button type="button" class="button remove-meta-image js-remove-meta-image">Remove</button>
    </li>
</script>