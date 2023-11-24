<?php
/*
Title: Custom Videos
Context: normal
Priority: default
*/

global $post, $wpdb;
$videos = get_post_meta( $post->ID, 'custom_videos', true );

?>

<input type='hidden' name ='post_meta[custom_videos]' value=''>

<ul class="field-list">

<?php if(is_array($videos)): ?>
<?php foreach($videos as $i => $video): ?>

    <li>
        <div class="field-row">
            <div class="field-label col-9">
                <input class="widefat" name="post_meta[custom_videos][]" type="text" value="<?php echo $video; ?>" placeholder="YouTube URL" />
            </div>
            <button type="button" class="button button-large button-spec-remove js-video-remove">-</button>
        </div>
    </li>

<?php endforeach; ?>
<?php endif; ?>

</ul>

<div class="add-new-video field-row">
    <div class="col-4">
        <button type="button" class="button button-primary button-large button-video-add js-video-add">Add Video</button>
    </div>

</div>

<script type="text/template" class="videoTemplate">
    <li>
        <div class="field-row">
            <div class="field-label col-9">
                <input class="widefat" name="post_meta[custom_videos][]" type="text" value="" placeholder="YouTube URL" />
            </div>
            <button type="button" class="button button-large button-spec-remove js-video-remove">-</button>
        </div>
    </li>
</script>
