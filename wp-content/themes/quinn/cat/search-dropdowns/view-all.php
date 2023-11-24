<?php
/*
* All terms dropdown
*/
?>


<div class="filter-search-category-select all-cats active-cat-dropdown">

    <select name="all-categ" id="categ ">
        <option value="" name="All">All categories</option>
        <?php
        $args = array('hide_empty' => 1);

        $allcat = array('family', 'used-family');
        $terms = get_terms($allcat, $args);
        if (!empty($terms) && !is_wp_error($terms)) {
            $count = count($terms);
            $term_list = '';
            foreach ($terms as $term) {
                $term_slug_title = str_replace('-', ' ', $term->slug);
                $term_title_clean = ucwords($term_slug_title);
                $term_list .= '<option  value="' . esc_url(get_term_link($term)) . '">' . $term->name . '</option>';
            }
            echo $term_list;
        }
        ?>
    </select>
</div>
