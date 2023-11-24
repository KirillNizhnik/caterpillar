<?php

use Fx_Cat_Helper\Controllers\Fx_Clone_Cpc_Xmls;

$clone_instance = Fx_Clone_Cpc_Xmls::instance();
$cron_scheduled = $clone_instance->maybe_update_cron();
?>
<h2>New Feed Clone and View tool.</h2>
<p>This feature allows you to clone all the New Feed XMLs configured in the CAT plugin for the New Feed.</p>
<br>
<form method="post" action="">
	<?php if ( $cron_scheduled ) : ?>
		<p>Daily clones/re-sync of xmls with the CPC feed are scheduled. Next run will
			be: <?php echo esc_html( wp_date( 'Y-m-d H:i:s', $cron_scheduled ) ); ?></p>
		<p><input type="submit" name="disable_cron" value="Disable Daily Import" class="button button-primary"/></p>
	<?php else : ?>
		<p>Daily clone/re-sync of xmls is not scheduled.</p></p>
		<p><input type="submit" name="enable_cron" value="Enable Daily Import" class="button button-primary"/></p>
	<?php endif; ?>
</form>
<i class="cpc-current-status">Current Status: <?php echo esc_html( get_option( 'fx_cpc_xmls_cloned_status' ) ); ?></i>
<br>
<br>
<div class="cpc-xml-spinner-wrap"><span class="cpc-xml-spinner"></span></div>
<hr>
<a href="<?php echo esc_html( get_site_url() ); ?>/wp-content/uploads/local-cat-display/hierarchy/feed-list.html" target="_blank">View
	this in browser</a>
<iframe id="cpc-xml-hierarchy" style="min-width:1250px;max-width:100%;height:650px;"
		src="<?php echo esc_html( get_site_url() ); ?>/wp-content/uploads/local-cat-display/hierarchy/feed-list.html"></iframe>
<hr>
<?php if ( get_option( 'fx_cpc_xmls_cloned_status' ) !== 'Empty' ) : ?>
	<?php if ( get_option( 'fx_cpc_xmls_cloned_status' ) !== 'In Progress' ) : ?>
		<button type="button" class="cpc-delete-data">Delete XMLs</button>
	<?php endif; ?>
	<i class="cpc-last-sync-time">Last synced: <?php echo esc_html( get_option( 'fx_cpc_xmls_cloned_time' ) ); ?></i>
<?php endif; ?>
<hr>
<br>
<i>Why would I need this?</i> <br>
<h8>As of 2021, CAT started requiring all of it's dealers to pass a oauth request in order to access their new/cpc feed.
	Because of this, you cannot view the view directly in browser.
	<a target="_blank" href="https://cpc.cat.com/docs/cpc-ws-v2-configuration/">(documentation reference)</a><br>
	This tool provides a workaround to this by making the authentication request for you, and then cloning the contents
	of the feed to your current WordPress site in the uploads directory. <br>
	<b>PLEASE NOTE:</b><br>
	Since this tool clones the data vs reading it directly, it is necessary to "refresh" every so often to ensure you
	are reading from the most accurate results.
</h8>
