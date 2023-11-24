<?php
// IMPORTANT: This plugin is dynamically updated - MODIFICATIONS WILL BE OVERWRITTEN - see https://app.getguru.com/card/T585Kydc

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals
// - this file is included in a function, and no globals are being set here

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<style type='text/css' >
	/**
	 * Ideally would like to use a WP CSS class instead if we can find a good one
	 *  - for now, we have our own class based on WP's notice color and the error-message weight
	 */
	.success-message {
		color: #00a32a;
		font-weight: 600;
	}
</style>
<div class="wrap">
	<h2><?php esc_html_e( 'MCFX Settings', 'webfx' ); ?></h2> 
	<form method="post" action="options.php">
		<?php settings_fields( 'mcfx_wp_settings' ); ?>
		<table class="form-table">

			<tr valign="top">
				<?php
					// License Key saved in options
					$license_key = $this->get_license_key();

					// License Key Validation status and errors if any
					list($is_license_key_valid, $validation_messages) = $this->is_license_key_valid();

					// License Key Invalidation Flag
					$license_key_maybe_invalid = (int) ( ! $is_license_key_valid );
				?>
				<th scope="row">
					<label for="webfx_core_services_license_key">
						<?php esc_html_e( 'WebFX License Key', 'webfx' ); ?>
					</label>
				</th>
				<td>
					<!-- License Key -->
					<input type="text"
						name="webfx_core_services_license_key"
						id="webfx_core_services_license_key"
						value="<?php echo esc_attr( $license_key ); ?>"
						placeholder="ABCD-1234-EFGH-5678-IJKL"
						style="width: 100%; max-width: 500px" />

					<!-- License Key Information -->
					<?php if ( $license_key_maybe_invalid ) : ?>

						<?php if ( empty( $license_key ) ) : ?>
							<span class='error-message'><?php esc_html_e( 'Enter License Key', 'webfx' ); ?></span>
						<?php else : ?>
							<span class='error-message'><?php esc_html_e( 'Inactive or Invalid License', 'webfx' ); ?></span>
						<?php endif; ?>

						<br>
						<b><a href='https://app.getguru.com/card/TeE6oe6c/MCFX-WordPress-Plugin-AKA-WebFX-Core-Services' target='_blank'><?php esc_html_e( 'Instructions', 'webfx' ); ?></a></b>

					<?php else : ?>
						<span class='success-message'><?php esc_html_e( 'Active', 'webfx' ); ?></span>
					<?php endif ?>

					<?php foreach ( $validation_messages as $validation_message ) : ?>
							<p class='<?php echo esc_attr( $is_license_key_valid ? 'success-message' : 'error-message' ); ?>'><?php echo esc_html( $validation_message ); ?></p>
					<?php endforeach ?>

					<!-- License Key Invalidation Flag -->
					<input type="hidden"
						name="webfx_core_services_license_key_maybe_invalid"
						id="webfx_core_services_license_key_maybe_invalid"
						value="<?php echo esc_attr( $license_key_maybe_invalid ); ?>" />

					<!-- Invalidate License Key on Change -->
					<script>
						document.getElementById('webfx_core_services_license_key').addEventListener('change', function () {
							document.getElementById('webfx_core_services_license_key_maybe_invalid').value = 1;
						});
					</script>
				</td>
			</tr>


			<tr valign="top">
				<th scope="row">
					<label for="mcfx_id">
						<?php esc_html_e( 'MCFX ID', 'webfx' ); ?>
					</label>
				</th>
				<td>
					<input type="text"
						name="mcfx_id"
						id="mcfx_id"
						value="<?php echo esc_attr( $this->get_mcfx_id() ); ?>"
						placeholder="1234"
						style="width: 100%; max-width: 500px" />

					<?php if ( $license_key_maybe_invalid && ! $this->get_mcfx_id() ) : ?>
						<br><b>Save a valid license key to auto-fill the MCFX ID</b>
					<?php endif ?>
				</td>
			</tr>

			<tr valign="top">
				<td></td>
				<td scope="row" style="padding-bottom: 0">
					<input type="checkbox"
							name="mcfx_script_active"
							id="mcfx_script_active"
							value="1"
							<?php checked( get_option( 'mcfx_script_active' ) ); ?> />
					<label for="mcfx_script_active">
						<b><?php esc_html_e( 'Enable MCFX tracking scripts', 'webfx' ); ?></b>
					</label>
				</td>
			</tr>

			<tr valign="top" class="js-mcfx-config">
				<td></td>
				<td scope="row" style="padding-bottom: 20px">
					<input type="checkbox"
							name="pfx_active"
							id="pfx_active"
							value="1"
							<?php checked( get_option( 'pfx_active' ) ); ?>>
					<label for="pfx_active">
						<b><?php esc_html_e( 'Enable Personalization script', 'webfx' ); ?></b>
					</label>
				</td>
			</tr>

		<?php $integrations = $this->get_mcfx_integrations(); ?>
		<?php if ( ! empty( $integrations ) ) : ?>
			<tr valign="top" class="js-mcfx-config">
				<th scope="row">Integrations</th>
				<td scope="row" style="padding-bottom: 20px">
				<?php foreach ( $integrations as $slug => $integration ) : ?>
					<input type="checkbox"
							name="mcfx_integrations[<?php echo esc_attr( $slug ); ?>][enabled]"
							id="mcfx_integrations[<?php echo esc_attr( $slug ); ?>][enabled]"
							value="1"
							<?php checked( $integration['enabled'] ); ?>>
					<label for="mcfx_integrations">
						<b><?php echo esc_html( $integration['name'] ); ?>:</b>
						<?php echo esc_html( $integration['description'] ); ?>
						<?php if ( ! empty( $integration['link'] ) ) : ?>
							- <a href="<?php echo esc_attr( $integration['link'] ); ?>"><?php esc_html_e( 'Read More', 'webfx' ); ?></a>
						<?php endif ?>
					</label><br>
				<?php endforeach ?>
				</td>
			</tr>
		<?php endif ?>

			<tr valign="top" class="js-mcfx-config">
				<th scope="row">
					<label for="configuration_type">
						Configuration Type
					</label>
				</th>
				<td scope="row" style="padding-bottom: 20px">
					<select name="configuration_type" id="configuration_type">
						<option value="default" <?php selected( get_option( 'configuration_type' ), 'default' ); ?>>Default Configuration</option>
						<option value="custom" <?php selected( get_option( 'configuration_type' ), 'custom' ); ?>>Custom Configuration</option>
					</select>
				</td>
			</tr>

			<tr valign="top" class="js-default js-mcfx-config hidden"> <!-- Hide if Custom Configuration -->
				<td></td>
				<td scope="row" style="padding-bottom: 20px">
					<b><?php esc_html_e( 'Default Configuration: MCFX scripts will be automatically output based on your selections', 'webfx' ); ?></b>
				</td>
			</tr>

			<tr valign="top" class="js-default js-mcfx-config hidden"> <!-- Hide if Custom Configuration -->
				<td></td>
				<td scope="row" style="padding-bottom: 20px">
					<input type="checkbox"
							name="call_tracker_active"
							id="call_tracker_active"
							value="1"
							<?php checked( get_option( 'call_tracker_active' ) ); ?> />
					<label for="call_tracker_active">
						<b><?php esc_html_e( 'Enable CallTracker script', 'webfx' ); ?></b>
					</label>
				</td>
			</tr> <!-- END Hide if Custom Configuration -->

			<tr valign="top" class="js-custom js-mcfx-config hidden"> <!-- Hide if Default Configuration -->
				<td></td>
				<td scope="row" colspan="2">

					<b>Custom Configuration Instructions</b>
					<ol>
						<li>Please copy and paste your custom configuration from MarketingCloudFX below (including the <code>&lt;script&gt;</code> tags).<br>
						<?php if ( $this->get_mcfx_id() ) : ?>
							This is the script from <a href="https://app.webfx.com/marketingcloudfx/<?php echo esc_attr( $this->get_mcfx_id() ); ?>/settings/tracking-code" target='_blank'>your MarketingCloudFX dashboard</a>
						<?php else : ?>
							This is the script from your MarketingCloudFX dashboard. You can find it by going to your MCFX account. Under Configuration on the left sidebar, find "settings" at the bottom. Then, click "Tracking Code" from the settings page.
						<?php endif; ?>
						<li>Additionally, if you wish to add additional tracking customizations in the head, you can add them to the end of the script.</li>
					</ol>
				</td>
			</tr>

			<tr valign="top" class="js-custom js-mcfx-config hidden">
				<th scope="row">
					<label for="custom_mcfx_config"><b>Custom MCFX Scripts - HEAD</b></label>
					<br>
					<p>This code will be inserted into the head tag of the website.</p>
				</th>
				<td scope="row" style="padding-bottom: 20px">
					<?php
						// phpcs:disable WordPress.Security.EscapeOutput
						// - This is explicitly HTML with script tags in a code editor context
					?>
					<textarea name="custom_mcfx_config" id="custom_mcfx_config" cols="75" rows="10" placeholder="Paste script here from MarketingCloudFX configuration page after setting your preferences there" class='js-code-editor'><?php echo get_option( 'custom_mcfx_config' ); ?></textarea>
					<?php // phpcs:enable WordPress.Security.EscapeOutput ?>
					<small style='float:right'><a href='https://jshint.com/docs/#:~:text=Inline%20configuration' target='_blank'>JS Linting Tweaks</a></small>
				</td>
			</tr> <!-- END Hide if Default Configuration -->

			<tr valign="top" class="js-mcfx-config">
				<th scope="row">
					<label for="custom_footer_scripts"><b>Custom Scripts - FOOTER</b></label>
					<br>
					<p>This code will be inserted into the footer of the website.</p>
				</th>
				<td scope="row" style="padding-bottom: 20px">
					<?php
						// phpcs:disable WordPress.Security.EscapeOutput
						// - This is explicitly HTML with script tags in a code editor context
					?>
					<textarea name="custom_footer_scripts" id="custom_footer_scripts" cols="75" rows="10" placeholder="Add custom scripts here to output in footer - event tracking for example" class='js-code-editor'><?php echo get_option( 'custom_footer_scripts' ); ?></textarea>
					<?php // phpcs:enable WordPress.Security.EscapeOutput ?>
					<small style='float:right'><a href='https://jshint.com/docs/#:~:text=Inline%20configuration' target='_blank'>JS Linting Tweaks</a></small>
				</td>
			</tr>


			<tr valign="top" class="js-mcfx-config">
				<th scope="row">
					<label><b>References</b></label>
				</th>
				<td scope="row" style="padding-bottom: 20px">
					<p><a href="https://app.getguru.com/card/i48kxMpT/Micro-Event-Tracking-in-LMFX" target="_blank">Micro Event Tracking in LMFX - Guru Card</a></p>
					<p><a href="https://app.getguru.com/card/cn8eA8qi/Working-with-the-MarketingCloudFX-Script" target="_blank">Working with the MarketingCloudFX Script - Guru Card</a></p>
				</td>
			</tr>

		</table>
		<?php submit_button(); ?>
	</form>
