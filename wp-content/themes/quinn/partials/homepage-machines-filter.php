<?php 
//homepage category search
?>
<div class="home-quipment-search-category">
                            <!--<form>-->
                                <div class="home-quipment-search-category-wrapper">
                                    <div class="category-label">
                                        <h5>Equipment category:</h5>
                                    </div>
                                    <div class="category-option">
                                        <select id="home-categ">
                                           <option>All categories</option>
                                                
                                                <?php
                                                $args = array( 'hide_empty' => true, );
                        
                        						$allcat = array('cat_new_machine_family','cat_used_machine_family');
                        
                                                $terms = get_terms( $allcat, $args );
                                                if ( ! empty( $terms ) ) {
                                                    $count = count( $terms );
                                                    $term_list = '';
                                                    $term_names = array();
                                                    foreach ( $terms as $term ) {
                                                        $term_slug_title = str_replace('-', ' ', $term->slug);
                                                        $term_title_clean = ucwords($term_slug_title);
                                                
                                                        if(in_array($term->name, $term_names)){
                                                            continue;
                                                        }
                                                        $term_names[] = $term->name;
                                                        $term_list .= '<option value="' . esc_url( get_term_link( $term) ). '">' . $term->name . '</option>';
                                                    }
                                                     echo $term_list;
                                                }
                                                ?>
                                        </select>
                                    </div>
                                    <div class="category-search-button">
                                        <a id="home-categ-destination" href="<?php echo get_permalink(  6 ); ?>"><button class="btn btn-secondary">Search <span class="hidden-sm-down">our product</span> inventory</button></a>
                                    </div>
                                </div>
                            <!--</form>-->
                        </div>
                        <script>
                        (function($){
        		$(function(){
                
                $("#home-categ").on('change', function(e) {
                        //e.preventDefault();
                        var cat_value = $(this).val();
        
                        $('#home-categ-destination').attr("href",cat_value);
        
                    });
        		});
        	})(jQuery);
        	
        	</script>
  