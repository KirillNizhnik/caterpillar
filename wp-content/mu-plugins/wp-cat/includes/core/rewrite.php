<?php
namespace Cat\Core;
use Cat\Core\Post_types;

class Rewrite
{

    private static $instance;

    private function __construct()
    {
        add_action( 'init', array($this, 'add_rewrite_endpoints') );
        add_filter( 'query_vars', array( $this, 'add_query_vars'), 0 );
        add_filter( 'rewrite_rules_array', array($this, 'add_rewrite_rules'));

        $this->init_query_vars();
    }

    /**
     * Singleton design pattern
     * only allows one instance of the class to be created.
     *
     * @return instance singleton instance of class
     */

    public static function instance()
    {
        if (!isset(self::$instance)) {
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }


    /**
     * Init query vars by loading options.
     */
    public function init_query_vars()
    {
        // Query vars to add to WP
        $this->query_vars = array(
            // My account views
            'equipment-search' => 'equipment-search'
            ,'products'         => 'products'
            ,'application'      => 'application'
            ,'cat_class'        => 'cat_class'
            ,'cat_rental'       => 'cat_rental'
        );
    }

    public function add_rewrite_endpoints()
    {
        if( defined('EP_INDUSTRY') )
        {
            add_rewrite_endpoint( 'equipment', EP_INDUSTRY );
            add_rewrite_endpoint( 'products', EP_INDUSTRY );
        }
    }

    public function add_query_vars( $vars )
    {
        foreach ( $this->query_vars as $key => $var )
            $vars[] = $var;
        return $vars;
    }

    public function add_rewrite_rules($rules)
    {
        $classes       = get_option('cat_new_class_limitation');
        $industry_slug = get_option('cat_industry_slug') ? get_option('cat_industry_slug') : 'industries';

        $custom = array(
            //'equipment/search' => 'index.php?equipment-search=true'
        );

        if( defined('EP_INDUSTRY') )
        {
            $custom[$industry_slug.'/([^/]+)/application/all/?$'] = 'index.php?cat_industry=$matches[1]&application=all';
        }

        if(!empty($classes)) {

            // Find all the __cat_type_slug__ rules (default post generated)
            $cat_type_slug_rules = array();
            foreach ($rules as $rule => $url)
            {
                if (preg_match('/__cat_type_slug_[^_]+__/', $rule))
                {
                    $cat_type_slug_rules[$rule] = $url;

                    // Remove the generic rules after finding them
                    unset($rules[$rule]);
                }
            }


            foreach(CAT()->get_available_classes() as $id => $class){
                if (!in_array($id, $classes)) continue;

                // Rental Product List
                $base = get_option(CAT()->get_class_post_type_relation($id).'_rental_slug');
                if (empty($base))
                    $base = Post_types::get_class_default_slug($id, 'rental');

                // Add all the specific posttype rules
                foreach ($cat_type_slug_rules as $rule => $url)
                {
                    $id_slug = '__cat_type_slug_' . $id . '__';
                    if (strpos($rule, $id_slug) !== false)
                    {
                        $specific_rule = str_replace($id_slug, $base, $rule);
                        $specific_url = $url . "&cat_rental=1";
                        $custom[$specific_rule] = $specific_url;
                    }
                }

                // - Unpaged
                $unpaged_base = $base . '/([^/]+)/?$';
                $custom[$unpaged_base] = 'index.php?'.CAT()->get_class_post_type_relation($id).'_rental_family=$matches[1]&cat_rental=1';
                // - Paged
                $base .= '/([^/]+)/page/([0-9]{1,})/?$';
                $custom[$base] = 'index.php?'.CAT()->get_class_post_type_relation($id).'_rental_family=$matches[1]&paged=$matches[2]&cat_rental=1';

                // New Product List - paged
                $base = get_option(CAT()->get_class_post_type_relation($id).'_slug');
                if (empty($base))
                    $base = Post_types::get_class_default_slug($id);

                // Add all the specific posttype rules
                foreach ($cat_type_slug_rules as $rule => $url)
                {
                    $id_slug = '__cat_type_slug_' . $id . '__';
                    if (strpos($rule, $id_slug) !== false)
                    {
                        $specific_rule = str_replace($id_slug, $base, $rule);
                        $specific_url = $url . "&cat_rental=0";
                        $custom[$specific_rule] = $specific_url;
                    }
                }

                // - Unpaged
                $unpaged_base = $base . '/([^/]+)/?$';
                $custom[$unpaged_base] = 'index.php?'.CAT()->get_class_post_type_relation($id).'_family=$matches[1]&cat_rental=0';
                // - Paged
                $base .= '/([^/]+)/page/([0-9]{1,})/?$';
                $custom[$base] = 'index.php?'.CAT()->get_class_post_type_relation($id).'_family=$matches[1]&paged=$matches[2]&cat_rental=0';
            }
        }

        $custom = apply_filters('cat_rewrite_rules', $custom+$rules);

        return $custom;
    }

}

Rewrite::instance();
