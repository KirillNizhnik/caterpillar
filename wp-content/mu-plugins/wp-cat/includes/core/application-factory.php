<?php
namespace Cat\Core;

class Application_Factory extends \Cat\Core\Abstracts\Factory
{

    /**
     * Get an item from the collection by key.
     *
     * @param  mixed  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get( $the_app = false, $default = null )
    {
        $the_app = $this->get_object($the_app);

        if ( ! $the_app )
        {
            return false;
        }

        if( $this->contains('id', $the_app->ID) )
        {
            return $this->where('id', $the_app->ID);
        }

        if ($this->offsetExists($the_app->ID))
        {
            return $this->items[$the_app->ID];
        }

        $this->add( new \Cat\Models\Application($the_app) );
        return $this->last();
    }


    private function get_object($the_app)
    {
        if ( false === $the_app )
        {
            $the_app = $GLOBALS['post'];
        }
        elseif ( is_numeric( $the_app ) )
        {
            $the_app = get_post( $the_app );
        }
        elseif ( $the_app instanceof \Cat\Models\Application )
        {
            $the_app = get_post( $the_app->id );
        }
        elseif ( ! ( $the_app instanceof \WP_Post ) )
        {
            $the_app = false;
        }

        if( strpos($the_app->post_type, 'cat_') !== 0 )
        {
            $the_app = false;
        }

        return $the_app;
    }

}