<?php

class ImporterHandler
{


    public function __construct()
    {
    }

    public function importCategories($categories, $taxonomy, $parent_term_id = 0 ): void
    {
        foreach ($categories as $category => $data) {
            $children = $data['children'] ?? array();
            $category_term = $this->getOrCreateTerm($category, $taxonomy, $parent_term_id );

            if (isset($category_term['term_id']) && !empty($children)) {
                $category_id = $category_term['term_id'];
                $this->importCategories($children, $taxonomy, $category_id);
            }
        }
    }



    private function getOrCreateTerm($term_name, $taxonomy, $parent_term_id = 0)
    {
        $category_term = term_exists($term_name, $taxonomy, $parent_term_id);
        if (!$category_term) {
            $category_term = wp_insert_term($term_name, $taxonomy, array('parent' => $parent_term_id));
        }
        return $category_term;
    }
}