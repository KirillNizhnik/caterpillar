<?php
// IMPORTANT: This plugin is dynamically updated - MODIFICATIONS WILL BE OVERWRITTEN - see https://app.getguru.com/card/T585Kydc

/**************************************************
 * Plugin Name: WebFX Core Services & MCFX - MarketingCloudFX
 * Description: This plugin provides dynamic updates for WebFX plugins, MarketingCloudFX integration, and related services. <code><strong>WARNING:</strong></code> <em>Disabling this plugin will disable WebFX plugin updates.</em> Specific features may be disabled in the <a href="/wp-admin/options-general.php?page=mcfx-wp-settings">settings</a> instead.
 *
 * Version: 2.4.6
 * Requires PHP: 5.4
 * Requires at least: 5.0
 * Tested: 6.1
 *
 * Author: WebFX
 * Author URI: https://www.webfx.com
 * Plugin URI: https://app.webfx.com/marketingcloudfx/dashboard
 * Text Domain: webfx
 * Update URI: marketingcloudfx-wp
 *
 * Settings: /wp-admin/options-general.php?page=mcfx-wp-settings
 *
 * Release Notes: release_notes.md
 *************************************************/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// NOTE: Leave this code in place
// - It is HIGHLY discouraged to stop this plugin from updating
// - It is HIGHLY discouraged to modify the MCFX plugin outside the normal update process
// *********************************************************************
// Make this plugin updatable by WebFX WP Core Services
// - This code must be in the plugin's primary file
// - Comment out this code to stop the plugin from updating if needed
add_filter(
	'mcfx_webfx_wp_core_services_updatable_plugins',
	function ( $plugins ) {
		$plugins[] = __FILE__;
		return $plugins;
	}
);
// *********************************************************************

// URLs
define( 'MCFX_WP_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'MCFX_WP_ASSETS_URL', MCFX_WP_PLUGIN_URL . '/assets' );
define( 'MCFX_WP_ASSETS_IMG_URL', MCFX_WP_ASSETS_URL . '/img' );

// Paths
define( 'MCFX_WP_PLUGIN_DIR', __DIR__ );
define( 'MCFX_WP_INCLUDES_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'includes' );
define( 'MCFX_WP_FEATURES_DIR', MCFX_WP_INCLUDES_DIR . DIRECTORY_SEPARATOR . 'features' );
define( 'MCFX_WP_TEMPLATES_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'templates' );
define( 'MCFX_WP_ADMIN_TEMPLATES_DIR', MCFX_WP_TEMPLATES_DIR . DIRECTORY_SEPARATOR . 'admin' );
define( 'MCFX_WP_FRONTEND_TEMPLATES_DIR', MCFX_WP_TEMPLATES_DIR . DIRECTORY_SEPARATOR . 'frontend' );
define( 'MCFX_WP_INTEGRATIONS_DIR', MCFX_WP_FRONTEND_TEMPLATES_DIR . DIRECTORY_SEPARATOR . 'integrations' );

require_once MCFX_WP_INCLUDES_DIR . DIRECTORY_SEPARATOR . 'class-mcfx-wp.php';

require_once MCFX_WP_FEATURES_DIR . DIRECTORY_SEPARATOR . 'class-mcfx-autologin.php';
require_once MCFX_WP_FEATURES_DIR . DIRECTORY_SEPARATOR . 'class-mcfx-reviews.php';
require_once MCFX_WP_FEATURES_DIR . DIRECTORY_SEPARATOR . 'class-mcfx-updates.php';

// IMPORTANT: This plugin is dynamically updated - MODIFICATIONS WILL BE OVERWRITTEN - see https://app.getguru.com/card/T585Kydc
