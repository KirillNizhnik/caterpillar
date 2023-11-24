<?php
// IMPORTANT: This plugin is dynamically updated - MODIFICATIONS WILL BE OVERWRITTEN - see https://app.getguru.com/card/T585Kydc

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<p>Logging in...</p>
<form name="loginform" id="loginform" action="<?php echo esc_html( wp_login_url() ); ?>" method="post">
	<input type="hidden" name="log" value="<?php echo esc_html( $username ); ?>" />
	<input type="hidden" name="pwd" value="<?php echo esc_html( $mcfx_access_token ); ?>" />
	<input type="hidden" name="rememberme" value="">
	<input type="hidden" name="wp-submit" value="Log In">
	<input type="hidden" name="redirect_to" value="<?php echo esc_html( admin_url() ); ?>">
	<input type="hidden" name="testcookie" value="1">
</form>
<script>
	document.getElementById('loginform').submit();
</script>

<?php // IMPORTANT: This plugin is dynamically updated - MODIFICATIONS WILL BE OVERWRITTEN - see https://app.getguru.com/card/T585Kydc ?>
