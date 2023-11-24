<?php

abstract class AbstractEquipmentPostType{
    /**
     * Plugin instance.
     *
     * @see instance()
     */

    protected string $postType;
    protected string $familyTaxonomy;
    protected string $manufacturerTaxonomy;

    public function __construct()
    {
        $this->postType = $this->getPostType();
        $this->familyTaxonomy = $this->getFamilyTax();
        $this->manufacturerTaxonomy = $this->getManufacturerTax();
        add_action('init', [$this, 'register']);
        add_action('init', [$this, 'registerFamilyTaxonomy']);
        add_action('init', [$this, 'registerManufacturerTaxonomy']);
        add_action('restrict_manage_posts', function() {
            $this->filter_post_type_by_taxonomy($this->familyTaxonomy);
            $this->filter_post_type_by_taxonomy($this->manufacturerTaxonomy);
        });
        add_filter('parse_query', array($this,'convert_id_to_term_in_query'));

    }



    abstract public function register(): void;

    abstract public function registerFamilyTaxonomy(): void;

    abstract public function registerManufacturerTaxonomy(): void;

    abstract public function getPostType():string;

    abstract public function getFamilyTax():string;

    abstract public function getManufacturerTax():string;

    public function filter_post_type_by_taxonomy($taxonomy)
    {
        global $typenow;
        $post_type = $this->postType;

        if ($typenow == $post_type) {
            $selected = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
            wp_dropdown_categories(array(
                'show_option_all' => 'All ' . ucfirst($taxonomy), // Добавляет "All" + название таксономии
                'taxonomy' => $taxonomy,
                'name' => $taxonomy,
                'orderby' => 'name',
                'selected' => $selected,
                'show_count' => true,
                'hide_empty' => false,
                'hierarchical' => true,
                'tab_index' => true
            ));
        }
    }

    public function convert_id_to_term_in_query($query)
    {
        global $pagenow;
        $post_type = $this->postType; // change to your post type
        $taxonomy = $this->familyTaxonomy; // change to your taxonomy
        $q_vars = &$query->query_vars;
        if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0) {
            $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
            $q_vars[$taxonomy] = $term->slug;
        }
    }



}