<section class="locations-search full-width clearfix" id="locations-search">

<?php if($locations[0]->distance <= 60): ?>

	<?php foreach($locations as $location): ?>
		<?php if($location->distance > 60): ?>
			<?php break; ?>
		<?php endif; ?>

	<article class="overflow result clearfix">
	     
	</article>

	<?php endforeach; ?>
<?php else: ?>
	<article class="overflow result clearfix">
		<p>No results found within 60 miles of your ZIP.</p>
	</article>
<?php endif; ?>
</section>
