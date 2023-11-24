<?php
namespace Cat\Core;

class Breadcrumbs
{
    private static $instance;


    private function __construct()
    {
        add_filter( 'wpseo_breadcrumb_links', array($this, 'breadcrumbs') );
        add_filter( 'wpseo_title', array($this, 'title') );
    }

    /**
     * Singleton design pattern
     * only allows one instance of the class to be created.
     *
     * @ignore
     * @since 1.0.0
     * @return instance singleton instance of class
     */

    public static function instance()
    {
        if(!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    public function breadcrumbs($crumbs)
    {/*{{{*/
        global $wp_query;

        if( is_single() && is_cat_used() )
            $crumbs = $this->cat_used_single_crumbs($crumbs);

        elseif( is_tax() && is_cat_used() )
            $crumbs = $this->cat_used_tax_crumbs($crumbs);

        elseif( is_single() && is_cat_application() )
            $crumbs = $this->cat_application_crumbs($crumbs);

        elseif( is_single() && is_cat_industry() )
            $crumbs = $this->cat_industry_crumbs($crumbs);

        elseif( is_single() && is_cat_new() && ! is_cat_rental() && ! is_cat_allied() )
            $crumbs = $this->cat_new_single_crumbs($crumbs);

        elseif( is_single() && is_cat_rental() )
            $crumbs = $this->cat_single_rental_crumbs($crumbs);

        elseif( is_single() && is_cat_allied() )
            $crumbs = $this->cat_single_allied_crumbs($crumbs);

        elseif( is_tax() && is_cat_new() && ! is_cat_rental() && ! is_cat_allied() )
            $crumbs = $this->cat_new_tax_crumbs($crumbs);

        elseif( is_tax() && is_cat_rental() )
            $crumbs = $this->cat_tax_rental_crumbs($crumbs);

        elseif( is_tax() && is_cat_allied() )
            $crumbs = $this->cat_tax_allied_crumbs($crumbs);


        if( is_equipment_search() )
        {
            array_pop($crumbs);

            $crumbs[] = array(
                'text' => 'Product Search Results'
            );
        }

        return $crumbs;
    }

    private function cat_used_single_crumbs($crumbs)
    {
        $home_link = array_shift($crumbs);

        // remove unwanted post archive
        array_shift($crumbs);

        // Append Used to first Family
        foreach( $crumbs as &$link ) {
            if( isset( $link['term'] ) && 0 === $link['term']->parent ) {
                $link['term']->name = 'Used '.$link['term']->name;
            }
        }

        array_unshift(
            $crumbs,
            $home_link,
            array(
                'text' => 'Equipment'
                ,'url' => site_url('equipment/')
                ,'allow_html' => true
            ),
            array(
                'text' => 'Machines'
                ,'url' => site_url('/equipment/machines/')
                ,'allow_html' => true
            )
        );

        return $crumbs;
    }

    private function cat_used_tax_crumbs($crumbs)
    {
        //echo '<pre>'; var_dump($crumbs); echo '</pre>';

        $home_link = array_shift($crumbs);

        // Append Used to first Family
        foreach( $crumbs as &$link ) {
            if( 0 === $link['term']->parent ) {
                $link['term']->name = 'Used '.$link['term']->name;
            }
        }

        // Add Equipment pages before families
        array_unshift(
            $crumbs,
            $home_link,
            array(
                'text' => 'Equipment'
                ,'url' => site_url('equipment/')
                ,'allow_html' => true
            ),
            array(
                'text' => 'Machines'
                ,'url' => site_url('/equipment/machines/')
                ,'allow_html' => true
            )
        );

        return $crumbs;
    }


    private function cat_new_single_crumbs($crumbs)
    {
        global $post;
        $home_link = array_shift($crumbs);

        // removes base crumbs
        array_shift($crumbs);
        array_shift($crumbs);
        array_shift($crumbs);

        $terms = get_the_terms( $post, $post->post_type.'_family' );

        usort($terms, function($a, $b) {
            if( $a->parent === $b->parent)
                return 0;

            return $a->parent < $b->parent ? -1 : 1;
        });

        // Add Equipment pages before families
        array_unshift(
            $crumbs,
            $home_link,
            array(
                'text' => 'Equipment'
                ,'url' => site_url('equipment/')
                ,'allow_html' => true
            ),
            array(
                'text' => 'Machines'
                ,'url' => site_url('/equipment/machines/')
                ,'allow_html' => true
            ),
            array(
                'text' => 'New Machinery'
                ,'url' => site_url('/equipment/machines/new/')
                ,'allow_html' => true
            ),
            array(
                'term' => $terms[0]
            )
        );

        return $crumbs;
    }

    private function cat_new_tax_crumbs($crumbs)
    {
        $home_link = array_shift($crumbs);

        // drop archive link
        array_shift($crumbs);

        // Add Equipment pages before families
        array_unshift(
            $crumbs,
            $home_link,
            array(
                'text' => 'Equipment'
                ,'url' => site_url('equipment/')
                ,'allow_html' => true
            ),
            array(
                'text' => 'Machines'
                ,'url' => site_url('/equipment/machines/')
                ,'allow_html' => true
            ),
            array(
                'text' => 'New Machinery'
                ,'url' => site_url('/equipment/machines/new/')
                ,'allow_html' => true
            )
        );

        return $crumbs;
    }



    private function cat_application_crumbs($crumbs)
    {
        $application = CAT()->application();
        $industry    = CAT()->industry($application->industry->ID);

        $home_link = array_shift($crumbs);

        $equipment_link = array(
            'text' => 'Industries'
            ,'url' => site_url('industries/' )
            ,'allow_html' => true
        );

        $industry_link = array(
            'text' => $industry->name
            ,'url' => $industry->url
            ,'allow_html' => true
        );

        $applications_link = array(
            'text' => 'Applications'
            ,'url' => $industry->get_applications_link()
            ,'allow_html' => true
        );

        // shift links back on to the front
        array_unshift(
            $crumbs,
            $home_link,
            $equipment_link,
            $industry_link,
            $applications_link
        );

        return $crumbs;
    }

    private function cat_industry_crumbs($crumbs)
    {
        global $wp_query;
        $industry = CAT()->industry();

        $home_link = array_shift($crumbs);

        $equipment_link = array(
            'text' => 'Industries'
            ,'url' => site_url('/equipment/by-industry/')
            ,'allow_html' => true
        );

        // shift links back on to the front
        array_unshift($crumbs, $home_link, $equipment_link);

        if(isset($wp_query->query_vars['products']))
        {
            array_pop($crumbs);

            $crumbs[] = array(
                'text' => $industry->name
                ,'url' => $industry->url
                ,'allow_html' => true
            );

            $crumbs[] = array(
                'text' => 'Products'
            );
        }
        elseif(! empty($wp_query->query_vars['application']))
        {
            array_pop($crumbs);

            $crumbs[] = array(
                'text' => $industry->name
                ,'url' => $industry->url
                ,'allow_html' => true
            );

            $crumbs[] = array(
                'text' => 'Applications'
            );
        }
        elseif(isset($wp_query->query_vars['equipment']))
        {
            array_pop($crumbs);

            $crumbs[] = array(
                'text' => $industry->name
                ,'url' => $industry->url
                ,'allow_html' => true
            );

            $crumbs[] = array(
                'text' => 'Equipment'
            );
        }

        return $crumbs;
    }

    private function cat_single_rental_crumbs($crumbs)
    {
        global $post;
        $home_link = array_shift($crumbs);

        // removes new machine archive
        array_shift($crumbs);

        $equipment_link = array(
            'text' => 'Rental Products'
            ,'url' => home_url('/rental-products/')
            ,'allow_html' => true
        );

        $terms = get_the_terms( $post, $post->post_type.'_rental_family' );

        $tax_link = array(
            'text' => $terms[0]->name
            ,'url' => get_term_link( $terms[0] )
            ,'allow_html' => true
        );

        array_unshift($crumbs, $home_link, $equipment_link, $tax_link);

        return $crumbs;
    }


    private function cat_tax_rental_crumbs($crumbs)
    {
        $home_link = array_shift($crumbs);

        $equipment_link = array(
            'text' => 'Rental Products'
            ,'url' => home_url('/rental-products/' )
            ,'allow_html' => true
        );

        array_unshift($crumbs, $home_link, $equipment_link);

        return $crumbs;
    }

    private function cat_single_allied_crumbs($crumbs)
    {
        global $post;
        $home_link = array_shift($crumbs);

        // removes base crumbs
        array_shift($crumbs);
        array_shift($crumbs);
        array_shift($crumbs);

        $terms = get_the_terms( $post, $post->post_type.'_family' );

        usort($terms, function($a, $b) {
            if( $a->parent === $b->parent)
                return 0;

            return $a->parent < $b->parent ? -1 : 1;
        });

        // Add Equipment pages before families
        array_unshift(
            $crumbs,
            $home_link,
            array(
                'text' => 'Equipment'
                ,'url' => site_url('equipment/')
                ,'allow_html' => true
            ),
            array(
                'text' => 'Machines'
                ,'url' => site_url('/equipment/machines/')
                ,'allow_html' => true
            ),
            array(
                'text' => 'Quinn Rental Services'
                ,'url' => site_url('/equipment/machines/rental-store/')
                ,'allow_html' => true
            ),
            array(
                'term' => $terms[0]
            )
        );

        return $crumbs;
    }


    private function cat_tax_allied_crumbs($crumbs)
    {
        $home_link = array_shift($crumbs);

        // drop archive link
        array_shift($crumbs);

        // Add Equipment pages before families
        array_unshift(
            $crumbs,
            $home_link,
            array(
                'text' => 'Equipment'
                ,'url' => site_url('equipment/')
                ,'allow_html' => true
            ),
            array(
                'text' => 'Machines'
                ,'url' => site_url('/equipment/machines/')
                ,'allow_html' => true
            ),
            array(
                'text' => 'Quinn Rental Services'
                ,'url' => site_url('/equipment/machines/rental-store/')
                ,'allow_html' => true
            )
        );

        return $crumbs;
    }




    public function title($title)
    {
        global $wp_query;
        if ($wp_query->is_cat_rental)
        {
            $title = str_replace("For Sale", "For Rent", $title);
        }

        return $title;
    }


}

Breadcrumbs::instance();
