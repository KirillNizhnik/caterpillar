<?php

class CM_Rep_Factory extends CM_Abstract_Factory
{

    /**
     * Get an item from the collection by key.
     *
     * @param  mixed  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get( $the_rep = false, $default = null )
    {
        $the_rep = $this->get_object($the_rep);

        if ( ! $the_rep )
        {
            return false;
        }


        if( $this->contains('id', $the_rep->ID) )
        {
            return $this->where('id', $the_rep->ID);
        }

        if ($this->offsetExists($the_rep->ID))
        {
            return $this->items[$the_rep->ID];
        }

        $this->add( new CM_Rep($the_rep) );

        return $this->last();
    }


    public function get_by_name($name)
    {
        global $wpdb;

        $rep   = false;
        $ids   = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_title = '$name' AND post_status='publish'");
        $query = new WP_Query(array(
            'post_type'       => 'representative'
            ,'posts_per_page' => 1
            ,'post__in' => $ids
        ));

        while($query->have_posts())
        {
            $query->the_post();

            $the_rep = $this->get_object($the_rep);
            $this->add( new CM_Rep($the_rep) );
        }
        wp_reset_postdata();

        return $this->last();
    }



    public function get_by_zip( $zipcode )
    {
        $rep   = false;
        $query = new WP_Query(array(
            'post_type'       => 'rep'
            ,'posts_per_page' => 1
            ,'meta_key'       => 'zipcodes'
            ,'meta_value'     => $zipcode
            ,'meta_compare'   => 'LIKE'
        ));

        if( $query->have_posts() )
        {
            while( $query->have_posts() )
            {
                $query->the_post();

                $the_rep = $this->get_object( get_the_id() );
                $this->add( new CM_Rep($the_rep) );
            }
        }

        return $this->last();
    }

    public function get_by_location($location)
    {
        $query = new WP_Query(array(
            'post_type'       => 'rep'
            ,'posts_per_page' => 1
            ,'meta_key'       => 'zipcodes'
            ,'meta_value'     => $location->postal_code
            ,'meta_compare'   => 'LIKE'
        ));


        while( $query->have_posts() )
        {
            $query->the_post();

            $the_rep = $this->get_object( get_the_id() );

            $this->add( new CM_Rep($the_rep) );
        }
        wp_reset_postdata();
    }


    private function get_object($the_rep)
    {
        if ( false === $the_rep )
        {
            $the_rep = $GLOBALS['post'];
        }
        elseif ( is_numeric( $the_rep ) )
        {
            $the_rep = get_post( $the_rep );
        }
        elseif ( $the_rep instanceof CM_Rep )
        {
            $the_rep = get_post( $the_rep->id );
        }
        elseif ( ! ( $the_rep instanceof \WP_Post ) )
        {
            $the_rep = false;
        }

        if( $the_rep->post_type !== 'rep' )
        {
            $the_rep = false;
        }

        return $the_rep;
    }

}