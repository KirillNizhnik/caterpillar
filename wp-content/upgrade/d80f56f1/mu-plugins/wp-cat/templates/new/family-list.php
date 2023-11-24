<div class="product-item-card__list">
    <div class="row">
    <?php if (empty($families)): ?>
        <p><b>Sorry, there aren't currently any listings for this family.</b></p>
    <?php
        else:
            foreach($families as $family) {
                cat_template('new/loop/content-family', array('family' => $family));
            }
        endif;
    ?>
    </div>
</div>
