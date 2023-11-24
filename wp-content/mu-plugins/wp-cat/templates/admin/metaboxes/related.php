<?php
/*
Title: Related Information
Post Type: cat_used_equipment
Context: normal
Priority: default
*/

global $post;

$press_release  = get_post_meta( $post->ID, 'related_press_release', true );
$video_link     = get_post_meta( $post->ID, 'related_video_link', true );
$news_article   = get_post_meta( $post->ID, 'related_news_article', true );
?>

<table class="details wp-list-table widefat">

    <tr>
        <th style="width: 150px">
            <label for="post_meta_related_press_release">Press Release</label>
        </th>
        <td>
            <input type="text" id="post_meta_related_press_release" name="post_meta[related_press_release]" value="<?php echo $press_release; ?>" class="widefat text" />
        </td>
    </tr>
    <tr>
        <th style="width: 150px">
            <label for="post_meta_related_video_link">Video Link</label>
        </th>
        <td>
            <input type="text" id="post_meta_related_video_link" name="post_meta[related_video_link]" value="<?php echo $video_link; ?>" class="widefat text" />
        </td>
    </tr>
    <tr>
        <th style="width: 150px">
            <label for="post_meta_related_news_article">News Article</label>
        </th>
        <td>
            <input type="text" id="post_meta_related_news_article" name="post_meta[related_news_article]" value="<?php echo $news_article; ?>" class="widefat text" />
        </td>
    </tr>
</table>