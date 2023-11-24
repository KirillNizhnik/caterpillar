<?php
// IMPORTANT: This plugin is dynamically updated - MODIFICATIONS WILL BE OVERWRITTEN - see https://app.getguru.com/card/T585Kydc

/**************************************************
 * Name: Ninja Forms
 * Description: Automatically track submissions from Ninja Forms.
 * Link: https://app.getguru.com/card/cKMnndRi/DEV-LeadmanagerFX-Guides#ninjaforms
 *************************************************/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<!-- MCFX Integration: Ninja Forms -->
<script type="text/javascript" data-registered="mcfx-plugin" >
	/* global Backbone, Marionette, mcfx */
	document.addEventListener( 'DOMContentLoaded', () => {
		marionette_loaded().then( () => {
			const mcfxSubmitController = Marionette.Object.extend( {
				initialize: function () {
					this.listenTo(
						Backbone.Radio.channel( 'forms' ),
						'before:submit',
						this.actionSubmit
					);
				},
				actionSubmit: function ( response ) {
					const form = document.querySelector(
						'#nf-form-' + response.id + '-cont form'
					);
					if ( form ) {
						form.id = 'nf-form-'+response.id; // Give it a nice ID for easier reference and exclusion
						mcfx( 'capture', form );
					}
				},
			} );
			new mcfxSubmitController();
		} );
	} );

	async function marionette_loaded() {
		while( typeof Marionette == 'undefined' ) {
			await new Promise(function(resolve) {
				setTimeout(resolve, 1000);
			});
		};
	}
</script>

<?php // IMPORTANT: This plugin is dynamically updated - MODIFICATIONS WILL BE OVERWRITTEN - see https://app.getguru.com/card/T585Kydc ?>
