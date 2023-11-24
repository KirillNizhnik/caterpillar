<?php

/** 
 * $template note:
 * 
 * Block names should be prefixed with acf/. So if the name you specified in
 * fx_register_block is 'your-block-name', the name you should use here is
 * 'acf/your-block-name' 
 */

$template = [
	['acf/homepage-masthead-slider'],
    ['acf/homepage-icon-blocks'],
    ['acf/homepage-video-text'],
    ['acf/homepage-deals-and-specials'],
    ['acf/homepage-industries'],
    ['acf/homepage-equipment-blocks'],
    ['acf/homepage-text-and-image'],
    ['acf/homepage-find-a-location'],
];

?>

<div>
    <InnerBlocks template="<?php echo esc_attr( wp_json_encode( $template ) )?>" />
</div>
