<?php

/**
 * Plugin Name: 	FX CF7 Redirect
 * Plugin URI: 		https://webfx.com
 * Description: 	Adds simple redirect functionality to CF7
 * Version: 		1.1.0
 * Author: 			The WebFX Team
 * Author URI: 		https://webfx.com
 */

defined( 'ABSPATH' ) || exit;

if( !class_exists( 'FX_CF7_Redirect' ) ):

	final class FX_CF7_Redirect
	{
		protected static $instance = null;

		private static $meta_key_redirect_url 	= '_fx_cf7_redirect_url';
		private static $meta_key_extra_js 		= '_fx_cf7_extra_js';
		

		public static function instance() 
		{
			if( is_null( self::$instance ) )
				self::$instance = new self();

			return self::$instance;
		}


		public function __construct() 
		{
			add_action( 'admin_notices', 		[ $this, 'install_check' ] );
			add_action( 'wpcf7_editor_panels', 	[ $this, 'add_meta_box' ] );
			add_action( 'wpcf7_after_save', 	[ $this, 'save_panel_values' ] );
			add_action( 'wp_footer', 			[ $this, 'add_redirection_script' ] );
		}


		/**
		 * Check that CF7 is installed, active, and at least version 4.9
		 *
		 * @return	void
		 */
		public function install_check() 
		{
			$error = '';
			if( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
				if( ( defined( 'WPCF7_VERSION' ) ) ) {
					if( 4.9 > WPCF7_VERSION )
						$error = 'Please upgrade Contact Form 7 to at least <strong>version 4.9</strong> to enable custom redirects.';
				} else {
					$error = 'Your version of Contact Form 7 could not be determined and may have been corrupted. Please update or reinstall Contact Form 7.';
				}
			} else {
				$error = 'To use custom Contact Form 7 redirects, the base Contact Form 7 plugin must be installed and active.';
			}

			if( !empty( $error ) )
				printf( '<div class="error"><p><strong>Warning:</strong> %s</p></div>', $error );
		}	


		/**
		 * Add custom admin panel to CF7 frame
		 *
		 * @param	array 	$panels 	Admin panels
		 * @return	array 				Admin panels
		 */
		public function add_meta_box( $panels ) 
		{
			$panels['custom-redirects'] = [
				'title'    => 'Custom Redirects',
				'callback' => [ $this, 'add_panel_fields' ],
			];

			return $panels;
		}	


		/**
		 * Add fields to panel
		 *
		 * @param	object 	$form 	CF7 form
		 * @return	void
		 */
		public function add_panel_fields( $form ) 
		{
			// Display values if already set
			$redirect_url = get_post_meta( $form->id(), self::$meta_key_redirect_url, true );
			$extra_js     = get_post_meta( $form->id(), self::$meta_key_extra_js, true );
			$class        = '';

			// Validate our Redirect URL
			if( !empty( $redirect_url ) && !filter_var( $redirect_url, FILTER_VALIDATE_URL ) )
				$class = 'url-invalid';

			?>

				<h3>Custom Redirect URL</h3>
				<fieldset>
					<legend>Enter URL for form to redirect to upon successful submission (i.e. <em>http://www.example.com/thank-you/</em>).</legend>
					<input type="text" id="redirect_url" name="fx_cf7_redirect_url" class="large-text <?php echo esc_attr( $class ); ?>" size="70" value="<?php echo esc_url( $redirect_url ); ?>" placeholder="http://www.example.com/thank-you/">
				</fieldset>

				<h3 style="margin-top: 1.66em">Custom JS</h3>
				<fieldset>
					<legend>Enter any custom JS needed to run before redirect <em>(do not wrap in &lt;script&gt; tags)</em>.</legend>
					<textarea id="extra_js" name="fx_cf7_extra_js" class="large-text" cols="100" rows="4" placeholder="ga( 'send', 'event', 'Contact Form', 'submit' );"><?php echo $extra_js; ?></textarea>
				</fieldset>
				<script>
					(function( $ ) {
						$('input.url-invalid').css('border', '1px solid red');
						$('input.url-invalid').after('<p style="margin-top:.25em; color:red;">Please use a full URL (i.e. <em>http://www.example.com/thank-you/</em>)</p>');
					})( jQuery );
				</script>

			<?php
		}	


		/**
		 * Save panel values when updating form
		 *
		 * @param	object	$form 	CF7 form
		 * @return	void
		 */
		public function save_panel_values( $form )
		{
			$form_id 		= $form->id();
			$redirect_url   = $_POST['fx_cf7_redirect_url'] ?? '';
			$extra_js       = $_POST['fx_cf7_extra_js'] ?? '';

			if( !empty( $redirect_url ) )
				$redirect_url = esc_url_raw( $redirect_url );

			// Update each time (in case user uses blank value)
			update_post_meta( $form_id, self::$meta_key_redirect_url, $redirect_url );
			update_post_meta( $form_id, self::$meta_key_extra_js, $extra_js );
		}


		/**
		 * Output redirection script in footer
		 *
		 * @return	void
		 */
		public function add_redirection_script()
		{
			$forms = get_posts(
				[
					'post_status'		=> 'publish',
					'post_type'			=> 'wpcf7_contact_form',
					'posts_per_page'	=> -1,
				]
			);

			$form_data = [];
			foreach( $forms as $form ) {
				$form_id 		= $form->ID;
				$redirect_url 	= get_post_meta( $form_id, self::$meta_key_redirect_url, true );
				$extra_js     	= get_post_meta( $form_id, self::$meta_key_extra_js, true );
				
				if( !empty( $redirect_url ) || !empty( $extra_js ) ) {
					$form_data[ $form_id ] = [
						'redirect_url'	=> $redirect_url,
						'extra_js'		=> $extra_js
					];
				}
			}

			?>

			<?php if( !empty( $form_data ) ): ?>
				<script type="text/javascript">
					document.addEventListener( 'wpcf7mailsent', function( e ) {
					    console.log(e);
						<?php foreach( $form_data as $id => $meta ): ?>
							if( <?php echo $id; ?> === parseInt( e.detail.contactFormId ) ) {
								<?php if( !empty( $meta['extra_js'] ) ) echo $meta['extra_js'] . "\n"; ?>
								<?php if( !empty( $meta['redirect_url'] ) ): ?>
									var redirectUrl 	= '<?php echo $meta['redirect_url']; ?>',
										downloadField 	= e.detail.inputs.filter( function( field ) {
											return ( 'downloadurl' === field.name )
										})

									if( downloadField.length ) {
										var downloadAddon = downloadField[0].value

										if( downloadAddon.length )
											redirectUrl = redirectUrl + '?filefx=' + downloadAddon
									}

									if( redirectUrl.length )
										location = redirectUrl
								<?php endif; ?>
							}
						<?php endforeach; ?>
					}, false )
				</script>
			<?php endif; ?>

			<?php
		}

	}

	function FX_CF7_Redirect() {
		return FX_CF7_Redirect::instance();
	}

	FX_CF7_Redirect();

endif;