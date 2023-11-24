<?php
// IMPORTANT: This plugin is dynamically updated - MODIFICATIONS WILL BE OVERWRITTEN - see https://app.getguru.com/card/T585Kydc

/**
 * MCFX WP
 *  - Contains primary functionality - MCFX script integrations
 */

// phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedScript
// - Scripts are intentionally not output with enqueue functions
// - We don't want any WP or plugin functionality messing with these scripts - moving, combining, minifying, etc.

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

final class MCFX_WP {

	// Updates & Licensing URL
	//  - sprintf format
	//  - %1s is the action
	//  - %2s is the license key serial
	//  - %3s is the channel
	const WP_CORE_SERVICES_URL = 'https://wp-core-services.webpagefxdev.com/?action=%1s&serial=%2s&channel=%3s';

	/**
	* Singleton pattern
	*  - This makes working with WP Hooks fairly nice
	*/
	private static $instance = null;
	public static function instance() {
		return is_null( self::$instance ) ? new self() : self::$instance;
	}
	private function __construct() {

		// Already instantiated, and new called on the class directly
		if ( ! is_null( self::$instance ) ) {
			$class = get_called_class();
			throw new Exception( $class . ' uses forced singleton pattern - call ' . $class . '::instance() instead to get existing instance.' );
		}

		self::$instance = $this;

		$this->init_hooks();
	}

	/**
	 * Get channel to use for update checks & all related info
	 */
	private $channel = null;
	public function get_channel() {
		if ( is_null( $this->channel ) ) {
			if ( defined( 'MCFX_WP_UPDATE_CHANNEL' ) ) {
				$this->channel = MCFX_WP_UPDATE_CHANNEL;
			} else {
				$this->channel = 'production';
			}
		}
		return $this->channel;
	}

	/**
	 * Get MCFX ID from option table
	 */
	private $mcfx_id = null;
	public function get_mcfx_id() {
		if ( is_null( $this->mcfx_id ) ) {
			// will return false if option doesn't exist
			$this->mcfx_id = get_option( 'mcfx_id' );
		}
		return $this->mcfx_id;
	}

	/**
	 * Set MCFX ID to new value
	 */
	private function set_mcfx_id( $mcfx_id ) {
		update_option( 'mcfx_id', $mcfx_id );
		$this->mcfx_id = $mcfx_id;
	}

	/**
	 * Get extra mcfx integrations list and whether they are enabled
	 */
	private $mcfx_integrations = null;
	private function get_mcfx_integrations() {
		if ( is_null( $this->mcfx_integrations ) ) {
			$integrations            = [];
			$available_integrations  = scandir( MCFX_WP_INTEGRATIONS_DIR );
			$configured_integrations = get_option( 'mcfx_integrations' );
			if ( empty( $configured_integrations ) || ! is_array( $configured_integrations ) ) {
				$configured_integrations = [];
			}

			// Parse integration files
			foreach ( $available_integrations as $filename ) {

				$integration_file = MCFX_WP_INTEGRATIONS_DIR . DIRECTORY_SEPARATOR . $filename;
				$filename         = basename( $integration_file );
				$file_parts       = explode( '.', $filename );
				$ext              = array_pop( $file_parts );

				// Only allow PHP files given that we will include them directly
				if ( 'php' !== $ext ) {
					continue;
				}

				$integration_data = get_file_data(
					$integration_file,
					[
						'name'        => 'Name',
						'description' => 'Description',
						'link'        => 'Link',
						'slug'        => 'Slug',
					]
				);

				// Autmoatic values and fallbacks
				$integration_data['enabled']  = 0;
				$integration_data['filepath'] = $integration_file;
				if ( empty( $integration_data['slug'] ) ) {
					$integration_data['slug'] = array_shift( $file_parts );
				}
				$file_slug = $integration_data['slug'];
				if ( empty( $integration_data['name'] ) ) {
					$integration_data['name'] = ucwords( preg_replace( '/[^0-9a-z]+/', ' ', $file_slug ) );
				}
				if ( empty( $integration_data['description'] ) ) {
					$integration_data['description'] = $filename;
				}

				$integrations[ $file_slug ] = $integration_data;
			}

			foreach ( $configured_integrations as $config_slug => $config ) {
				$integrations[ $config_slug ] = array_merge( $integrations[ $config_slug ], $config );
			}
			$this->mcfx_integrations = $integrations;
		}

		return $this->mcfx_integrations;
	}

