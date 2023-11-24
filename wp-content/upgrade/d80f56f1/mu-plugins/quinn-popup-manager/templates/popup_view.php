<?php global $popup_post; ?>
<div id="quinn-popup-modal" class="col-md-offset-2 col-md-8 mfp-hide <?php if ( $popup_post['hide_title'] ){echo ' no-title';}?>" data-popup="<?php echo $popup_post['cookie_name']; ?>">
	<div class="mfp-close">x</div>
    <?php if (get_the_post_thumbnail_url($popup_found['ID'], 'full')) { ?>
	<div class="popup-image" style="background-image: url('<?php echo $popup_post['image_url']; ?>');"></div>
    <?php } ?>
	<?php if (!$popup_post['hide_title']):?>
		<h2><?php echo $popup_post['post_title']; ?></h2>
	<?php endif;?>

	<p class="content"><?php echo $popup_post['post_content']; ?></p>
</div>