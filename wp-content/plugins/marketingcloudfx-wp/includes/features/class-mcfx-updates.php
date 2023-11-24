<?php
// IMPORTANT: This plugin is dynamically updated - MODIFICATIONS WILL BE OVERWRITTEN - see https://app.getguru.com/card/T585Kydc

/**
 * MCFX Updates
 *  - Functionality to support dynamically updating WebFX tools, including the MCFX plugin itself
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

final class MCFX_Updates {

	/**
	* Singleton pattern
	*  - This makes working with WP Hooks fairly nice
	*/
	protected static $instance = null;
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

		$this->utc_timezone = new DateTimezone( 'UTC' );

		// Primary Functionality
		$this->init_hooks();

	}

	// Default plugin information structure
	private static $plugin_info_default = [

		'id'            => '', // Set based on local plugin
		'plugin'        => '', // Set based on local plugin
		'slug'          => '', // Set based on local plugin

		'date'          => '', // From WP_CORE_SERVICES_URL
		'file_name'     => '', // From WP_CORE_SERVICES_URL
		'package'       => '', // From WP_CORE_SERVICES_URL
		'url'           => '', // From WP_CORE_SERVICES_URL
		'version'       => '', // From WP_CORE_SERVICES_URL

		// Mock defaults
		'icons'         => [],
		'banners'       => [],
		'banners_rtl'   => [],
		'tested'        => '',
		'requires_php'  => '',
		'compatibility' => null,

		// Possible future additions:
		/*
			- requires (WP version)
			- tested (WP version)

			- author (dynamic vs. coded into plugin)
			- homepage (dynamic vs. coded into plugin)
			- downloaded (count)
		 */
	];

	// Errors encountered while trying to fetch updates
	private $errors = [];

	// Debug flag for plugin updates
	// - Use ?mcfx_debug_plugin_updates on plugin page to force update check on page load
	// - Use ?mcfx_debug_plugin_updates=output to output transient data for debugging
	private $debug_plugin_updates = null;

	private function init_hooks() {

		// Plugin Updates

		// - Normal check
		add_action( 'pre_set_site_transient_update_plugins', [ $this, 'site_transient_update_plugins' ] );

		// On-demand update check

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		//  - Basic query string flag, nonce doesn't apply
		//  - Filtering just in case
		$this->debug_plugin_updates = filter_input( INPUT_GET, 'mcfx_debug_plugin_updates', FILTER_SANITIZE_STRING );
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		if ( ! is_null( $this->debug_plugin_updates ) ) {
			// - Checks on every page load - useful for testing
			add_action( 'site_transient_update_plugins', [ $this, 'site_transient_update_plugins' ] );
		}

		// Plugins API - override normal request for plugin information
		add_action( 'plugins_api', [ $this, 'plugins_api' ], 10, 3 );
	}

	/**
	 * Return custom information for plugins
	 */
	public function plugins_api( $res, $action, $args ) {

		if ( 'plugin_information' !== $action ) {
			return $res;
		}

		$plugin_slug = empty( $args->slug ) ? false : $args->slug;
		if ( empty( $plugin_slug ) ) {
			return $res;
		}

		$remote_plugin_list = $this->get_remote_plugin_information();
		$updatable_plugins  = $this->get_updatable_plugins();
		if ( empty( $updatable_plugins[ $plugin_slug ] ) || empty( $updatable_plugins[ $plugin_slug ]['_plugin_full_data'] ) ) {
			return $res;
		}

		// Fill in remote release notes if we have them
		if (
			is_array( $remote_plugin_list )
			&& isset( $remote_plugin_list[ $plugin_slug ] )
			&& isset( $remote_plugin_list[ $plugin_slug ]['info'] )
			&& isset( $remote_plugin_list[ $plugin_slug ]['info']['release_notes_content'] )
		) {
			$updatable_plugins[ $plugin_slug ]['_plugin_full_data']['Sections']['changelog'] = $remote_plugin_list[ $plugin_slug ]['info']['release_notes_content'];
		}

		$res = new stdClass();
		foreach ( $updatable_plugins[ $plugin_slug ]['_plugin_full_data'] as $key => $value ) {
			$lower_key       = strtolower( $key );
			$res->$lower_key = $value;
		}

		return $res;
	}

	/**
	 * Update plugin update data to include custom plugins available for update
	 */
	public function site_transient_update_plugins( $transient ) {

		$remote_plugin_list = $this->get_remote_plugin_information();
		$updatable_plugins  = $this->get_updatable_plugins();

		foreach ( $updatable_plugins as $plugin_slug => $local_plugin_info ) {

			$plugin_id = $local_plugin_info['id'];

			// Remove any pre-existing data
			unset( $transient->response[ $plugin_id ] );
			unset( $transient->no_update[ $plugin_id ] );

			// Skip any plugins where we couldn't get the remote data for some reason
			$remote_plugin_data = ( is_array( $remote_plugin_list ) && isset( $remote_plugin_list[ $plugin_slug ] ) ) ? $remote_plugin_list[ $plugin_slug ] : [];
			if (
			empty( $remote_plugin_data )
			|| ! is_array( $remote_plugin_data )
			|| empty( $remote_plugin_data['success'] )
			|| empty( $remote_plugin_data['info'] )
			|| empty( $remote_plugin_data['info']['version'] )
			) {
				$message = empty( $remote_plugin_data['message'] ) ? 'Error WCS-WP9 - Failed to obtain version for plugin ' . $plugin_slug : $remote_plugin_data['message'];
				$this->add_error( $message );
				continue;
			}
			$remote_plugin_info = $remote_plugin_data['info'];

			// Merge in remote info with local info and add to transient
			$response_data = array_merge( $local_plugin_info, $remote_plugin_info );

			// Fill in some defaults
			if ( is_null( $response_data['compatibility'] ) ) {
				$response_data['compatibility'] = new stdClass();
			}
			if ( empty( $response_data['new_version'] ) ) {
				$response_data['new_version'] = $response_data['version'];
			}
			if ( empty( $response_data['icons'] ) ) {
				$response_data['icons'] = [
					'1x'      => MCFX_WP_ASSETS_IMG_URL . '/icon-128×128.jpg',
					'2x'      => MCFX_WP_ASSETS_IMG_URL . '/icon-256×256.jpg',
					'default' => MCFX_WP_ASSETS_IMG_URL . '/icon-256×256.jpg',
				];
			}

			$local_version  = $local_plugin_info['version'];
			$remote_version = $remote_plugin_info['version'];

			if ( version_compare( $remote_version, $local_version, '>' ) ) {
				// If the remote version is newer, add data to the updates list
				$transient->response[ $plugin_id ] = (object) $response_data;
			} else {
				// Otherwise, add data to the no_update list
				$transient->no_update[ $plugin_id ] = (object) $response_data;
			}
		}

		if ( 'output' === $this->debug_plugin_updates ) {
			// phpcs:disable WordPress.PHP.DevelopmentFunctions
			// - We're explicitly using var_dump for debugging - under a flag
			?>

			<?php if ( ! empty( $this->errors ) ) : ?>
				<h1>Errors</h1>
					<?php foreach ( $this->errors as $error ) : ?>
					<p><b> - <?php echo esc_html( $error ); ?></b></p>
				<?php endforeach ?>
			<?php endif ?>

			<h1>Remote Plugin List</h1>
			<pre><?php var_dump( $remote_plugin_list ); ?></pre>

			<h1>Updatable Plugins</h1>
			<pre><?php var_dump( $updatable_plugins ); ?></pre>

			<h1>Updated Transient</h1>
			<pre><?php var_dump( $transient ); ?></pre>

			<?php
			// phpcs:enable WordPress.PHP.DevelopmentFunctions
			die;
		}

		return $transient;
	}

	/**
	 * Get remote information for all updatable plugins
	 */
	private $remote_plugin_information = null;
	private function get_remote_plugin_information() {
		if ( is_null( $this->remote_plugin_information ) ) {

			$this->remote_plugin_information = $this->fresh_get_remote_plugin_information();

		}
		return $this->remote_plugin_information;
	}

	/**
	 * This method is not to be called except by get_remote_plugin_information
	 */
	private function fresh_get_remote_plugin_information() {
		$license_key       = mcfx_wp()->get_license_key();
		$channel           = mcfx_wp()->get_channel();
		$updatable_plugins = $this->get_updatable_plugins();

		if ( empty( $updatable_plugins ) ) {
			$this->add_error( 'Error WCS-WP3 - Getting available updates failed - 0 updatable plugins. But, MCFX itself is expected to be updatable at minimum.  Perhaps this was intentionally disabled?' );
			return [];
		}

		$plugin_slugs    = array_keys( $updatable_plugins );
		$plugin_versions = [];
		foreach ( $updatable_plugins as $key => $value ) {
			$plugin_versions[ $key ] = isset( $value['version'] ) ? $value['version'] : 'unknown';
		}

		$url  = sprintf( MCFX_WP::WP_CORE_SERVICES_URL, 'get_available_updates', $license_key, $channel );
		$url .= '&' . http_build_query(
			[
				'plugins'         => $plugin_slugs,
				'plugin_versions' => $plugin_versions,
				'domain'          => site_url(),
			]
		);

		$response = wp_remote_get( $url );
		if ( is_wp_error( $response ) ) {

			$full_error = 'Error WCS-WP4 - Getting available updates failed';

			$errors = [];
			foreach ( $response->errors as $type => $error_list ) {
				$errors = array_merge( $errors, $error_list );
			}

			foreach ( $errors as $error ) {
				$full_error .= "\n - " . $error;
			}

			$this->add_error( $full_error );
			return [];
		}

		// Validate expected type of response
		if (
			empty( $response )
			|| ! is_array( $response )
			|| ! isset( $response['body'] )
		) {
			$this->add_error( 'Error WCS-WP5 - Getting available updates failed - received unexpected response type' );
			return [];
		}

		$decoded = json_decode( $response['body'], true );

		// Validate expected type of response body
		if (
			empty( $decoded )
			|| ! is_array( $decoded )
			|| ! isset( $decoded['success'] )
			|| ! isset( $decoded['data'] )
			|| ! isset( $decoded['data']['serial_is_valid'] )
		) {
			$this->add_error( 'Error WCS-WP6 - Getting available updates failed - received unexpected response format' );
			return [];
		}

		if ( ! $decoded['data']['serial_is_valid'] ) {
			$this->add_error( 'Error WCS-WP7 - Getting available updates failed - license key is invalid' );
			return [];
		}

		if (
			empty( $decoded['data']['plugins'] )
		) {
			$this->add_error( 'Error WCS-WP10 - Getting available updates failed - received unexpected response format' );
			return [];
		}

		// Try and get remote release notes, if none already set
		foreach ( $decoded['data']['plugins'] as $plugin_slug => $plugin_data ) {
			$plugin_info = empty( $plugin_data['info'] ) ? [] : $plugin_data['info'];
			if ( empty( $plugin_info['release_notes_content'] ) ) {
				$release_notes = $this->get_release_notes( $plugin_slug, $plugin_info );
				if ( ! empty( $release_notes ) ) {
					$decoded['data']['plugins'][ $plugin_slug ]['info']['release_notes_content'] = $release_notes;
				}
			}
		}

		return $decoded['data']['plugins'];
	}

	/**
	 * Collect plugin slugs for WebFX plugins that support updates
	 */
	private $updatable_plugins = null;
	private function get_updatable_plugins() {
		if ( is_null( $this->updatable_plugins ) ) {
			$updatable_plugin_files  = apply_filters( 'mcfx_webfx_wp_core_services_updatable_plugins', [] );
			$this->updatable_plugins = [];
			foreach ( $updatable_plugin_files as $plugin_file ) {

				if ( ! is_file( $plugin_file ) || ! is_readable( $plugin_file ) ) {
					$this->add_error( "Error WCS-WP11 - File '$plugin_file' was passed to mcfx_webfx_wp_core_services_updatable_plugins - but the file does not exist or can't be read" );
				}

				$plugin_dir = dirname( $plugin_file );

				// Get unique plugin identifier used by WP
				$plugin_basename = plugin_basename( $plugin_file );

				// Parse the information about this plugin from it's doc block
				$plugin_data = get_plugin_data( $plugin_file );

				// We'll use the plugin's directory name as it's slug
				//  - WP (CMS) doesn't have a true slug for custom plugins - only .org enforces that
				//  - So, this is our own convention, to identify plugins uniquely with our update service
				$plugin_slug = wp_basename( $plugin_dir );

				// Build up default plugin data
				if ( empty( $plugin_data['Slug'] ) ) {
					$plugin_data['Slug'] = $plugin_slug;
				}
				if ( empty( $plugin_data['Banners'] ) ) {
					$plugin_data['Banners']['low'] = MCFX_WP_ASSETS_IMG_URL . '/banner.png';
				}
				if ( empty( $plugin_data['Sections'] ) ) {
					$plugin_data['Sections'] = [];
				}
				if ( empty( $plugin_data['Sections']['description'] ) && ! empty( $plugin_data['Description'] ) ) {
					$plugin_data['Sections']['description'] = '<p>' . $plugin_data['Description'] . '</p>';
				}
				if ( empty( $plugin_data['Sections']['changelog'] ) ) {
					$release_notes_file = 'release_notes.md';
					if ( ! empty( $plugin_data['Release Notes'] ) ) {
						$release_notes_file = $plugin_data['Release Notes'];
					}

					$release_notes_file = $plugin_dir . DIRECTORY_SEPARATOR . $release_notes_file;
					if ( is_readable( $release_notes_file ) ) {

						// phpcs:disable WordPress.WP.AlternativeFunctions
						// - we're OK with file_get_contents here - it's a local path
						$markdown = file_get_contents( $release_notes_file );
						// phpcs:enable WordPress.WP.AlternativeFunctions

						$html = $this->markdown_to_html( $markdown );

						$plugin_data['Sections']['changelog'] = $html;
					}
				}

				// Build up the info array
				$plugin_info = self::$plugin_info_default;

				$plugin_info['id']                    = $plugin_basename;
				$plugin_info['slug']                  = $plugin_slug;
				$plugin_info['plugin']                = $plugin_basename;
				$plugin_info['_plugin_full_filepath'] = $plugin_file;
				$plugin_info['_plugin_full_data']     = $plugin_data;

				if ( isset( $plugin_data['Version'] ) ) {
					$plugin_info['version'] = $plugin_data['Version'];
				} else {
					$this->add_error( 'Error WCS-WP8 - Plugin ' . $plugin_basename . ' does not include a version in its docblock' );
				}

				$this->updatable_plugins[ $plugin_slug ] = $plugin_info;
			}
		}

		return $this->updatable_plugins;
	}

	/**
	 * Attempt to get Release Notes remotely
	 */
	private $release_notes = [];
	private function get_release_notes( $plugin_slug, $plugin_info ) {
		if ( ! isset( $this->release_notes[ $plugin_slug ] ) ) {
			$release_notes = false;

			if ( ! empty( $plugin_info['release_notes_url'] ) ) {
				$response = wp_remote_get( $plugin_info['release_notes_url'] );

				if ( is_array( $response ) && ! empty( $response['body'] ) ) {
					$html                                = $this->markdown_to_html( $response['body'] );
					$this->release_notes[ $plugin_slug ] = $html;
				}
			}
		}
		return $this->release_notes[ $plugin_slug ];
	}

	/**
	 * Add general error to output regarding updates
	 */
	private function add_error( $error ) {

		$this->errors[] = $error;

		// Ideally should improve on this - ideally show in interface somehow

		// phpcs:disable  WordPress.PHP.DevelopmentFunctions
		error_log( $error );
		// phpcs:enable  WordPress.PHP.DevelopmentFunctions
	}

	/**
	 * Try to parse Markdown - fall back to pre-tag-wrapped text
	 */
	private function markdown_to_html( $markdown ) {
		$pre_fallback = '<pre>' . $markdown . '</pre>';
		$html         = '';

		try {
			if ( ! class_exists( '\WebFX\MCFX\External\Parsedown' ) ) {
				require_once MCFX_WP_INCLUDES_DIR . DIRECTORY_SEPARATOR . 'third-party' . DIRECTORY_SEPARATOR . 'Parsedown.php';
			}
			$parsedown = new \WebFX\MCFX\External\Parsedown();
			$html      = $parsedown->text( $markdown );
		} catch ( Exception $e ) {
			$html = $pre_fallback; // Silently fall back
		}

		if ( empty( $html ) ) {
			$html = $pre_fallback; // Silently fall back
		}

		return $html;
	}

}

/**
 * Returns the main instance of MCFX_WP to prevent the need to use globals.
 *
 * @since  1.0
 * @return MCFX_WP
 */
function mcfx_updates() {
	return MCFX_Updates::instance();
}
add_action( 'plugins_loaded', 'mcfx_updates' );

// IMPORTANT: This plugin is dynamically updated - MODIFICATIONS WILL BE OVERWRITTEN - see https://app.getguru.com/card/T585Kydc
