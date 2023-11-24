<?php
/*
Title: Rental Rates
Context: side
Priority: default
*/

global $post, $wpdb;
$rates = get_post_meta( $post->ID, 'rental_rates', true );

$rate_keys = array('period', 'currency_value', 'currency_id');
?>

<ul class="field-list">
    <li>
        <div class="field-row">
            <div class="field-label col-3"> <label>Period</label> </div>
            <div class="field-label col-3"> <label>Rate</label> </div>
            <div class="field-label col-3"> <label>Currency</label> </div>
        </div>
    </li>

<?php if(is_array($rates)): ?>
<?php foreach($rates as $i => $rate): ?>

    <li>
        <div class="field-row">
        
        <?php foreach ($rate_keys as $k => $key): ?>

            <?php
                $value = "";
                if (is_array($rate))
                    $value = $rate[$key];
            ?>

            <div class="field-label col-3">
                <input class="widefat" name="post_meta[rental_rates][<?php echo $i ?>][<?php echo $key; ?>]" type="text" <?php echo ($k==0) ? "readonly='readonly'" : "" ?> value="<?php echo $value; ?>" />
            </div>

        <?php endforeach ?>

        </div>
        <button type="button" class="button button-large button-spec-remove js-spec-remove">-</button>
    </li>

<?php endforeach; ?>
<?php endif; ?>

</ul>

<div class="add-new-field">
    <div class="col-4">
        <input class="widefat add-field-name" name="add_field_name" type="text" value="" placeholder="Period" />
    </div>
    <div class="col-4">
        <button type="button" class="button button-primary button-large button-spec-add js-spec-add">Add Field</button>
    </div>

</div>


<script type="text/template" class="fieldTemplate">
    <li>
        <div class="field-row">
            <?php foreach ($rate_keys as $i => $key): ?>
                <div class="field-label col-3">
                    <input class="widefat" name="post_meta[rental_rates][<%= label %>][<?php echo $key; ?>]" type="text" <?php echo ($i==0) ? "readonly='readonly'" : "" ?> value="<?php if ($i==0) echo "<%= label %>" ?>" />
                </div>
            <?php endforeach ?>
        </div>
        <button class="button button-large button-spec-remove js-spec-remove">-</button>
    </li>
</script>
