<?php
namespace Cat\Models;

class View
{
    public  $header = false;
    public  $thumbnail = false;
    protected $join = array();

    public function with($with)
    {
        if(is_array($with))
        {
            foreach($with as $func)
            {
                call_user_func(array($this, $func));
            }
        }
        else
        {
            call_user_func(array($this, $with));
        }

        return $this;
    }

    protected function get_size_string($size)
    {
        global $_wp_additional_image_sizes;
        $sizer = '';

        if($size == 'full'){
            return '';
        } else {

            if(is_string($size)){
                if( isset( $_wp_additional_image_sizes )
                    AND isset( $_wp_additional_image_sizes[ $size ] )
                ){
                   $size = array(
                        $_wp_additional_image_sizes[ $size ]['width']
                        ,$_wp_additional_image_sizes[ $size ]['height']
                    );
                }
            }

            if(is_array($size))
            {
                $sizer .= '?';
                $width = false;

                if(intval($size[0]) != 9999){
                    $sizer .= 'wid='.$size[0];
                    $width=true;
                }
                if(intval($size[1]) != 9999){
                    $sizer .= ($width) ? '&' : '';
                    $sizer .='hei='.$size[1];
                }

                $sizer .='&op_sharpen=1&qlt=100';
            }
        }

        return $sizer;
    }


    protected function get_join_table()
    {
        global $wpdb;

        $results = $wpdb->get_results(
            "SELECT *
             FROM {$wpdb->prefix}cat_term_industries
             WHERE {$this->join_key} = {$this->id}
             "
        );

        // echo '<pre>'; var_dump($wpdb->last_query); echo '</pre>'; die;

        if($results)
            $this->join = $results;
    }


    /**
     * Check if the family has a header image
     * @return boolean [description]
     */
    public function has_header()
    {
        return $this->header !== false;
    }


    /**
     * Check if the family has a thumbnail
     * @return boolean [description]
     */
    public function has_thumbnail()
    {
        return $this->thumbnail !== false;
    }



    /**
     * format custom data
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    protected function setup_custom_data($data)
    {
        $return = array();

        foreach($data as $k => $prop)
        {
            $return[$k] =  maybe_unserialize(array_shift($prop));
        }

        $return = $this->setup_image_data($return);

        return $return;
    }

    protected function setup_image_data($data)
    {

        // echo '<pre>'; var_dump($data); echo '</pre>'; die;

        if( ! empty($data['images']) )
        {
            foreach($data['images'] as &$image)
            {
                if( is_numeric($image) )
                {
                    $ID  = $image;
                    $src = wp_get_attachment_image_src($image, 'full');

                    $image       = new \stdClass();
                    $image->src  = $src[0];
                    $image->type = 'wp_attachment';
                    $image->ID   = $ID;
                }
            }
        }

        //echo '<pre>'; var_dump($data); echo '</pre>'; die;


        if(! isset($data['additional-images']) )
            return $data;

        foreach($data['additional-images'] as $image)
        {
           
            $ID  = $image;
            $src = wp_get_attachment_image_src($image, 'full');

            $image       = new \stdClass();
            $image->src  = $src[0];
            $image->type = 'wp_attachment';
            $image->ID   = $ID;
            if(is_string($data['images'])) {
                $data['images'] = array();
            }
            $data['images'][] = $image; //current
            //array_push(data['images'], $image); //what I tried 
        }

        unset($data['additional-images']);

        return $data;
    }
}