</div>
<script type='text/javascript' >

	/**
	 * Show/hide configuration type custom vs. default & MCFX tracking options
	 *
	 */
	const mcfx_script_active = document.getElementById('mcfx_script_active');
	const config_select = document.getElementById('configuration_type');
	const updateConfigView = function() {

		const hide_mcfx_config = ! mcfx_script_active.checked;
		const hide_default = (config_select.value === 'custom');
		const hide_custom = ! hide_default;

		// Toggle elements with class: js-mcfx-config FIRST
		document.querySelectorAll('.js-mcfx-config').forEach(el => {
			el.classList.toggle('hidden', hide_mcfx_config);
		});

		// If NOT showing MCFX Config, then we don't want to show anything for any type
		if (hide_mcfx_config) {
			// So, return early
			return;
		}

		// Toggle elements with class: js-custom
		document.querySelectorAll('.js-custom').forEach(el => {
			el.classList.toggle('hidden', hide_custom);
		});

		// Toggle elements with class: js-default
		document.querySelectorAll('.js-default').forEach(el => {
			el.classList.toggle('hidden', hide_default);
		});

	}
	updateConfigView(); // Update immediately on load
	config_select.addEventListener('change', updateConfigView); // Update on config type change
	mcfx_script_active.addEventListener('change', updateConfigView); // Update on MCFX Enable/Disable

	/**
	 * Set up WP Plugin Editor support for syntax and linting
	 * - Dependency scripts loaded in footer, so wait for window load
	 */
	jQuery(document).ready(function($) {

		$('.js-code-editor').each(function() {
			wp.codeEditor.initialize($(this), cm_settings);
		})
	});

</script>

<?php // IMPORTANT: This plugin is dynamically updated - MODIFICATIONS WILL BE OVERWRITTEN - see https://app.getguru.com/card/T585Kydc ?>
