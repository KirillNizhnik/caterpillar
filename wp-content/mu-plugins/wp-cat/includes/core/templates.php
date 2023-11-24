<?php

namespace Cat\Core;

class Templates
{
    private static $instance;
    private $set_variables = false;


    private function __construct()
    {
        add_action('pre_get_posts', array($this, 'set_variables'), 1);
        add_filter('pre_get_posts', array($this, 'order_products_by_sort'));

        add_filter('template_include', array($this, 'set_template'));
    }

    /**
     * Singleton design pattern
     * only allows one instance of the class to be created.
     *
     * @return instance singleton instance of class
     * @since 1.0.0
     * @ignore
     */

    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Sets cat variables into query
     * @param [type] $wp_query [description]
     */
    public function set_variables($wp_query)
    {/*{{{*/

        if (!$wp_query->is_main_query()) {
            return;
        }
        $object = get_queried_object();
        $wp_query->is_cat = false;
        $wp_query->is_cat_new = false;
        $wp_query->is_cat_rental = false;
        $wp_query->is_cat_allied = false;
        $wp_query->is_cat_used = false;
        $wp_query->is_cat_industry = false;
        $wp_query->is_cat_application = false;

        if ($this->set_variables)
            return;

        if (isset($wp_query->query_vars['post_type'])) {
            if ($wp_query->query_vars['post_type'] === 'equipment' || $wp_query->query_vars['post_type'] === 'used-equipment') {
                $wp_query->is_cat = true;
                if ($wp_query->query_vars['post_type'] === 'equipment') {
                    $wp_query->is_cat_new = true;

                    if ($wp_query->query_vars['post_type'] === 'cat_industry') {
                        $wp_query->is_cat_industry = true;
                    }

                    if ($wp_query->query_vars['post_type'] === 'cat_application') {
                        $wp_query->is_cat_application = true;
                    }

                    if ($wp_query->query_vars['post_type'] === 'cat_new_allied') {
                        $wp_query->is_cat_allied = true;
                    }
                }

                if ($wp_query->query_vars['post_type'] === 'used-equipment') {
                    $wp_query->is_cat_used = true;
                }
            }
        } elseif (isset($object->taxonomy)) {
            if (is_string($object->taxonomy)) {
                $wp_query->is_cat = true;

                if ($object->taxonomy === 'family') {
                    $wp_query->is_cat_new = true;
                }

                if ($object->taxonomy === 'used-family') {
                    $wp_query->is_cat_used = true;
                }

                if (strpos($object->taxonomy, 'cat_new_allied') === 0) {
                    $wp_query->is_cat_allied = true;
                }
            }
        } elseif (!empty($wp_query->query_vars['cat_class'])) {
            $wp_query->is_cat = true;
            $wp_query->is_cat_class = true;
        }

        // Rental can be on in addition to new
        // Products are considered new AND rentable
        if (!empty($wp_query->query_vars['cat_rental'])) {
            $wp_query->is_cat_rental = true;
        }

        $this->set_variables = true;
    }/*}}}*/


    /**
     * Checks to see if appropriate templates are present in active template directory.
     * Otherwises uses templates present in plugin's template directory.
     * Hooked onto template_include'
     *
     * @param string $template Absolute path to template
     * @return string Absolute path to template
     * @ignore
     * @since 1.0.0
     */

    public function set_template($template)
    {
        global $wp_query;

        $object = get_queried_object();
        $base = substr(basename($template), 0, -4);

        if (get_query_var('equipment-search'))
            return self::view('search');

        if (is_single()) {
            if ($object->post_type === 'cat_industry') {
                if (isset($wp_query->query_vars['products'])) {
                    $template = self::view('industry/products');
                } elseif (!empty($wp_query->query_vars['application'])) {
                    $template = self::view('industry/applications');
                } elseif (isset($wp_query->query_vars['equipment'])) {
                    $template = self::view('industry/equipment');
                } else {
                    $_template = get_post_meta($object->ID, '_template', true);
                    $template = ($_template)
                        ? self::view('industry/' . $_template)
                        : self::view('industry/single');
                }
            } elseif ($object->post_type === 'cat_application') {
                $template = self::view('industry/application');
            } elseif ($object->post_type === 'used-equipment') {
                $template = self::view('used/single');
            } elseif ($object->post_type === 'equipment') {
                if ($override = cat_locate_template('cat/new/single-' . $object->post_type . '.php')) {
                    $template = $override;
                } else {
                    $template = self::view('new/single');
                }
            }
        } elseif (is_tax()) {

            if (strpos($object->taxonomy, 'family') === 0) {
                if ($override = cat_locate_template('cat/new/taxonomy-' . $object->taxonomy . '.php')) {
                    $template = $override;
                } else {
                    $template = self::view('new/taxonomy');
                }
            } elseif (strpos($object->taxonomy, 'used-family') === 0) {
                if ($override = cat_locate_template('cat/used/taxonomy-' . $object->taxonomy . '.php')) {
                    $template = $override;
                } else {
                    $template = self::view('used/taxonomy');
                }
            }
        }

        return $template;
    }


    public function order_products_by_sort($wp_query)
    {
        if (!empty($wp_query->tax_query)
            && !empty($wp_query->tax_query->queries)
            && !is_admin()) {
            if ($wp_query->is_main_query()
                && $wp_query->tax_query->queries[0]['taxonomy'] === 'family') {
                if(get_field('show_subfamilies', get_queried_object()) != 'ON') {
                    $wp_query->set('orderby', 'title');
                    $wp_query->set('order', 'ASC');
                    $wp_query->set('posts_per_page', 999);
                }
            }

            if ($wp_query->is_main_query()
                && $wp_query->tax_query->queries[0]['taxonomy'] === 'used-family') {
                $wp_query->set('posts_per_page', 9999);
            }
        }
    }

    public static function view($template)
    {
        $lookup = array('cat/' . $template);

        if (is_single()) {
            global $post;
            array_unshift($lookup, 'cat/' . $template . '-' . $post->post_name);
        }

        foreach ($lookup as &$template) {
            if (substr($template, -4) != '.php')
                $template .= '.php';
        }

        if ($theme_file = cat_locate_template($lookup)) {
            $file = $theme_file;
        } else {
            $file = CAT()->plugin_path . 'templates/' . $template;
        }

        return $file;
    }


    public static function asset($asset)
    {
        if (substr($asset, -3) == '.js')
            $folder = 'js/';
        else
            $folder = 'css/';


        if ($theme_file = cat_locate_template(array('cat/' . $asset))) {
            $file = get_stylesheet_directory_uri() . '/cat/' . $asset;

        } else {

            if (strpos($asset, $folder) !== 0) {
                $asset = $folder . $asset;
            }

            $file = CAT()->plugin_url . 'assets/' . $asset;
        }

        return $file;
    }

}

Templates::instance();
