<?php
/*
Title: Details
Post Type: cat_used_machine
Context: normal
Priority: default
*/

global $post;

$price         = get_post_meta( $post->ID, 'price', true);
$hours         = get_post_meta( $post->ID, 'hours', true );
$location      = get_post_meta( $post->ID, 'location', true );
$serial_number = get_post_meta( $post->ID, 'serial_number', true );
?>

<table class="details wp-list-table widefat">

    <tr>
        <th style="width: 150px">
            <label for="post_meta_hours">Hours</label>
        </th>
        <td>
            <input type="text"
                   id="post_meta_hours"
                   name="post_meta[hours]"
                   value="<?php echo $hours; ?>"
                   class="widefat text" />
        </td>
    </tr>
    <tr>
        <th style="width: 150px">
            <label for="post_meta_price">Price</label>
        </th>
        <td>
            <input type="text"
                   id="post_meta_price"
                   name="post_meta[price]"
                   value="<?php echo $price; ?>"
                   class="widefat text" />
        </td>
    </tr>
    <tr>
        <th style="width: 150px">
            <label for="post_meta_location">Location</label>
        </th>
        <td>
            <input type="text"
                   id="post_meta_location"
                   name="post_meta[location]"
                   value="<?php echo $location; ?>"
                   class="widefat text" />
        </td>
    </tr>
    <tr>
        <th style="width: 150px">
            <label for="post_meta_serial_number">Serial Number</label>
        </th>
        <td>
            <input type="text"
                   id="post_meta_serial_number"
                   name="post_meta[serial_number]"
                   value="<?php echo $serial_number; ?>"
                   class="widefat text" />
        </td>
    </tr>
</table>