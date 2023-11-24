<?php
// IMPORTANT: This plugin is dynamically updated - MODIFICATIONS WILL BE OVERWRITTEN - see https://app.getguru.com/card/T585Kydc

/**************************************************
 * Name: Gravity Forms
 * Description: Automatically track submissions from Gravity Forms
 * Link: https://app.getguru.com/card/cKMnndRi/DEV-LeadmanagerFX-Guides#gravityforms
 *************************************************/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<!-- MCFX Integration: Gravity Forms -->
<script type="text/javascript" data-registered="mcfx-plugin" >
	if (
		/* global mcfx */
		'undefined' !== typeof mcfx
	) {
		document.addEventListener( 'submit.gravityforms', ( e ) => {
			if ( 'function' === typeof mcfx ) {
				mcfx( 'capture', e.target );
			}
		} );
	}
</script>

<?php // IMPORTANT: This plugin is dynamically updated - MODIFICATIONS WILL BE OVERWRITTEN - see https://app.getguru.com/card/T585Kydc ?>
