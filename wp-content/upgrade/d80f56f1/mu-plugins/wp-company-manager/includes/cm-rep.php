<?php

class CM_Rep
{
    public function __construct( $rep )
    {
        $this->id        = $rep->ID;
        $this->post      = $rep;
        $this->name      =  get_the_title( $this->id );

        $this->phone    = get_post_meta( $this->id, 'phone', true );
        $this->phone_link = 'tel:' . preg_replace( '/\D/', '', $this->phone );
        $this->email    = get_post_meta( $this->id, 'email', true );
        $this->title    = get_post_meta( $this->id, 'title', true );

        $industries = wp_get_object_terms( $this->id, 'rep_industry' );
        // $this->industry = empty( $industries ) ? '' : $industries[0]->name;

		$primary_image      = get_post_meta($rep->ID, '_thumbnail_id',true);

        // icon of placeholder person
        if( empty( $primary_image ) ) {
            $primary_image = 16293;
        }

		$this->photo 	 	= wp_get_attachment_url( $primary_image, 'full');

    }


    /**
     * format custom data
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    private function setup_custom_data($data)
    {
        $return = array();

        foreach($data as $k => $prop)
        {
            $return[$k] =  maybe_unserialize(array_shift($prop));
        }

        return $return;
    }
}
