<?php
/*
Title: Spec Order
Post Type: cnf_equipment
Context: normal
Priority: default
*/

global $post, $wpdb;


$product_id = get_post_meta( $post->ID, 'product_id', true );
$specs_table = $wpdb->prefix . Cat_New_Feed::SPECS_TABLE;

$sql = "SELECT *
        FROM $specs_table
        WHERE product_id=$product_id
        GROUP BY group_name
        ORDER BY priority ASC,
                 group_sort_custom ASC,
                 group_sort ASC,
                 sort_custom ASC,
                 sort
       ";

$specs = $wpdb->query($sql);

$custom = get_post_custom( $post->ID );

?>

<p>Change the order in which specs appear.</p>

<?php

foreach($custom as $name => $meta) {

    echo $name.'<br>';
    if(unserialize($meta[0])) {
        echo '<pre>'; print_r(unserialize($meta[0])); echo '</pre><br>';
    }
    else {
        echo $meta[0].'<br>';
    }
    echo '<br>';
}

//echo '<pre>'; print_r($custom); echo '</pre>';

echo '<pre>'; print_r($specs); echo '</pre>';