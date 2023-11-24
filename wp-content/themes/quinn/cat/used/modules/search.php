<aside class="used-equipemnt-search">

    <h4 class="js-equipment-search-title">Search</h4>
    
    <form name="equipment-search" class="search-equipment-form js-equipment-search-form" action="<?php echo $action == 'equipment_search_post' ? $target : ''?>" method="post" id="js-equipment-search-inject">
    
        <div class="hidden">
            <input type="hidden" name="action" value="<?php echo $action; ?>" id="action" />
            <input type="hidden" name="target" value="<?php echo $target; ?>" id="target" />
            <input type="hidden" name="source" value="<?php echo $source ?>" id="source" />
        <?php if (!empty($class)): ?>
            <input type="hidden" name="class" value="<?php echo $class ?>" id="class" />
        <?php endif ?>
        <?php if (!empty($family)): ?>
            <input type="hidden" name="family" value="<?php echo $family ?>" id="family" />
        <?php endif ?>
        </div>
    
        <div class="form-row row">
    
            <div class="col-xxs-12 form-field">
                <label for="price">Price</label>
                <select name="price" id="price">
                    <?php default_search_values('price', $source); ?>
                </select>
            </div>
    
            <div class="col-xxs-12 form-field">
                <label for="hours">Hours</label>
                <select name="hours" id="hours">
                    <?php default_search_values('hours', $source); ?>
                </select>
            </div>
    
            <div class="col-xxs-12 form-field">
                <label for="city">City</label>
                <select name="city" id="city">
                    <?php default_search_values('city', $source); ?>
                </select>
            </div>
    
            <div class="col-xxs-12 form-field">
                <label for="state">State</label>
                <select name="state" id="state">
                    <?php default_search_values('state', $source); ?>
                </select>
            </div>
    
            <div class="col-xxs-12 form-field">
                <label for="manufacturer">Manufacturer</label>
                <select name="manufacturer" id="manufacturer">
                    <?php default_search_values('manufacturer', $source); ?>
                </select>
            </div>
    
            <div class="col-xxs-12 form-field">
                <label for="year">Year</label>
                <select name="year" id="year">
                    <?php default_search_values('year', $source); ?>
                </select>
            </div>
    
            <div class="col-xxs-12">
                <button type="submit">Submit</button>
            </div>
    
        </div>
    
    </form>

</aside>
