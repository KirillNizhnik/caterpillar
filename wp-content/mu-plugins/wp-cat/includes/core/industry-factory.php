<?php
namespace Cat\Core;

class Industry_Factory extends \Cat\Core\Abstracts\Factory
{

    /**
     * Get an item from the collection by key.
     *
     * @param  mixed  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get( $the_industry = false, $default = null )
    {
        $the_industry = $this->get_object($the_industry);

        if ( ! $the_industry )
        {
            return false;
        }

        if( $this->contains('id', $the_industry->ID) )
        {
            return $this->where('id', $the_industry->ID);
        }

        if ($this->offsetExists($the_industry->ID))
        {
            return $this->items[$the_industry->ID];
        }

        $this->add( new \Cat\Models\Industry($the_industry) );
        return $this->last();
    }


    private function get_object($the_industry)
    {
        if ( false === $the_industry )
        {
            $the_industry = $GLOBALS['post'];
        }
        elseif ( is_numeric( $the_industry ) )
        {
            $the_industry = get_post( $the_industry );
        }
        elseif ( $the_industry instanceof \Cat\Models\Industry )
        {
            $the_industry = get_post( $the_industry->id );
        }
        elseif ( ! ( $the_industry instanceof \WP_Post ) )
        {
            $the_industry = false;
        }

        if( strpos($the_industry->post_type, 'cat_') !== 0 )
        {
            $the_industry = false;
        }

        return $the_industry;
    }

}