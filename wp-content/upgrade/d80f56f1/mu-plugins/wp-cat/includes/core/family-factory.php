<?php
namespace Cat\Core;

class Family_Factory extends \Cat\Core\Abstracts\Factory
{

    /**
     * Get an item from the collection by key.
     *
     * @param  mixed  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get( $the_family = false, $default = null )
    {
        $the_family = $this->get_object($the_family);

        if ( ! $the_family )
        {
            return false;
        }

        return new \Cat\Models\Family($the_family);
    }


    private function get_object($the_family)
    {
        if ( false === $the_family )
        {
            $the_family = get_queried_object();
            if (!isset( $the_family->term_id))
                return false;
        }
        elseif ( is_numeric( $the_family ) )
        {
            $term = false;
            $taxonomies = CAT()->get_class_post_type_relation();
            $taxonomies[] = 'cat_used_machine';

            foreach( $taxonomies as $type )
            {
                if( $term = get_term_by('id', $the_family, $type.'_family') )
                    break;
            }

            $the_family = $term;
        }
        elseif ( $the_family instanceof \Cat\Models\Family )
        {
            $the_family = $the_family->wp_term;
        }
        elseif ( ! isset($the_family->term_id) )
        {
            return false;
        }

        if ( !isset($the_family->ID) )
            $the_family->ID = $the_family->term_id;

        if( strpos($the_family->taxonomy, 'cat_') !== 0 )
        {
            $the_family = false;
        }

        return $the_family;
    }

}
