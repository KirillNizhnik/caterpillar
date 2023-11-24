<?php
// IMPORTANT: This plugin is dynamically updated - MODIFICATIONS WILL BE OVERWRITTEN - see https://app.getguru.com/card/T585Kydc

/**************************************************
 * Name: HubSpot
 * Description: Automatically track submissions from HubSpot forms
 * Link: https://app.getguru.com/card/cKMnndRi/DEV-LeadmanagerFX-Guides#hubspot
 *************************************************/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<!-- MCFX Integration: HubSpot -->
<script type="text/javascript" data-registered="mcfx-plugin" >
	if (
		/* global mcfx */
		'undefined' !== typeof mcfx
		&& 'undefined' !== typeof window.mcfxCaptureCustomFormData
	) {
		// Reference https://legacydocs.hubspot.com/global-form-events
		window.addEventListener( 'message', ( event ) => {
			if (
				event.data.type === 'hsFormCallback' &&
				event.data.eventName === 'onFormSubmit'
			) {
				window.mcfxCaptureCustomFormData(event.data.data, 'hsForm_' + event.data.id );
			}
		} );
	}
</script>

<?php // IMPORTANT: This plugin is dynamically updated - MODIFICATIONS WILL BE OVERWRITTEN - see https://app.getguru.com/card/T585Kydc ?>
