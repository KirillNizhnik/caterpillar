<?php
namespace Cat\Models;

class Application extends View
{
    public $id;
    public $name;
    public $header;
    public $products = array();
    public $industry;

    protected $join_key = 'application_id';

    public function __construct( $application )
    {
        $this->id   = $application->ID;
        $this->name = get_the_title( $this->id );
        $this->url  = get_permalink( $this->id );

        $this->header();

        if(is_single()) {
            $this->with(array('industry', 'products'));
        }

        return $this;
    }

    public function header()
    {
        $thumbnail_id = get_post_thumbnail_id( $this->id );

        if($thumbnail_id)
        {
            $header_url_array = wp_get_attachment_image_src( $thumbnail_id, 'full', true );

            $this->header      = new \stdClass();
            $this->header->src = $header_url_array[0];
        }
        else
        {
            $this->industry();
            $industry = CAT()->industry($this->industry->id);
            $this->header = $industry->header();
        }

        return $this->header;
    }



    public function industry()
    {
        if( empty($this->join) )
            $this->get_join_table();

        $industry_id = pluck('industry_id', $this->join);
        $industry_id = array_shift($industry_id);
        $this->industry = get_post($industry_id);

        return $this->industry;
    }

    public function products()
    {
        if( empty($this->join) )
            $this->get_join_table();

        if( ! empty($this->products) )
            return $this->products;

        $family_ids = array_filter(pluck_where('object_id', array('object_type' => 'term'), $this->join));
        $terms      = ( ! empty($family_ids) )
                        ? get_terms(
                            array(
                                'cat_new_machine_family'
                                ,'cat_new_power_family'
                                ,'cat_new_attachment_family'
                            ),
                            array('include' => $family_ids)
                        )
                        : array();

        foreach($terms as &$term) {
            $term->type = 'term';
        }

        $page_ids = array_filter(pluck_where('object_id', array('object_type' => 'page'), $this->join));
        $page_ids = ltrim(implode(',', $page_ids), ',');
        $pages    = ( ! empty($page_ids) )
                      ? get_pages( array('include' => $page_ids) )
                      : array();

        foreach($pages as &$page) {
            $page->name = $page->post_title;
            $page->type = 'page';
        }


        $this->products = array_merge($terms, $pages);

        usort($this->products, function($a, $b) {
            return strcmp($a->name, $b->name);
        });


        return $this->products;
    }
}