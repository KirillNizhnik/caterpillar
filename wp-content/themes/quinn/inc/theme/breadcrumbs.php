<?php 
// define the wpseo_breadcrumb_output callback and fix CAT sillies
function fx_wpseo_breadcrumb_output( $output ) { 
    
    if (is_404()) {
        return($output);
    }
    
    //USED FAMILY 
    if (is_tax('cat_used_machine_family')) { 
        
        $new_full_markup = '<a href="' . get_site_url() . '/equipment/machines/">Machines</a>';
        //var_dump(strpos($output, $new_full_markup));
        if(strpos($output, $new_full_markup) !== false) {
   
            $new_output = str_replace($new_full_markup, '', $output);
            $output = $new_output;
        }
        
        $replace_markup = '<a href="' . get_site_url() . '/used-equipment/machinery/">Machinery</a>';
        $new_full_markup = '<a href="' . get_site_url() . '/used-equipment/machinery/">Used Machinery</a>';
        
        if(strpos($output, $replace_markup) !== false  )
        {
   
            $new_output = str_replace($replace_markup, $new_full_markup,  $output);
            $output = $new_output;
        }
    }
    
    //SINGLE USED 
    if(is_singular('cat_used_machine')) {
        $used_machine_page = '<a href="' . get_site_url() . '/used-equipment/machinery/">Used Machinery</a>';
        $new_page = '<a href="' . get_site_url() . '/equipment/machines/">Machines</a>';
         if(strpos($output, $new_page) !== false) {

            $new_output = str_replace($new_page, $used_machine_page, $output);
            $output = $new_output;
        }
        
         //AG title change 
        $replace_markup = '<a href="' . get_site_url() . '/used-equipment/machinery/">Machinery</a>';
        $new_full_markup = '<li><a href="' . get_site_url() . '/used-equipment/machinery/">Used Machinery</a></li><li><a href="' . get_site_url() . '/used-equipment/agricultural-products/">Agricultural Products</a>';
        $id = get_queried_object_id();
        
        if(strpos($output, $replace_markup) !== false && (has_term('agricultural-products', 'cat_used_machine_family', $id) || has_term('tractors', 'cat_used_machine_family', $id) ) )
        {
   
            $new_output = str_replace($replace_markup, $new_full_markup,  $output);
            $output = $new_output;
        }
    }
    
    //NEW TAX 
    if (is_tax('cat_new_machine_family')) {
        $queried_object = get_queried_object();
    
                $taxonomy = $queried_object->taxonomy;
                $term_id = $queried_object->term_id;
        
        
        //fix archive main
        $not_real_glitch = '<a href="' . get_site_url() . '/equipment/machines/new/">New Machinery</a>';
        if(strpos($output, $not_real_glitch) !== false) {

            $new_output = str_replace($not_real_glitch, '', $output);
            $output = $new_output;
        }
        $last_item_glitch = 'New Machinery';
        $current_tax_name = CAT()->family()->name;
        if(strpos($output, $last_item_glitch) !== false) {
            $new_output = str_replace($last_item_glitch, $current_tax_name, $output);
            $output = $new_output;
        }
        
        //just kalmar fix 
        $kalmar_url_bad = '<a href="' . get_site_url() . '/new-equipment/machines/kalmar/">Kalmar</a>';
        $url_correct = '<a href="' . get_site_url() . '/equipment/lift-truck/new-lift-trucks/kalmar/">Kalmar</a>';
        if (strpos($output, $kalmar_url_bad) !== false ) {
            $new_output = str_replace($kalmar_url_bad, $url_correct, $output);
            $output = $new_output;
        }
        
        // AG fix 
        $ag_url_bad = '<a href="' . get_site_url() . '/new-equipment/machines/ag-tractors">Ag Tractors</a>';
        $ag_url_correct = '<a href="' . get_site_url() . '/equipment/ag-tractors/new/">Ag Tractors</a>';
        if (strpos($output, $ag_url_bad) !== false ) {
            $new_output = str_replace($ag_url_bad, $ag_url_correct, $output);
            $output = $new_output;
        }
        
        //push fendt onto crumbs 
        $fendt_fams = array('1100 Series', '1100 Series', '500 Series', '700 Series', '800 Series', '900 Series');
        foreach($fendt_fams as $fendt_fam) {
            $bad_url = '<li>  <span class="breadcrumb_last" aria-current="page">' . $fendt_fam . '</span></li>';
            if (strpos($output, $fendt_fam)){
                
                $forced_crumb = '<li><a href="' . get_site_url() . '/equipment/ag-tractors/fendt-ag-tractors/">Fendt</a></li><li><span class="breadcrumb_last" aria-current="page">' . $fendt_fam . '</span></li>';
                $new_output = str_replace($bad_url, $forced_crumb, $output);
                $output = $new_output;
               //var_dump($new_output);die();
            }
        }
        
        //set lift trucks custom

            if (term_is_ancestor_of( 648 ,$queried_object->term_id, 'cat_new_machine_family')){
                $replace_crumb = '<a href="' . get_site_url() . '/equipment/machines/">Machines</a>';
                $new_crumb = '<a href="' . get_site_url() . '/equipment/lift-truck/new-lift-trucks/">Lift Trucks</a>';
                if (strpos($output, $replace_crumb ) !== false){
                   $new_output = str_replace($replace_crumb, $new_crumb, $output);
                   $output = $new_output;
                   //var_dump($new_output);die(); 
                }
                
            }
 
    }
    
    //NEW SINGLE 
    if(is_singular('cat_new_machine')) {
        
        $not_real_glitch = '<a href="' . get_site_url() . '/equipment/machines/new/">New Machinery</a>';
        $fam_fix_name = CAT()->product()->family->name;
        $fam_fix_link = get_site_url() . '/new-equipment/machines/' .CAT()->product()->family->slug;
        $full_fix = '<a href="' . $fam_fix_link . '">' . $fam_fix_name . '</a> ' . '<span class="breadcrumb_last" aria-current="page">' . get_the_title() . ' </span>';
        if(strpos($output, $not_real_glitch) !== false) {
            $new_output = str_replace($not_real_glitch, $full_fix, $output);
            $output = $new_output;
        }
        
        
        // AG fix 
        $ag_url_bad = '<a href="' . get_site_url() . '/new-equipment/machines/ag-tractors">Ag Tractors</a>';
        $ag_url_correct = '<a href="' . get_site_url() . '/equipment/ag-tractors/new/">Ag Tractors</a>';
        if (strpos($output, $ag_url_bad) !== false ) {
            $new_output = str_replace($ag_url_bad, $ag_url_correct, $output);
            $output = $new_output;
        }
        
        //push fendt onto crumbs 
        $fendt_fams = array('1100 Series', '1000 Series', '500 Series', '700 Series', '800 Series', '900 Series');
        $product = CAT()->product();
        foreach($fendt_fams as $fendt_fam) {
            
            
            $target_crumb = '<a href="' . get_site_url() . '/equipment/machines/">Machines</a>';
            
           
            if (strpos($output, $fendt_fam) && strpos($output, $target_crumb)){
               
                $forced_crumb = '<a href="' . get_site_url() . '/equipment/machines/">Machines</a><li><a href="' . get_site_url() . '/equipment/ag-tractors/fendt-ag-tractors/">Fendt</a></li>';
                $new_output = str_replace($target_crumb, $forced_crumb, $output);
                $output = $new_output;
               
            }
        }
        
        
    }
    
    //SINGLE LOCATION
    if(is_singular('location')) {
        
        $replace_markup = 'Home</a>';
        $new_full_markup = 'Home</a></li><li><a href="' . get_site_url() . '/locations/">Locations</a>';
        
        if(strpos($output, $replace_markup) !== false  )
        {
   
            $new_output = str_replace($replace_markup, $new_full_markup,  $output);
            $output = $new_output;
        }
    }
    
    
    
    // GENERAL RULES ALL
    $queried_object = get_queried_object();
    if(is_object($queried_object)) {
        if($queried_object->slug =="special-application") {
            $target_crumb = '<a href="' . get_site_url() . '/equipment/machines/">Machines</a>';
            if(strpos($output, $target_crumb)) {
                $forced_crumb = '<a href="' . get_site_url() . '/equipment/machines/">Machines</a><li><a href="' . get_site_url() . '/equipment/ag-tractors/new/challenger/">Challenger</a></li>';
                $new_output = str_replace($target_crumb, $forced_crumb, $output);
                $output = $new_output;
            }
        }
    }
    $target_crumb = '<a href="' . get_site_url() . '/?page_id=8787">Cat® Technology</a>';
    if(strpos($output, $target_crumb)) {
        $new_output = str_replace($target_crumb, '', $output);
                $output = $new_output;
    }
    
    
    
   
    return($output); 
}; 
         
// add the filter 
add_filter( 'wpseo_breadcrumb_output', 'fx_wpseo_breadcrumb_output', 10, 1 ); 


//FINAL OVERRIDE RULE:
add_filter( 'wpseo_breadcrumb_links', 'location_breadcrumbs', 20, 1 );
function location_breadcrumbs($crumbs) {
    
    if (is_404()) {
        return($crumbs);
    }
    
    $id = get_queried_object_id();
        if (is_single()) {
            $id = get_queried_object_id();
        } else {
            $queried_object = get_queried_object();
            $taxonomy = $queried_object->taxonomy;
            $term_id = $queried_object->term_id;
            $id = $taxonomy . '_' . $term_id;
        }
    if( have_rows('custom_breadcrumbs', $id) ):
        
        $crumbs = [];
        
      
        while( have_rows('custom_breadcrumbs', $id) ) : 
            the_row();
            
                $crumbs[] = array('url' => get_sub_field('crumb_link', $id), 'text' => get_sub_field('crumb_title', $id) );
            
        endwhile;
        
        
    endif;
    
    return($crumbs);
 
}




