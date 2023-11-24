<?php
/*
Title: Service Departments
Post Type: location
Context: normal
Priority: default
*/


global $post, $wpdb;

$services = get_the_terms( $post, 'service' );
$content  = maybe_unserialize( get_post_meta( $post->ID, 'service_info', true ));

if( $services ): foreach( $services as $service ): ?>

<h3 style="margin-bottom: -30px;"><?php echo $service->name; ?></h3>
<?php
$field = isset($content[$service->name]) ? $content[$service->name] : '';
wp_editor(
    $field
    ,'service_'.$service->slug
    ,array(
        'teeny' => true
        ,'drag_drop_upload' => true
        ,'textarea_name' => 'post_meta[service_info]['.$service->name.']'
        ,'media_buttons' => false
        ,'textarea_rows' => 5
    )
);
?>
<div style="margin-top: 20px;"></div>
<?php endforeach; else: ?>
    <p><strong>Select services and save the post to enter additional contact information</strong></p>
<?php endif; ?>