<?php
/*
Title: Features
Post Type: cat_used_machine
Context: normal
Priority: default
*/

global $post, $wpdb;
$fields = get_post_meta( $post->ID, 'features', true );
?>


<ul class="field-list">

<?php if(is_array($fields)): foreach($fields as $label => $value): ?>

    <li>
        <div class="field-row">
            <div class="field-label col-11">
                <input class="widefat" name="post_meta[features][]" type="text" value="<?php echo $value; ?>" />
            </div>
        </div>
        <button type="button" class="button button-large button-spec-remove js-feature-remove">Remove</button>
    </li>

    <?php endforeach; endif; ?>
</ul>

<div class="add-new-field">
    <div class="col-11">
        <input class="widefat add-field-name" name="add_field_name" type="text" value="" placeholder="Feature Text" />
    </div>
    <div class="col-1">
        <button type="button" class="button button-primary button-large button-spec-add js-feature-add">Add Feature</button>
    </div>

</div>


<script type="text/template" class="fieldTemplate">

    <li>
        <div class="field-row">
            <div class="field-label col-11">
                <input class="widefat" name="post_meta[features][]" type="text" value="<%= value %>" />
            </div>
        </div>
        <button class="button button-large button-spec-remove js-spec-remove">Remove</button>
    </li>

</script>