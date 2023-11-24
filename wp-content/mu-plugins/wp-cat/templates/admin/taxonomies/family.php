
<?php
    $thumbnail_id    = '';
    $header_id       = '';
//    $redirect       = '';

    if( isset($term_meta['thumbnail'][0]) AND ! empty($term_meta['thumbnail'][0]) ){
        $thumbnail_id = $term_meta['thumbnail'][0];
    }

    if( isset($term_meta['header'][0]) AND ! empty($term_meta['header'][0]) ){
        $header_id = $term_meta['header'][0];
    }

//    if( isset($term_meta['redirect'][0]) AND ! empty($term_meta['redirect'][0]) ){
//        $redirect = $term_meta['redirect'][0];
//    }
?>

<!--<tr class="form-field">-->
<!--    <th scope="row" valign="top"><label for="redirect">Redirect</label></th>-->
<!--	<td>-->
<!--        <input name="term_meta[redirect]" id="redirect" type="text" value="--><?php //echo $redirect ?><!--" size="40" aria-required="true">-->
<!--		<p class="description">Redirect this family to a custom URL</p>-->
<!--	</td>-->
<!--</tr>-->

<tr class="form-field">
    <th scope="row" valign="top"><label>Images</label></th>
    <td>
        <table>
            <tr>
                <td valign="top">
                    <label class="term-image-label">Thumbnail</label>

                    <div class="term-image" id="term_thumbnail">
                        <?php
                            if ( ! empty($thumbnail_id) )
                                echo wp_get_attachment_image( $thumbnail_id, array(300, 200) );
                        ?>
                        <button type="button" class="button js-img-add <?php echo (empty($thumbnail_id)) ? '' : 'hidden'; ?>">Add Image</button>
                        <button type="button" class="button js-img-remove <?php echo (empty($thumbnail_id)) ? 'hidden' : ''; ?>">Remove Image</button>
                        <input type="hidden" class="img-input js-img-input" id="term_meta_thumbnail" name="term_meta[thumbnail]" value="<?php echo $thumbnail_id; ?>" />
                    </div>
                </td>
                <td valign="top">
                    <label class="term-image-label">Header</label>

                    <div class="term-image" id="term_header">
                        <?php
                            if ( ! empty($header_id) )
                                echo wp_get_attachment_image( $header_id, array(300, 200) );
                        ?>
                        <button type="button" class="button js-img-add <?php echo (empty($header_id)) ? '' : 'hidden'; ?>">Add Image</button>
                        <button type="button" class="button js-img-remove <?php echo (empty($header_id)) ? 'hidden' : ''; ?>">Remove Image</button>
                        <input type="hidden" class="img-input js-img-input" id="term_meta_header" name="term_meta[header]" value="<?php echo $header_id; ?>" />
                    </div>
                </td>
            </tr>
        </table>
    </td>
</tr>

<tr>
    <th scope="row" valign="top"><label for="term_meta[content_before_products]">Content Before Equipment</label></th>
    <td>
        <?php
            $content = isset($term_meta['content_before_products'][0]) ? $term_meta['content_before_products'][0] : '';
            wp_editor($content, 'term_meta_content_before_products',
            array(
                'textarea_name' => 'term_meta[content_before_products]'
            )); ?>
    </td>
</tr>

<tr>
    <th scope="row" valign="top"><label for="term_meta[content_after_products]">Content After Equipment</label></th>
    <td>
        <?php
        $content = isset($term_meta['content_after_products'][0]) ? $term_meta['content_after_products'][0] : '';
        wp_editor($content, 'term_meta_content_after_products',
            array(
                'textarea_name' => 'term_meta[content_after_products]'
            )); ?>
    </td>
</tr>