	/**
	 * Get License Key from option table
	 */
	private $license_key = null;
	public function get_license_key() {
		if ( is_null( $this->license_key ) ) {
			// will return false if option doesn't exist
			$this->license_key = get_option( 'webfx_core_services_license_key' );
		}
		return $this->license_key;
	}

	/**
	 * Get License Key Invalidation from option table
	 */
	private $license_key_maybe_invalid = null;
	private function get_license_key_maybe_invalid() {
		if ( is_null( $this->license_key_maybe_invalid ) ) {
			// will return false if option doesn't exist
			$this->license_key_maybe_invalid = get_option( 'webfx_core_services_license_key_maybe_invalid' );
		}
		return $this->license_key_maybe_invalid;
	}

	/**
	 * Mark license key as maybe invalid
	 */
	public function invalidate_license_key() {
		$this->set_license_key_maybe_invalid();
	}

	/**
	 * Set license key validity
	 */
	private function set_license_key_maybe_invalid( $invalid = 1 ) {
		update_option( 'webfx_core_services_license_key_maybe_invalid', $invalid );
		$this->license_key_maybe_invalid = $invalid;

		// Try to initiate a fresh update check
		delete_site_transient( 'update_plugins' );
	}

	/**
	 * See if license key is valid
	 *
	 * @param (bool) $force_check
	 *
	 * @return (array) [ (int) $valid, (array) [ (string) $messages] ]
	 */
	public function is_license_key_valid( $force_check = false ) {

		$license_key = $this->get_license_key();

		// If the key is not set, obviously it's invalid
		if ( empty( $license_key ) ) {
			$this->invalidate_license_key();
			return [ 0, [] ];
		}

		// Check the validation flag
		$license_key_maybe_invalid = $this->get_license_key_maybe_invalid();

		// If the key is not flagged as invalid, then we trust it
		// - Unless method called with force check
		if ( ! $force_check && ! $license_key_maybe_invalid ) {
			return [ 1, [] ];
		}

		$url      = sprintf( self::WP_CORE_SERVICES_URL, 'validate_serial', $license_key, $this->get_channel() );
		$response = wp_remote_get( $url );
		if ( is_wp_error( $response ) ) {
			$errors = [];
			foreach ( $response->errors as $type => $error_list ) {
				$errors = array_merge( $errors, $error_list );
			}
			return [ 0, $errors ];
		}

		// Validate expected type of response
		if (
			empty( $response )
			|| ! is_array( $response )
			|| ! isset( $response['body'] )
		) {
			return [ 0, [ esc_html__( 'Error WCS-WP1 - Validation received unexpected response type', 'webfx' ) ] ];
		}

		// Validate expected type of response body
		$decoded = json_decode( $response['body'], true );
		if (
			empty( $decoded )
			|| ! is_array( $decoded )
			|| ! isset( $decoded['success'] )
			|| ! isset( $decoded['data'] )
			|| ! isset( $decoded['data']['serial_is_valid'] )
		) {
			return [ 0, [ esc_html__( 'Error WCS-WP2 - Validation received unexpected response format', 'webfx' ) ] ];
		}

		// Valid as long as:
		// - request successful
		// - key valid
		$valid = ( $decoded['success'] && $decoded['data']['serial_is_valid'] );

		// If valid - remove invalidation flag
		if ( $valid ) {
			$this->set_license_key_maybe_invalid( 0 );
		}

		if ( ! empty( $decoded['data']['mcfx_id'] ) ) {
			$mcfx_id = $this->get_mcfx_id();
			if ( empty( $mcfx_id ) ) {
				$this->set_mcfx_id( $decoded['data']['mcfx_id'] );
			}
		}

		$messages = isset( $decoded['message'] ) ? [ $decoded['message'] ] : [];
		return [ $valid, $messages ];
	}

