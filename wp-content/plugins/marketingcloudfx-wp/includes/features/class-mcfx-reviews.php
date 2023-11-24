<?php
// IMPORTANT: This plugin is dynamically updated - MODIFICATIONS WILL BE OVERWRITTEN - see https://app.getguru.com/card/T585Kydc

/**
 * MCFX Reviews
 *  - Functionality to import reviews from MCFX into testimonial plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

final class MCFX_Reviews {

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

	const REVIEWS_PER_IMPORT = 50;

	private $utc_timezone = null;

	private function init_hooks() {

		// Admin Tools Page
		add_action( 'admin_menu', [ $this, 'add_menu' ] );

		// Cron hook for automatic import
		add_action( 'mcfx_reviews_cron_import', [ $this, 'cron_import' ] );
	}

	/**
	 * Reviews Import Page
	 */
	public function add_menu() {
		// Only add the import page if the FX Testimonials plugin is present
		if ( class_exists( 'FX_Testimonials' ) ) {
			add_submenu_page(
				'tools.php',
				'MCFX Reviews Import',
				'MCFX Reviews Import',
				'manage_options',
				'mcfx-wp-review-import',
				[ $this, 'tools_page' ]
			);
		}
	}

	public function cron_import() {
		$reviews_response = $this->fetch_reviews();
		$this->import( $reviews_response );
	}

	public function tools_page() {

		$this->authorize_user();

		$reviews_response = $this->fetch_reviews();

		$mcfx_id = mcfx_wp()->get_mcfx_id();

		if ( is_wp_error( $reviews_response ) ) {
			foreach ( $reviews_response->get_error_messages() as $error_message ) {
				echo '<div class="notice notice-error">';
				echo esc_html( $error_message );
				echo '</div>';
			}

			return false;
		}

		$imported = $this->maybe_import( $reviews_response );

		// If import ran and successfully imported at least one review, then freshen the data
		if ( $imported > 0 ) {
			$this->get_testimonials( 'fresh' );
			$this->augment_reviews_response( $reviews_response );
		}

		// Get cron status and update if needed
		$cron_scheduled = $this->maybe_update_cron();

		$display_preferences = $this->get_import_display_preferences();

		$reviews_total_count = count( $reviews_response->reviews );

		// If not showing all, filter the reviews
		if ( $display_preferences['imported'] < 2 ) {
			$reviews_response->reviews = array_filter(
				$reviews_response->reviews,
				function ( $review ) use ( $display_preferences ) {
					return (
						// Showing only new
						( 0 === $display_preferences['imported'] && ! $review->_imported )
						||
						// Showing only imported
						( 1 === $display_preferences['imported'] && $review->_imported )
					);
				}
			);
		}

		// Limit the reviews
		if ( $reviews_total_count > $display_preferences['limit'] ) {
			// Remove everything after the limit
			array_splice( $reviews_response->reviews, $display_preferences['limit'] );
		}

		$reviews_display_count = count( $reviews_response->reviews );

		// Show template of page
		require MCFX_WP_ADMIN_TEMPLATES_DIR . DIRECTORY_SEPARATOR . 'mcfx-reviews-tools.php';
	}

	/**
	 * Get display preferences for manual import table
	 * - Defaults - show REVIEWS_PER_IMPORT new reviews
	 */
	public function get_import_display_preferences() {
		$preferences = [
			// 0 - no, 1 - yes, 2 - both
			'imported' => 0,
			'limit'    => self::REVIEWS_PER_IMPORT,
		];

		if ( empty( $_POST['modify_display_preferences'] ) ) {
			return $preferences;
		}

		check_admin_referer( 'mcfx_modify_display_preferences', '_mcfx_review_nonce' );

		$preferences['imported'] = (int) $_POST['imported'];
		$preferences['limit']    = (int) $_POST['limit'];

		return $preferences;
	}

	/**
	 * Non-friendly quick safety check
	 */
	public function authorize_user() {
		if ( ! current_user_can( 'manage_options' ) && ! defined( 'DOING_CRON' ) ) {
			throw new Exception( 'You should not be here, my friend' );
		}
	}

	/**
	 * Fetch Reviews from MCFX Endpoint
	 *  - Add in _endpoint at top level
	 *  - Add in UID for each review
	 *  - Add in whether imported or not for each review
	 *  @return array of reviews or WP_Error if any issues
	 */
	public function fetch_reviews() {

		$this->authorize_user();

		$mcfx_id = mcfx_wp()->get_mcfx_id();
		if ( empty( $mcfx_id ) ) {
			return new WP_Error( 100, esc_html_e( 'MCFX plugin must be configured first - save client ID on that page, then return to this one', 'webfx' ) . $endpoint );
		}

		$endpoint = 'https://cdn.leadmanagerfx.com/reviews/' . mcfx_wp()->get_mcfx_id();
		$request  = wp_remote_get( $endpoint );

		if ( is_wp_error( $request ) ) {
			return $request;
		}

		$body             = wp_remote_retrieve_body( $request );
		$reviews_response = json_decode( $body );

		if ( empty( $reviews_response ) || ! is_object( $reviews_response ) || empty( $reviews_response->reviews ) ) {
			return new WP_Error( 101, esc_html_e( 'No reviews found at endpoint - ', 'webfx' ) . $endpoint );
		}

		$reviews_response->_endpoint = $endpoint;

		$this->augment_reviews_response( $reviews_response );

		return $reviews_response;

	}

	public function augment_reviews_response( $reviews_response ) {
		foreach ( $reviews_response->reviews as &$review ) {
			$review->_uid      = $this->get_review_uid( $review );
			$review->_imported = $this->testimonial_exists( $review->_uid );
		}
	}

	/**
	 * Get all testimonials imported by the plugin, keyed by UID
	 */
	protected $testimonials = null;
	public function get_testimonials( $fresh = false ) {
		if ( $fresh || is_null( $this->testimonials ) ) {
			$this->testimonials = [];

			$testimonial_posts = get_posts(
				[
					'post_type'   => 'testimonial',
					'numberposts' => -1,
					// allow admin to trash a post to keep it from re-importing
					'post_status' => [ 'any', 'inherit', 'trash', 'auto-draft' ],
				]
			);

			foreach ( $testimonial_posts as $testimonial ) {

				// Other metadata, if wanted:
				// - client_name
				// - location
				// - testimonial_content

				$uid = get_post_meta( $testimonial->ID, 'mcfx_rafx_uid', true );

				// Skip over testimonials not imported by our plugin
				if ( empty( $uid ) ) {
					continue;
				}

				$testimonial->mcfx_rafx_uid = $uid;

				$this->testimonials[ $uid ] = $testimonial;

			}
		}
		return $this->testimonials;
	}

	/**
	 * Check if a testimonial exists with a given uid or review object
	 */
	public function testimonial_exists( $uid_or_review ) {

		$uid = is_object( $uid_or_review ) ? $this->get_review_uid( $uid_or_review ) : $uid_or_review;

		$testimonials = $this->get_testimonials();

		return ( isset( $testimonials[ $uid ] ) );
	}

	/**
	 * Maybe update cron
	 *  - Verifies intent to adjust status
	 *  - Verifies nonce
	 *  - Adjusts status
	 * @return cron status - after changes if applicable
	 */
	public function maybe_update_cron() {

		$this->authorize_user();

		$scheduled = wp_next_scheduled( 'mcfx_reviews_cron_import' );
		$changed   = false;

		if ( empty( $_POST['enable_cron'] ) && empty( $_POST['disable_cron'] ) ) {
			return $scheduled;
		}

		check_admin_referer( 'mcfx_adjust_review_import_cron', '_mcfx_review_nonce' );

		// Enable cron if requested and not already scheduled
		if ( ! $scheduled && ! empty( $_POST['enable_cron'] ) ) {
			wp_schedule_event( time(), 'hourly', 'mcfx_reviews_cron_import' );
			$changed = true;
		}

		// Disable cron if requested and not already disabled
		if ( $scheduled && ! empty( $_POST['disable_cron'] ) ) {
			wp_unschedule_event( $scheduled, 'mcfx_reviews_cron_import' );
			$changed = true;
		}

		// Refresh to confirm next scheduled
		if ( $changed ) {
			$scheduled = wp_next_scheduled( 'mcfx_reviews_cron_import' );
		}

		return $scheduled;

	}

	/**
	 * Maybe import reviews
	 *  - Verifies intent to import
	 *  - Verifies nonce
	 *  - Imports reviews
	 *  @return number of reviews imported, or false if no import attempted
	 */
	public function maybe_import( $reviews_response ) {

		$this->authorize_user();

		if ( empty( $_POST['import'] ) ) {
			return false;
		}

		check_admin_referer( 'mcfx_import_reviews', '_mcfx_review_nonce' );

		return $this->import( $reviews_response );
	}

	/**
	 * Import reviews from reviews response object
	 */
	public function import( $reviews_response ) {
		$imported_count = 0;
		foreach ( $reviews_response->reviews as $review ) {

			$uid = $this->get_review_uid( $review );

			if ( ! $this->testimonial_exists( $uid ) ) {

				$post_data = [
					'post_title'   => $review->author_name,
					'post_content' => $review->text,
					'post_status'  => 'publish',
					'post_type'    => 'testimonial',
					'post_date'    => $this->get_review_date( $review ),
					'meta_input'   => [
						'client_name'         => $review->author_name,
						'location'            => $review->location,
						'testimonial_content' => $review->text,
						'mcfx_rafx_uid'       => $uid,
						'review_site'         => $review->site,
						'review_url'          => $review->review_url,
						'review_rating'       => $review->rating,
					],
				];

				wp_insert_post( apply_filters( 'mcfx_newly_imported_testimonial', $post_data, $review ) );

				$imported_count++;

				if ( $imported_count >= self::REVIEWS_PER_IMPORT ) {
					break;
				}
			}
		}

		return $imported_count;
	}

	/**
	 * Get Review Date - UTC stamped and formatted
	 */
	public function get_review_date( $review ) {
		$date = new DateTime( $review->date, $this->utc_timezone );
		return $date->format( 'Y-m-d H:i:s' );
	}

	/**
	 * Get UID of Review - attempt to identify reviews by:
	 *  - Author Name
	 *  - Date
	 *  - Rating
	 *  - Location
	 *  - First 50 characters of the review
	 */
	public function get_review_uid( $review ) {
		return $review->author_name . ' | ' .
			$this->get_review_date( $review ) . ' | ' .
			$review->rating . ' | ' .
			$review->location . ' | ' .
			substr( $review->text, 0, 50 );
	}

}

/**
 * Returns the main instance of MCFX_WP to prevent the need to use globals.
 *
 * @since  1.0
 * @return MCFX_WP
 */
function mcfx_reviews() {
	return MCFX_Reviews::instance();
}
add_action( 'plugins_loaded', 'mcfx_reviews' );

// IMPORTANT: This plugin is dynamically updated - MODIFICATIONS WILL BE OVERWRITTEN - see https://app.getguru.com/card/T585Kydc
