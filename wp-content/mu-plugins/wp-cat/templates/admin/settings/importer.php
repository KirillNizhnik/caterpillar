<div class="wrap">
    <?php if(!empty($classes)): ?>

    <p>You can manually update each class of your selected types by pressing any of the buttons below. Imports may take several minutes to complete.</p>

    <div class="importer-buttons">
        <h2>New</h2>
        <?php foreach($classes as $class): if(empty($class)) continue; ?>

        <button type="button" class="button action js-importer-start" data-class="<?php echo $class; ?>">Import <?php echo CAT()->get_available_classes($class); ?></button>

        <?php endforeach; ?>

<!--        <h2>Rental</h2>-->
<!--        <button type="button" class="button action js-importer-start" data-class="rental">Import Rental Data</button>-->
       <!-- <button type="button" class="button action js-importer-start" data-class="rental-purge">Purge Rental Data</button> -->
<?php /*
        <button type="button" class="button action js-importer-start" data-class="purge-rental">Purge Rental Data</button>
 */ ?>

        <h2>Used</h2>
        <button type="button" class="button action js-importer-start" data-class="used">Import Used Machines</button>

    </div>
    <div class="progress">
    </div>

    <?php else: ?>
    <p>Please enable the feed class types that you wish to import.</p>
    <p><a class="button button-primary" href="options-general.php?page=cat-settings&tab=feeds">CAT Settings</a></p>

    <?php endif; ?>

</div>

