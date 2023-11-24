<?php
// IMPORTANT: This plugin is dynamically updated - MODIFICATIONS WILL BE OVERWRITTEN - see https://app.getguru.com/card/T585Kydc

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<?php // phpcs:disable WordPress.WP.EnqueuedResources ?>
<?php // - We specifically *WANT* to inline these scripts this way vs enqueueing ?>
<?php // - We do not want WP or any plugins to be modifying these scripts ?>

<!-- --------- START PLUGIN MARKETINGCLOUDFX-WP - CUSTOM FOOTER SCRIPTS --------- -->

<?php // phpcs:disable WordPress.Security.EscapeOutput ?>
<?php // - This is explicitly HTML with script tags - added by admin user ?>
<?php echo $footer_scripts; ?>
<?php // phpcs:enable WordPress.Security.EscapeOutput ?>


<!-- --------- END PLUGIN MARKETINGCLOUDFX-WP - CUSTOM FOOTER SCRIPTS --------- -->

<?php // phpcs:enable WordPress.WP.EnqueuedResources ?>

<?php // IMPORTANT: This plugin is dynamically updated - MODIFICATIONS WILL BE OVERWRITTEN - see https://app.getguru.com/card/T585Kydc ?>
