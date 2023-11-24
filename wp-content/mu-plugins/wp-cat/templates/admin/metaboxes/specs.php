<?php
/*
Title: Specs
Post Type: cat_used_machine
Context: normal
Priority: default
*/

global $post, $wpdb;
$fields = get_post_meta( $post->ID, 'specs', true );
?>


<ul class="field-list">

<?php if(is_array($fields)): foreach($fields as $label => $value): ?>

    <li>
        <div class="field-row">
            <div class="field-label col-2">
                <label><?php _e( $label, 'wpc' ); ?></label>
            </div>
            <div class="field-label col-9">
                <input class="widefat" name="post_meta[specs][<?php echo $label; ?>]" type="text" value="<?php echo $value; ?>" />
            </div>
        </div>
        <button type="button" class="button button-large button-spec-remove js-spec-remove">Remove</button>
    </li>

    <?php endforeach; endif; ?>
</ul>

<div class="add-new-field">
    <div class="col-4">
        <input class="widefat add-field-name" name="add_field_name" type="text" value="" placeholder="Field Name" />
    </div>
    <div class="col-4">
        <button type="button" class="button button-primary button-large button-spec-add js-spec-add">Add Field</button>
    </div>

</div>


<script type="text/template" class="fieldTemplate">

    <li>
        <div class="field-row">
            <div class="field-label col-2">
                <label><%= label %></label>
            </div>
            <div class="field-label col-9">
                <input class="widefat" name="post_meta[specs][<%= label %>]" type="text" value="" />
            </div>
        </div>
        <button class="button button-large button-spec-remove js-spec-remove">Remove</button>
    </li>

</script>