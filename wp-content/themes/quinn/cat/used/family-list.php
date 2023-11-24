<div class="product-item-card__list">
    <div class="flex-image-cards">
        <?php if (empty($families)): ?>
            <p><b>Sorry, there aren't currently any listings for this family.</b></p>
        <?php else:
            foreach($families as $family) {
                //if($family->count == 0) { continue; }
               // echo $family->count;
                cat_template('used/loop/content-family', array('family' => $family));
            }
        endif;
        
        
        ?>
    </div>
</div>
