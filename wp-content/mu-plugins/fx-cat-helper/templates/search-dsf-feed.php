<?php
/**
 * Template for the View New Feed XMLs tab
 */
?>
<h2>Used Feed Search Tool</h2>
<p>Use the form below to search the Used feed for a product.</p>
<form action="" method="post" id="fx_dsf_search_tool">
	<table class="form-table">
		<tbody>
		<tr valign="top">
			<td>
				<input type="text" name="cat_dsf_search" id="cat_dsf_search"
					   placeholder="Search by Product ID, Model Name or Serial Number" style="width: 200%; max-width: 500px">
			</td>
		</tr>
		</tbody>
	</table>
	<?php submit_button( 'Search!' ); ?>
</form>
<hr>
<div class="dsf_standard_response_placeholder"></div>
<br>
<div class="dsf_advanced_response_placeholder" style="display: none">
	<div class="advanced_toggle"><h4>Click for Full/Advanced Details</h4></div>
	<br>
	<div class="advanced_toggle_content" style="display: none">
	</div>
</div>
<hr>
<a href="<?php echo esc_html( get_option( 'cat_used_feed_url' ) ); ?> " target="_blank">Just show me the XML</a><i>Disclaimer: searching by ID is the most reliable tool for this. Model name and serial number are not as reliable for similar names.</i>
