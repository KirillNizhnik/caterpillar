<?php
// IMPORTANT: This plugin is dynamically updated - MODIFICATIONS WILL BE OVERWRITTEN - see https://app.getguru.com/card/T585Kydc

/**
 * MCFX Auto-login
 *  - Functionality to allow login integration from MCFX
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

final class MCFX_Autologin {

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

		// Primary Functionality
		$this->init_hooks();

	}

	// Ideally would make this higher, but would need to modify what MCFX generates.
	const MINIMUM_TOKEN_LENGTH = 30;

	public function init_hooks() {

		add_action( 'show_user_profile', [ $this, 'user_profile_token_field' ] );
		add_action( 'edit_user_profile', [ $this, 'user_profile_token_field' ] );
		add_action( 'personal_options_update', [ $this, 'update_token' ] );
		add_action( 'edit_user_profile_update', [ $this, 'update_token' ] );
		add_action( 'login_init', [ $this, 'maybe_log_user_in' ] );
		add_action( 'authenticate', [ $this, 'authenticate_token' ], 20, 3 );
		add_action( 'user_profile_update_errors', [ $this, 'pre_update_token' ], 10, 2 );
	}

	public function user_profile_token_field( $user ) {
		$token = get_user_meta( $user->ID, 'mcfx_access_token', true );
		?>
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="mcfx_access_token"><?php esc_html_e( 'MCFX Access Token', 'webfx' ); ?></label></th>
					<td>
						<?php wp_nonce_field( 'mcfx_autologin_update_token', '_mcfx_autologin_nonce' ); ?> 
						<input type="password" name="mcfx_access_token" id="mcfx_access_token" class="regular-text" value="<?php echo esc_html( $token ); ?>" minlength="<?php echo esc_html( self::MINIMUM_TOKEN_LENGTH ); ?>" style="width: 30em;" autocomplete="off"><br>
						<small>
							<em><?php esc_html_e( 'Warning: do not change this unless you know what you\'re doing!', 'webfx' ); ?></em>
							<a href='https://app.webfx.com/marketingcloudfx/<?php echo esc_html( mcfx_wp()->get_mcfx_id() ); ?>/settings/wordpress' target='_blank'><?php esc_html_e( 'Manage token in MCFX', 'webfx' ); ?></a>
						</small>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
	}

	public function pre_update_token( $errors, $update ) {
		
		if ( ! $update ) {
			return;
		}

		check_admin_referer( 'mcfx_autologin_update_token', '_mcfx_autologin_nonce' );

		if ( ! isset( $_POST['mcfx_access_token'] ) ) {
			return;
		}

		$mcfx_access_token = $_POST['mcfx_access_token'];
		$token_length      = strlen( $mcfx_access_token );
		if ( $token_length > 0 && $token_length < self::MINIMUM_TOKEN_LENGTH ) {
			$errors->add( 'mcfx_access_token', '<strong>ERROR</strong>: MCFX Token must be at least ' . self::MINIMUM_TOKEN_LENGTH . ' characters' );
		}
	}

	public function update_token( $user_id ) {

		check_admin_referer( 'mcfx_autologin_update_token', '_mcfx_autologin_nonce' );

		if ( ! current_user_can( 'edit_user', $user_id ) || ! isset( $_POST['mcfx_access_token'] ) ) {
			return false;
		}

		$mcfx_access_token = $_POST['mcfx_access_token'];

		// phpcs:disable Squiz.PHP.CommentedOutCode
		// - WOULD LIKE TO IMPLEMENT THIS IN THE FUTURE
		// Hash it
		// - Can't do this because MCFX is not passing a username ATM
		// $mcfx_access_token = wp_hash_password( $mcfx_access_token );
		// phpcs:enable Squiz.PHP.CommentedOutCode

		// Enforce minimum length, UNLESS it's empty - eg. removing the token completely
		$token_length = strlen( $mcfx_access_token );
		if ( $token_length > 0 && $token_length < self::MINIMUM_TOKEN_LENGTH ) {
			// Ugly error, because these tokens *should* come straight from MCFX
			// Error replaced with client side notification
			// throw new Exception( 'Token is too short - must be at least ' . self::MINIMUM_TOKEN_LENGTH . ' characters' );
			return;
		}

		update_user_meta( $user_id, 'mcfx_access_token', sanitize_text_field( $mcfx_access_token ) );
	}

	/**
	 * Authenticate a login based on username and token
	 */
	public function authenticate_token( $user, $username, $password ) {

		/** START FUNCTIONALITY COPIED FROM WP CORE: wp-includes/user.php::wp_authenticate_application_password **/

		// If already authenticated by another method, no need to continue
		if ( $user instanceof WP_User ) {
			return $user;
		}

		if ( empty( $username ) || empty( $password ) ) {
			if ( is_wp_error( $user ) ) {
				return $user;
			}

			$error = new WP_Error();

			if ( empty( $username ) ) {
				// Uses 'empty_username' for back-compat with wp_signon().
				$error->add( 'empty_username', __( '<strong>Error</strong>: The username field is empty.', 'webfx' ) );
			}

			if ( empty( $password ) ) {
				$error->add( 'empty_password', __( '<strong>Error</strong>: The password field is empty.', 'webfx' ) );
			}

			return $error;
		}

		// Allow *either* username or email
		if ( is_email( $username ) ) {
			$user = get_user_by( 'email', $username );
		} else {
			$user = get_user_by( 'login', $username );
		}

		if ( ! $user ) {
			return new WP_Error(
				__( 'Unknown username / email address. Check again.', 'webfx' )
			);
		}
		/** END FUNCTIONALITY COPIED FROM WP CORE **/

		// Don't bother validating if the token is too short - silently fail
		if ( strlen( $password ) < self::MINIMUM_TOKEN_LENGTH ) {
			return null;
		}

		/** This filter is documented in wp-includes/user.php */
		// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals
		// - we are not calling our own custom action, but rather an existing WP action
		$user = apply_filters( 'wp_authenticate_user', $user, $password );
		// phpcs:enable

		if ( is_wp_error( $user ) ) {
			return $user;
		}

		// Now we know we have a valid user - see if their token matches
		global $wpdb;
		$mcfx_access_token = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM $wpdb->usermeta WHERE meta_key='mcfx_access_token' AND user_id=%s", $user->ID ) );

		// phpcs:disable Squiz.PHP.CommentedOutCode
		// - WOULD LIKE TO IMPLEMENT THIS IN THE FUTURE
		// Check if they are equal first - backward compatibility (no hash)
		// - Can't do this because MCFX is not passing a username ATM
		/*
		if ( $password === $mcfx_access_token ) {
			// Hash it
			$mcfx_access_token = wp_hash_password( $mcfx_access_token );
			// Save it
			$success = update_user_meta( $user->ID, 'mcfx_access_token', sanitize_text_field( $mcfx_access_token ) );
			if ( ! $success ) {
				return new WP_Error(
					'failed_saving_token_hash',
					sprintf(
						__( '<strong>Error</strong>: Failed to secure your login token - see WebFX developer for assistance', 'webfx' ),
						'<strong>' . $username . '</strong>'
					)
				);
			}
		}

		// Check against the hash
		if ( ! wp_check_password( $password, $mcfx_access_token ) ) {
			// We won't throw an error - leave that to other methods - we'll just silently fail
			return null;
		}
		 */
		// phpcs:enable Squiz.PHP.CommentedOutCode

		if ( $password !== $mcfx_access_token ) {
			// We won't throw an error - leave that to other methods - we'll just silently fail
			return null;
		}

		return $user;
	}

	public function maybe_log_user_in() {

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		//  - This value comes from an external request, so nonce does not apply here
		$mcfx_access_token = isset( $_GET['mcfx_access_token'] ) ? $_GET['mcfx_access_token'] : false;
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		if ( ! $mcfx_access_token ) {
			return false;
		}

		// Enforce minimum length - don't bother attempting with token too short
		if ( strlen( $mcfx_access_token ) < self::MINIMUM_TOKEN_LENGTH ) {
			return;
		}

		// Get the user that has this token - fail silently if not found
		global $wpdb;
		$user_id = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM $wpdb->usermeta WHERE meta_key='mcfx_access_token' AND meta_value=%s", $mcfx_access_token ) );
		if ( empty( $user_id ) ) {
			return;
		}
		$user = get_user_by( 'id', $user_id );
		if ( is_wp_error( $user ) ) {
			return;
		}

		// Ideally would like to use wp_signon
		// but, it is not working correctly
		// So, instead, sending a post to the login form directly
		$username = $user->user_nicename;
		require MCFX_WP_ADMIN_TEMPLATES_DIR . DIRECTORY_SEPARATOR . 'mcfx-autologin-form.php';
		exit;
	}
}

/**
 * Returns the main instance of MCFX_Autologin to prevent the need to use globals.
 */
function mcfx_autologin() {
	return MCFX_Autologin::instance();
}
add_action( 'plugins_loaded', 'mcfx_autologin' );

// IMPORTANT: This plugin is dynamically updated - MODIFICATIONS WILL BE OVERWRITTEN - see https://app.getguru.com/card/T585Kydc
