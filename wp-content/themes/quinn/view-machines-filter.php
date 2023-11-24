<?php
    $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    $rental = false;

    if (strpos($url,'site-support') !== false) {
        $rental = true;
    }
    wp_enqueue_script(
            'filter-js'
            , content_url() . '/themes/quinn/assets/js/view-machines.js'
            ,array('jquery')
            ,false
            ,'true'
        ); 
    if (!wp_script_is('fx_choices')) {
                            
                            wp_enqueue_script('fx_choices');
                        }
                        if (!wp_script_is('fx_choices_plugin')) {
                            wp_enqueue_script('fx_choices_plugin');
                        }
                        if (!wp_style_is('fx_choices_custom')) {
                            wp_enqueue_style('fx_choices_custom');
                        }
                        if (!wp_style_is('fx_choices_plugin')) {
                            wp_enqueue_style('fx_choices_plugin');
                        }
                      
?>


<div class="filter-category clearfix">
                            <div id="view-machine-form" >
                                <div class="filter-category-flex-box clearfix">
                                    <h5>View Machines:</h5>
                                    <div class="filter-category-name">
                                        <input type="radio" id="new" name="name" value="cat_new_machine_family">
                                        <label class="newbtn" for="new" value="cat_new_machine_family">New</label>
                                        <input type="radio" id="used" name="name" value="cat_used_machine_family">
                                        <label class="usedbtn" for="used" value="cat_used_machine_family">Used</label>
                                       <!-- <input type="radio" id="rental" checked="" name="name" value="cat_new_allied_family">
                                        <label class="rentalbtn" for="rental" value="cat_new_allied_family">Rental</label> -->
                                    </div>
                                    <div class="filter-search-category">
                                        <?php //Little tricky - actually four different dropdowns that change based on user selection :sweat-smile:
                                        include get_template_directory() . '/cat/search-dropdowns/view-all.php';
                                        include get_template_directory() . '/cat/search-dropdowns/view-new.php';
                                        include get_template_directory() . '/cat/search-dropdowns/view-used.php';
                                       // include get_template_directory() . '/cat/search-dropdowns/view-rental.php';
                                        
                                        
                                        ?>
                                        <div class="filter-search-category-button">
                                            <a class="filter-categ-wrap" href="" ><button name="filter-categ" class="btn btn-secondary filter-categ">Search</button></a>
                                        </div>
                                    </div>
                                    <div><p><i>Select New, Used, or Rental to filter options.</i></p></div>
                                </div>
                                
                            </div>
                        </div>
                        
  