	/**
	 * Check if this looks like a live site
	 * - based on database name
	 */
	public function is_live() {
		return ( false === stripos( DB_NAME, 'fxstage' ) && 'project' !== DB_NAME );
	}

	public function init_hooks() {

		// Admin notices
		add_action( 'admin_notices', [ $this, 'admin_notices' ] );

		// Admin scripts
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );

		// Admin settings page
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_menu', [ $this, 'add_menu' ] );

		// Output scripts - only if MCFX tracking is enabled
		$mcfx_script_active = get_option( 'mcfx_script_active' );
		if ( $mcfx_script_active ) {
			add_action( 'wp_head', [ $this, 'header_scripts' ] );
			add_action( 'wp_footer', [ $this, 'footer_scripts' ] );
		}

		// Exclude JS from WP Rocket Optimizations
		add_filter( 'get_rocket_option_exclude_js', [ $this, 'wp_rocket_exclude_js' ] );
		add_filter( 'get_rocket_option_exclude_defer_js', [ $this, 'wp_rocket_exclude_js' ] );
		add_filter( 'get_rocket_option_exclude_inline_js', [ $this, 'wp_rocket_exclude_js' ] );
		add_filter( 'get_rocket_option_delay_js_exclusions', [ $this, 'wp_rocket_exclude_js' ] );
	}

	/**
	 * Queue up admin scripts for specific pages
	 */
	public function admin_enqueue_scripts() {

		// Assets for MCFX Settings page
		if ( $this->is_mcfx_settings_page() ) {

			// Note: This gets merged with core defaults
			//  - See wp-includes/general-template.php::wp_get_code_editor_settings
			$cm_settings['codeEditor'] = wp_enqueue_code_editor(
				[
					'type'   => 'text/html',
					'jshint' => [
						'esversion' => 8, // eg. async

						'es3'       => false, // deprecated
						'immed'     => false, // deprecated
						'quotmark'  => false, // deprecated

						'globals'   => [
							// From core - keeping
							'_'                         => false, // read-only
							'Backbone'                  => false, // read-only
							'jQuery'                    => false, // read-only
							'JSON'                      => false, // read-only
							'wp'                        => false, // read-only
							// New
							'mcfx'                      => false, // read-only
							'mcfxCaptureCustomFormData' => false, // read-only
						],
					],
				]
			);
			wp_localize_script( 'jquery', 'cm_settings', $cm_settings );
			wp_enqueue_script( 'wp-theme-plugin-editor' );
			wp_enqueue_style( 'wp-codemirror' );

			// Replace the WP "fakeJSHINT" (Esprima) with *real* JSHint
			wp_deregister_script( 'esprima' );
			wp_deregister_script( 'jshint' );
			wp_enqueue_script(
				'jshint',
				'https://cdnjs.cloudflare.com/ajax/libs/jshint/2.13.4/jshint.min.js',
				[],
				'2.13.4',
				true
			);

		}

	}

	public function admin_notices() {

		$mcfx_id            = $this->get_mcfx_id();
		$mcfx_script_active = get_option( 'mcfx_script_active' );
		$mcfx_incomplete    = ( $mcfx_script_active && empty( $mcfx_id ) );

		$license_key        = $this->get_license_key();
		$license_incomplete = empty( $license_key );

		// Show an alert if:
		if (
			// Missing either MCFX ID or License Key
			( $license_incomplete || $mcfx_incomplete )
			// and this is a live site
			&& $this->is_live()
			// and we're not on the settings page
			&& ! $this->is_mcfx_settings_page()
		) {
			?>
				<div class="notice notice-warning update-nag">
					<?php echo esc_html_e( 'MCFX Plugin is not configured.', 'webfx' ); ?>
					<a href='<?php echo esc_html( admin_url( 'options-general.php?page=mcfx-wp-settings' ) ); ?>'>
						<?php esc_html_e( 'Click here to configure.', 'webfx' ); ?>
					</a>
				</div>
			<?php
		}
	}

	/**
	 * Check if current admin screen is mcfx settings page
	 */
	public function is_mcfx_settings_page() {
		$admin_screen = get_current_screen();
		return ( 'settings_page_mcfx-wp-settings' === $admin_screen->base );
	}

	/**
	 * Register settings fields
	 */
	public function register_settings() {
		register_setting( 'mcfx_wp_settings', 'call_tracker_active' );
		register_setting( 'mcfx_wp_settings', 'configuration_type' );
		register_setting( 'mcfx_wp_settings', 'custom_footer_scripts' );
		register_setting( 'mcfx_wp_settings', 'custom_mcfx_config' );
		register_setting( 'mcfx_wp_settings', 'mcfx_id' );
		register_setting(
			'mcfx_wp_settings',
			'mcfx_integrations',
			[
				'type'    => 'array',
				'default' => [],
			]
		);
		register_setting( 'mcfx_wp_settings', 'mcfx_script_active', [ 'default' => 1 ] );
		register_setting( 'mcfx_wp_settings', 'pfx_active' );
		register_setting( 'mcfx_wp_settings', 'webfx_core_services_license_key' );
		register_setting( 'mcfx_wp_settings', 'webfx_core_services_license_key_maybe_invalid', [ 'default' => 1 ] );
	}

	/**
	 * MCFX Settings Page
	 */
	public function add_menu() {
		add_submenu_page(
			'options-general.php',
			'MCFX Settings',
			'MCFX Settings',
			'manage_options',
			'mcfx-wp-settings',
			[ $this, 'settings_page' ]
		);
	}
	public function settings_page() {
		require MCFX_WP_ADMIN_TEMPLATES_DIR . DIRECTORY_SEPARATOR . 'mcfx-wp-settings.php';
	}

	/**
	 * Output scripts in header
	 */
	public function header_scripts() {
		// Output main MCFX script
		$mcfx_id = $this->get_mcfx_id();
		if ( ! empty( $mcfx_id ) ) {
			ob_start();
			require MCFX_WP_FRONTEND_TEMPLATES_DIR . DIRECTORY_SEPARATOR . 'scripts-head.php';
			$output = ob_get_clean();
			// Remove blank lines
			$output = preg_replace( "/(^|\n)\s*($|\n)+/", '$1', $output );

			// phpcs:disable WordPress.Security.EscapeOutput
			// - nothing we can escape here - but we trust it all, coming from admin
			echo $output;
			// phpcs:enable WordPress.Security.EscapeOutput
		}
	}

	/**
	 * Output scripts in footer
	 */
	public function footer_scripts() {

		ob_start();
		$integrations = $this->get_mcfx_integrations();
		if ( ! empty( $integrations ) ) {
			require MCFX_WP_FRONTEND_TEMPLATES_DIR . DIRECTORY_SEPARATOR . 'scripts-integrations.php';
		}

		$footer_scripts = get_option( 'custom_footer_scripts' );
		if ( ! empty( $footer_scripts ) ) {
			require MCFX_WP_FRONTEND_TEMPLATES_DIR . DIRECTORY_SEPARATOR . 'scripts-footer.php';
		}
		$output = ob_get_clean();
		// Remove blank lines
		$output = preg_replace( "/(^|\n)\s*($|\n)+/", '$1', $output );

		// phpcs:disable WordPress.Security.EscapeOutput
		// - nothing we can escape here - but we trust it all, coming from admin
		echo $output;
		// phpcs:enable WordPress.Security.EscapeOutput
	}

	/**
	 * Exclude JS from WP Rocket optimizations
	 */
	public function wp_rocket_exclude_js( $exclude ) {
		if ( empty( $exclude ) ) {
			$exclude = [];
		}
		$exclude[] = 'marketingcloudfx';
		$exclude[] = 'mcfx';
		$exclude[] = '(.*)mcfx.js';
		$exclude[] = '(.*)cdn.leadmanagerfx.com/phone/js/(.*)';
		$exclude[] = '(.*)cdn.leadmanagerfx.com/pfx/js/(.*)';
		return array_unique( $exclude );
	}

}

/**
 * Returns the main instance of MCFX_WP to prevent the need to use globals.
 */
function mcfx_wp() {
	return MCFX_WP::instance();
}
add_action( 'plugins_loaded', 'mcfx_wp' );

// IMPORTANT: This plugin is dynamically updated - MODIFICATIONS WILL BE OVERWRITTEN - see https://app.getguru.com/card/T585Kydc
