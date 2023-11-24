<?php
/**
 * Template for the View New Feed tab
 */
?>
<h2>New Feed Search Tool</h2>
<p>Use the form below to search the new feed for a product.</p>
<form action="" method="post" id="fx_cpc_search_tool">
	<table class="form-table">
		<tbody>
		<tr valign="top">
			<td>
				<input type="text" name="cat_cpc_search" id="cat_cpc_search" placeholder="Enter CPC ID or Product Title"
					   style="width: 200%; max-width: 1000px">
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="cat_import_if_found">Import product if found on feed and not yet
					imported?</label></th>
			<td>
				<input type="checkbox" name="cat_import_if_found" id="cat_import_if_found">
			</td>
		</tr>
		</tbody>
	</table>
	<?php submit_button( 'Search!' ); ?>
</form>
<hr>
<div class="cpc_standard_response_placeholder"></div>
<br>
<div class="cpc_advanced_response_placeholder" style="display: none">
	<div class="advanced_toggle"><h4>Click for Full/Advanced Details</h4></div>
	<br>
	<div class="advanced_toggle_content" style="display: none">
	</div>
</div>
