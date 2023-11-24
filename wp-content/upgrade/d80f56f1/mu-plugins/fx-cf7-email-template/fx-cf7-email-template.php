<?php
/**
 * Plugin Name: FX Contact Form 7 Email Template
 * Plugin URI: https://www.webfx.com
 * Description: Adds Built-in Contact Form 7 Email Template Functionality
 * Version: 1.2
 * Author: The WebFX Team
 * Author URI: https://www.webfx.com
 * Documentation: https://webpagefx.mangoapps.com/mlink/wiki/NjQwNjg
 * Text Domain: webfx
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'FX_CF7_Email_Template' ) ) :

	final class FX_CF7_Email_Template {

		public $version            = '1.2';
		protected $debug           = false;
		protected static $instance = null;

		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'setup' ) );
		}

		public function setup() {
			// Check that CF7 is installed, active, and at least version 4.9
			add_action( 'admin_notices', array( $this, 'fx_cf7_install_check' ) );

			// Add our special panel
			add_action( 'wpcf7_editor_panels', array( $this, 'fx_cf7_add_meta_box' ) );

			// Save our special data
			add_action( 'wpcf7_after_save', array( $this, 'fx_cf7_save_template_data' ) );

			// Add our inputs to a script in footer
			add_action( 'admin_enqueue_scripts', array( $this, 'fx_cf7_assets_addon' ) );
		}

		public function fx_cf7_install_check() {
			if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
				if ( ( defined( 'WPCF7_VERSION' ) ) ) {
					if ( WPCF7_VERSION < 4.9 ) {
						echo '<div class="error"><p><strong>Warning:</strong> Please upgrade Contact Form 7 to at least <strong>version 4.9</strong> to enable custom email templates.</p></div>';
					}
				} else {
					echo '<div class="error"><p><strong>Warning:</strong> Your version of Contact Form 7 could not be determined and may have been corrupted. Please update or reinstall Contact Form 7.</p></div>';
				}
			} else {
				echo '<div class="error"><p><strong>Warning:</strong> To use custom Contact Form 7 email templates, the base Contact Form 7 plugin must be installed and active.</p></div>';
			}
		}

		public function fx_cf7_add_meta_box( $panels ) {
			$panels['email-template'] = array(
				'title'    => 'Customize Email Template',
				'callback' => array( $this, 'fx_cf7_email_template_fields' ),
			);
			return $panels;
		}

		/**
		 * Add our special fields
		 */
		public function fx_cf7_email_template_fields( $post ) {

			// Grab values if already set
            $email_template_data = get_post_meta( $post->id(), '_email_template_data', true );
            $email_template_code = get_post_meta( $post->id(), '_email_template_code', true );

            // Grab defaults so they can be used if needed
            if ( file_exists( __DIR__ . '/data/data.json' ) ) {
                $default_email_template_data = file_get_contents( __DIR__ . '/data/data.json' );
            }
            if ( file_exists( __DIR__ . '/data/template.handlebar.html' ) ) {
                $default_email_template_code = file_get_contents( __DIR__ . '/data/template.handlebar.html' );
            }
            ?>
            <div id="FX-CF7-ET">

                <p>You may use these mail-tags anywhere to pull in data from the form: <br><?php $post->suggest_mail_tags( 'mail' ); ?></p>

                <h2 class="FX-CF7-ET-preview-title">Email Preview</h2>
                <button type="button" class="FX-CF7-ET-admin-view-btn switch-view-btn active" data-view="admin" title="The message that will be sent to the admin" >Admin Preview - Mail</button>
                <button type="button" class="FX-CF7-ET-customer-view-btn switch-view-btn" data-view="customer" title="The message that will be sent to the customer">Customer Preview - Mail (2)</button>
                <button type="button" class="FX-CF7-ET-global-btn" data-editable="global" style="display:none;">EDIT GLOBAL OPTIONS</button>
                <div class="FX-CF7-ET-preview-wrapper">
                    <div id="FX-CF7-ET-preview" class="FX-CF7-ET-preview">
                        <!-- Preview Will Render Here -->
                    </div>
                    <div id="FX-CF7-ET-actions" class="FX-CF7-ET-actions" style="display: none"><button type="button">CLICK TO EDIT</button></div>
                </div>

                <h2 class="FX-CF7-ET-code-title" style="margin-top: 60px;">Email Template</h2>
                <button type="button" class="FX-CF7-ET-defaults-btn">LOAD DEFAULTS</button>
                <div class="FX-CF7-ET-code">
                    <h3 class="FX-CF7-ET-code-title">Data</h3>
                    <textarea id="FX-CF7-ET-editor-data" name="email_template_data" class="large-text" cols="100" rows="10" placeholder=""><?php echo $email_template_data; ?></textarea>
                    <h3 class="FX-CF7-ET-code-title">Code</h3>
                    <textarea id="FX-CF7-ET-editor-code" name="email_template_code" class="large-text" cols="100" rows="10" placeholder=""><?php echo $email_template_code; ?></textarea>
                </div>

                <div id="FX-CF7-ET-modal" class="FX-CF7-ET-modal FX-CF7-ET-modal--hidden">
                    <h3 class="FX-CF7-ET-modal-title">Edit Block Values</h3>
                    <button type="button" class="FX-CF7-ET-modal-close">&times;</button>
                    <div id="FX-CF7-ET-form" class="FX-CF7-ET-form">
                        <h4 id="FX-CF7-ET-form-header" class="FX-CF7-ET-form-header"></h4>
                        <div id="FX-CF7-ET-form-content" class="FX-CF7-ET-form-content"></div>
                        <button type="button" id="FX-CF7-ET-new-field" style="display: none;">Add New Field</button>
                        <div class="FX-CF7-ET-form-footer">
                            <button type="button" id="FX-CF7-ET-form-submit" class="FX-CF7-ET-form-submit">Update</button>
                        </div>
                    </div>
                </div>

            </div>
            <textarea id="FX-CF7-ET-default-editor-data" style="display:none;"><?php echo $default_email_template_data; ?></textarea>
            <textarea id="FX-CF7-ET-default-editor-code" style="display:none;"><?php echo $default_email_template_code; ?></textarea>
			<?php
		}

		public function fx_cf7_save_template_data( $contact_form ) {

			$contact_form_id     = $contact_form->id();
			$email_template_data = isset( $_POST['email_template_data'] ) ? $_POST['email_template_data'] : '';
			$email_template_code = isset( $_POST['email_template_code'] ) ? $_POST['email_template_code'] : '';

			// Update each time (in case user uses blank value)
			update_post_meta( $contact_form_id, '_email_template_data', $email_template_data );
			update_post_meta( $contact_form_id, '_email_template_code', $email_template_code );
		}

		public function fx_cf7_assets_addon() {

            // Only load assets on CF7 individual form pages
            $screen = get_current_screen();
            if ( is_null( $screen ) || 'toplevel_page_wpcf7' !== $screen->id || ! isset( $_GET['post'] ) ) {
                return;
            }

            wp_register_style(
                'fx-cf7-email-template-main',
                plugins_url( '/assets/css/main.css', __FILE__ ),
                true,
                $this->debug ? filemtime( __DIR__ . '/assets/css/main.css' ) : $this->version,
            );
            wp_register_style(
                'codemirror',
                plugins_url( '/assets/vendors/codemirror.css', __FILE__ ),
                true,
                ''
            );
            wp_register_style(
                'lint',
                plugins_url( '/assets/vendors/addons/lint/lint.css', __FILE__ ),
                true,
                ''
            );
            wp_register_style(
                'fold',
                plugins_url( '/assets/vendors/addons/fold/foldgutter.css', __FILE__ ),
                true,
                ''
            );
            wp_register_style(
                'codemirror-theme-material-ocean',
                plugins_url( '/assets/vendors/themes/material-ocean.css', __FILE__ ),
                true,
                ''
            );
            wp_register_style(
                'dragula',
                plugins_url( '/assets/vendors/dragula.min.css', __FILE__ ),
                true,
                ''
            );
            wp_enqueue_style( 'codemirror' );
            wp_enqueue_style( 'lint' );
            wp_enqueue_style( 'fold' );
            wp_enqueue_style( 'codemirror-theme-material-ocean' );

            wp_enqueue_style( 'dragula' );

            wp_enqueue_style( 'fx-cf7-email-template-main' );

            wp_register_script(
                'handlerbar',
                plugins_url( '/assets/vendors/handlebar.4.7.2.min.js', __FILE__ ),
                false,
                '4.7.2',
                true
            );
            wp_register_script(
                'fx-cf7-email-template-main',
                plugins_url( '/assets/js/main.js', __FILE__ ),
                true,
                $this->debug ? filemtime( __DIR__ . '/assets/js/main.js' ) : $this->version,
                true
            );

            // CodeMirror Assets
            wp_register_script(
                'codemirror',
                plugins_url( '/assets/vendors/codemirror.js', __FILE__ ),
                false,
                '',
                true
            );
            wp_register_script(
                'addon-mode-simple',
                plugins_url( '/assets/vendors/addons/mode/simple.js', __FILE__ ),
                false,
                '',
                true
            );
            wp_register_script(
                'addon-mode-multiplex',
                plugins_url( '/assets/vendors/addons/mode/multiplex.js', __FILE__ ),
                false,
                '',
                true
            );
            wp_register_script(
                'addon-edit-matchbrackets',
                plugins_url( '/assets/vendors/addons/edit/matchbrackets.js', __FILE__ ),
                false,
                '',
                true
            );
            wp_register_script(
                'handlebars-mode',
                plugins_url( '/assets/vendors/mode/handlebars.js', __FILE__ ),
                false,
                '',
                true
            );
            wp_register_script(
                'javascript-mode',
                plugins_url( '/assets/vendors/mode/javascript.js', __FILE__ ),
                false,
                '',
                true
            );
            wp_register_script(
                'lint-pckg-jshint',
                plugins_url( '/assets/vendors/addons/lint/jshint.js', __FILE__ ),
                false,
                '',
                true
            );
            wp_register_script(
                'lint-pckg-jsonlint',
                plugins_url( '/assets/vendors/addons/lint/jsonlint.js', __FILE__ ),
                false,
                '',
                true
            );
            wp_register_script(
                'lint-lint',
                plugins_url( '/assets/vendors/addons/lint/lint.js', __FILE__ ),
                false,
                '',
                true
            );
            wp_register_script(
                'lint-javascript-lint',
                plugins_url( '/assets/vendors/addons/lint/javascript-lint.js', __FILE__ ),
                false,
                '',
                true
            );
            wp_register_script(
                'lint-json-lint',
                plugins_url( '/assets/vendors/addons/lint/json-lint.js', __FILE__ ),
                false,
                '',
                true
            );
            wp_register_script(
                'fold-foldcode',
                plugins_url( '/assets/vendors/addons/fold/foldcode.js', __FILE__ ),
                false,
                '',
                true
            );
            wp_register_script(
                'fold-foldgutter',
                plugins_url( '/assets/vendors/addons/fold/foldgutter.js', __FILE__ ),
                false,
                '',
                true
            );
            wp_register_script(
                'fold-brace-fold',
                plugins_url( '/assets/vendors/addons/fold/brace-fold.js', __FILE__ ),
                false,
                '',
                true
            );
            wp_register_script(
                'fold-xml-fold',
                plugins_url( '/assets/vendors/addons/fold/xml-fold.js', __FILE__ ),
                false,
                '',
                true
            );
            wp_register_script(
                'fold-indent-fold',
                plugins_url( '/assets/vendors/addons/fold/indent-fold.js', __FILE__ ),
                false,
                '',
                true
            );
            wp_register_script(
                'dragula',
                plugins_url( '/assets/vendors/dragula.min.js', __FILE__ ),
                false,
                '',
                true
            );

            wp_enqueue_script( 'handlerbar' );
            wp_enqueue_script( 'codemirror' );
            wp_enqueue_script( 'addon-mode-simple' );
            wp_enqueue_script( 'addon-mode-multiplex' );
            wp_enqueue_script( 'addon-edit-matchbrackets' );
            wp_enqueue_script( 'javascript-mode' );
            wp_enqueue_script( 'handlebars-mode' );
            wp_enqueue_script( 'lint-pckg-jshint' );
            wp_enqueue_script( 'lint-pckg-jsonlint' );
            wp_enqueue_script( 'lint-lint' );
            wp_enqueue_script( 'lint-javascript-lint' );
            wp_enqueue_script( 'lint-json-lint' );

            wp_enqueue_script( 'fold-foldcode' );
            wp_enqueue_script( 'fold-foldgutter' );
            wp_enqueue_script( 'fold-brace-fold' );
            wp_enqueue_script( 'fold-xml-fold' );
            wp_enqueue_script( 'fold-indent-fold' );

            wp_enqueue_script( 'dragula' );

            wp_enqueue_script( 'fx-cf7-email-template-main' );
		}
	}
endif;

function FX_CF7_Email_Template() {
    return FX_CF7_Email_Template::instance();
}

FX_CF7_Email_Template();
