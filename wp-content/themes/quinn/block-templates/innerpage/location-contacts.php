<section class="location-contacts section-margins">
	<div class="container">
		<div class="locations-outer-wrap">
			<div class="half-width">
				<div class="iframe-wrapper">
					<?php the_field('map_iframe'); ?>
				</div>
			</div>
			<div class="half-width">
				<div class="location-contact-items">
					<?php if( have_rows('contact_blocks') ):
						while( have_rows('contact_blocks') ): the_row(); ?>
							<div class="lc-block">
								<h5><?php the_sub_field('contact_title'); ?></h5>
								<p><i class="icon icon-hours"></i><strong>Hours:</strong> <?php the_sub_field('hours'); ?></p>
								<div class="numbers-base">
									<p><i class="icon icon-phone"></i><strong>Phone:</strong> <?php the_sub_field('phone'); ?></p>
									<p><i class="icon icon-printer"></i><strong>Fax:</strong> <?php the_sub_field('fax'); ?>
									</p>
								</div>
							</div>
						<?php endwhile;
					endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>