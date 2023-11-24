<?php
// IMPORTANT: This plugin is dynamically updated - MODIFICATIONS WILL BE OVERWRITTEN - see https://app.getguru.com/card/T585Kydc

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals
// - this file is included in a function, and no globals are being set here

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<?php // phpcs:disable WordPress.WP.EnqueuedResources ?>
<?php // - We specifically *WANT* to inline these scripts this way vs enqueueing ?>
<?php // - We do not want WP or any plugins to be modifying these scripts ?>

<!-- --------- START PLUGIN MARKETINGCLOUDFX-WP - INTEGRATION SCRIPTS --------- -->

<?php
foreach ( $integrations as $slug => $integration ) {
	if ( $integration['enabled'] ) {
		include $integration['filepath'];
	}
}
?>

<!-- --------- END PLUGIN MARKETINGCLOUDFX-WP - INTEGRATION SCRIPTS --------- -->

<?php // phpcs:enable WordPress.WP.EnqueuedResources ?>

<?php // IMPORTANT: This plugin is dynamically updated - MODIFICATIONS WILL BE OVERWRITTEN - see https://app.getguru.com/card/T585Kydc ?>
