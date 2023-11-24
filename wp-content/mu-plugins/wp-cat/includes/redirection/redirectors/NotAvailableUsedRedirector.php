<?php

class NotAvailableUsedRedirector extends RedirectorAbstract
{

    public function redirect(): void
    {
        $url = $this->getMostSuitableUrl('used-family', 'used-equipment');
        if($url !== null){
            $this->makeRedirect($url);
        }
    }

    public function getMostSuitableUrl(string $taxonomy, string $postType): ?string
    {
        $object = get_queried_object();
        if (!$object instanceof WP_Post) {
            return null;
        }
        if ($object->post_type === $postType) {
            $post_id = $object->ID;
            $status = get_post_meta($post_id, 'used_status', true);
            if ($status === 'not_available') {
                $terms = get_post_meta($post_id, 'array_fam', true);
                if (!empty($terms)) {
                    $validTerms = get_terms([
                        'taxonomy' => $taxonomy,
                        'hide_empty' => 1,
                        'term_taxonomy_id' => $terms,
                        'fields' => 'ids'
                    ]);
                    if (!empty($validTerms)) {
                        $validTerms = array_values($validTerms);
                        $redirect_term_id = $validTerms[count($validTerms) - 1];
                        return get_term_link($redirect_term_id, 'used-family');
                    }
                }
                return get_home_url() . '/used/';
            }
        }
        return null;
    }
}