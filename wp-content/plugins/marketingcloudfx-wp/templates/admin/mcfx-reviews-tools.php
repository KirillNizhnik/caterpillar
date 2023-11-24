<?php
// IMPORTANT: This plugin is dynamically updated - MODIFICATIONS WILL BE OVERWRITTEN - see https://app.getguru.com/card/T585Kydc

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals
// - this file is included in a function, and no globals are being set here

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<style >
	/* move this style block to it's own file if it gets much larger */
	table.reviews {
		max-width: 1000px;
		width: 100%;
	}
	table.reviews th {
		background: #555;
		color: #ddd;
	}
	table.reviews th,
	table.reviews td
	{
		border-bottom: 1px solid #555;
	}
	table.reviews tr.imported {
		background-color: #ddd;
	}
</style>


<div class="mcfx_reviews_tools_page wrap">
	<h1><?php esc_html_e( 'FX Review Import', 'webfx' ); ?></h1>

	<p>
		<a href='https://app.marketingcloudfx.com/admin/reviewacceleration/configure/<?php echo esc_html( $mcfx_id ); ?>/' target='_blank'>
			<?php esc_html_e( 'Suspect reviews missing? Click here and enter 999999 as # to Display under Website Integration tab', 'webfx' ); ?>
		</a>
	</p>

	<p>
		<a href="<?php echo esc_html( $reviews_response->_endpoint ); ?>" target="_blank">
			<?php echo esc_html__( 'Reviews Endpoint: ', 'webfx' ) . esc_html( $reviews_response->_endpoint ); ?>
		</a>
	</p>

	<hr>
	<h2><?php esc_html_e( 'Automatic Hourly Import', 'webfx' ); ?></h2>

	<form method="post" action="">
		<?php wp_nonce_field( 'mcfx_adjust_review_import_cron', '_mcfx_review_nonce' ); ?>

		<?php if ( $cron_scheduled ) : ?>

			<p>
				<?php
					// translators: %s is number of new reviews that will be imported during each run
					echo esc_html( sprintf( __( 'Hourly import is scheduled.  %s new reviews will be imported during each run. Next run will be: ', 'webfx' ), self::REVIEWS_PER_IMPORT ) ) . esc_html( wp_date( 'Y-m-d H:i:s', $cron_scheduled ) );
				?>
			</p>
			<p><input type="submit" name="disable_cron" value="<?php esc_html_e( 'Disable Hourly Import', 'webfx' ); ?>" class="button button-primary" /></p>

		<?php else : ?>

			<p><?php echo esc_html__( 'Hourly import is not scheduled.', 'webfx' ); ?></p>
			<p><input type="submit" name="enable_cron" value="<?php esc_html_e( 'Enable Hourly Import', 'webfx' ); ?>" class="button button-primary" /></p>

		<?php endif ?>

	</form>

	<hr>
	<h2><?php esc_html_e( 'Manual Import', 'webfx' ); ?></h2>

	<form method="post" action="">
		<?php wp_nonce_field( 'mcfx_modify_display_preferences', '_mcfx_review_nonce' ); ?>

		<p>
			<?php
				// translators: %1$s will be a number of reviews currently showing, %2$s will be the total number of reviews available to show
				echo esc_html( sprintf( __( 'Previewing %1$s of %2$s reviews', 'webfx' ), $reviews_display_count, $reviews_total_count ) );
			?>
		</p>

		<label for='display_imported'><?php esc_html_e( 'Show:', 'webfx' ); ?></label>
		<select id='display_imported' name='imported'>
			<option value='0' <?php echo selected( 0 === $display_preferences['imported'] ); ?>><?php esc_html_e( 'Show only new reviews', 'webfx' ); ?></option>
			<option value='1' <?php echo selected( 1 === $display_preferences['imported'] ); ?>><?php esc_html_e( 'Show only imported reviews', 'webfx' ); ?></option>
			<option value='2' <?php echo selected( 2 === $display_preferences['imported'] ); ?>><?php esc_html_e( 'Show all reviews', 'webfx' ); ?></option>
		</select>

		<label for='display_limit'><?php esc_html_e( 'Limit:', 'webfx' ); ?></label>
		<input id='display_limit' name='limit' type='text' value='<?php echo esc_html( $display_preferences['limit'] ); ?>' />

		<input type="submit" name="modify_display_preferences" value="<?php esc_html_e( 'Update', 'webfx' ); ?>" class="button button-primary" />

	</form>

	<form method="post" action="">
		<?php wp_nonce_field( 'mcfx_import_reviews', '_mcfx_review_nonce' ); ?>

		<?php
			// translators: %s is number of new reviews that will be imported during each run
			$import_text = sprintf( __( 'Import the next %s (max) new reviews', 'webfx' ), self::REVIEWS_PER_IMPORT );
		?>

		<?php if ( false !== $imported ) : ?>
			<div class="notice notice-info">
				<?php echo esc_html( $imported ) . esc_html__( ' reviews imported', 'webfx' ); ?>
			</div>
		<?php endif ?>

		<p><input type="submit" name="import" value="<?php echo esc_html( $import_text ); ?>" class="button button-primary" /></p>

		<table class="reviews widefat importers">
			<tbody>
				<tr>
					<th>Author / Client</th>
					<th>Review Content</th>
					<th>Date of Review</th>
					<th>Already Imported?</th>
				</tr>

				<?php foreach ( $reviews_response->reviews as $review ) : ?>

					<tr <?php echo $review->_imported ? "class='imported'" : ''; ?>>
						<td width="20%">
							<?php echo esc_html( $review->author_name ); ?>
						</td>
						<td>
							<?php echo esc_html( $review->text ); ?>
						</td>
						<td width="20%">
							<?php echo esc_html( $this->get_review_date( $review ) ); ?>
						</td>
						<td>
							<b><?php echo $review->_imported ? esc_html_e( 'Imported', 'webfx' ) : ''; ?></b>
						</td>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>

		<?php if ( empty( $reviews_response->reviews ) ) : ?>
			<p><?php esc_html_e( 'No reviews to display', 'webfx' ); ?>
		<?php endif ?>

	</form>

</div>

<?php // IMPORTANT: This plugin is dynamically updated - MODIFICATIONS WILL BE OVERWRITTEN - see https://app.getguru.com/card/T585Kydc ?>
