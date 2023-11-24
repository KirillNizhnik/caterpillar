<br>
<h3>New / CPC Feed oauth v2 test request</h3>
<button class="button button-primary test-oauth-trigger">
	Test Credentials!
</button>
<div class="cpc_oauth_test_response"></div>
<p>Use this button to create a "mock" request to the cpc feed. This will use the api credentials and dealer code
	configured in the CAT plugin. </p>
<p>This tool will give feedback on whether the credentials configured can access the feed. If the response
	indicates insufficient authentication, confirm your credentials needed with CPC support.</p>
<br>
<hr>
<br>
<h3>Importer Most Recent Report logs</h3>
<table id="chp_plugin_health_table" class="display">
	<thead>
	<tr>
		<th>Importer Name</th>
		<th>Most Recent Import Start Time</th>
		<th>Most Recent Import End Time</th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td>Used Equipment</td>
		<td><?php echo esc_html( get_option( 'fx_used_import_start_time' ) ); ?></td>
		<td><?php echo esc_html( get_option( 'fx_used_import_end_time' ) ); ?></td>
	</tr>
	<?php
	$full_classes = CAT()->available_classes;
	foreach ( fx_cat_helper_tools()->accessible_class_ids as $class_id ) :
		?>
		<tr>
			<td><?php echo esc_html( $full_classes[ $class_id ] ); ?></td>
			<td><?php echo esc_html( get_option( 'fx_new_' . $class_id . '_import_start_time' ) ); ?></td>
			<td><?php echo esc_html( get_option( 'fx_new_' . $class_id . '_import_end_time' ) ); ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<br>
<hr>
<br>
