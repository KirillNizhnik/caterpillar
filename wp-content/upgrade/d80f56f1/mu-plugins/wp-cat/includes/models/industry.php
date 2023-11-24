<?php
namespace Cat\Models;

class Industry extends View
{
    public $id;
    public $post;
    public $name;
    public $header;
    public $thumbnail;
    public $products     = array();
    public $applications = array();
    public $machines;
    public $featured;
    public $news;

    protected $join_key = 'industry_id';

    public function __construct( $industry )
    {
        $this->id   = $industry->ID;
        $this->post = $industry;
        $this->name = get_the_title( $this->id );
        $this->url  = get_permalink( $this->id );

        $this->header();

        return $this;
    }

    public function header()
    {
        $header_url_array = wp_get_attachment_image_src( get_post_meta( $this->id, 'hero_image', true ), 'full', true );

        $this->header      = new \stdClass();
        $this->header->src = $header_url_array[0];

        return $this->header;
    }

    public function thumbnail()
    {
        $header_url_array = wp_get_attachment_image_src( get_post_thumbnail_id( $this->id ), array(200,200), true );

        $this->thumbnail      = new \stdClass();
        $this->thumbnail->src = $header_url_array[0];

        return $this->thumbnail;
    }


    public function products()
    {
        if( empty($this->join) )
            $this->get_join_table();

        // second test is so that if join truly is empty then
        // we can jsut return and ampty array
        if( empty($this->join) )
            return array();

        if( ! empty($this->products) )
            return $this->products;


        $family_ids = pluck_where('object_id', array('object_type' => 'term'), $this->join);
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



        foreach($terms as &$term){
            $term->type = 'term';
            $term->ID = $term->term_id;
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

    public function get_products_link()
    {
        return trailingslashit( get_permalink($this->id) ).'products/all';
    }


    public function applications()
    {
        if( ! CAT()->usingApplications )
            return false;

        if( empty($this->join) )
            $this->get_join_table();

        // second test is so that if join truly is empty then
        // we can jsut return and ampty array
        if( empty($this->join) )
            return array();

        $application_ids = array_unique(pluck('application_id', $this->join));
        $application_ids = ltrim(implode(',', $application_ids), ',');

        $this->applications = get_posts(array('include' => $application_ids, 'post_type' => 'cat_application'));

        // hardcode worktool page to applications
        $this->applications[] = get_page(4304);

        usort($this->applications, function($a, $b) {
            return strcmp($a->post_title, $b->post_title);
        });

        return $this->applications;
    }


    public function get_applications_link()
    {
        if( ! CAT()->usingApplications )
            return false;

        return trailingslashit( get_permalink($this->id) ).'application/all';
    }


    /**
     * Returns all available machines for the industry
     * @return WP_Query WP_Query object of machines
     */
    public function machines()
    {
        if(isset($this->machines->posts) )
            return $this->machines;

        $families = pluck_where('term_id', array('type' => 'term'), $this->products());

        $this->machines = new \WP_Query(array(
            'post_type' => array(
                'cat_new_machine'
                ,'cat_new_power'
                ,'cat_new_attachment'
            )
            ,'posts_per_page' => -1
            ,'meta_key' => 'sort'
            ,'orderby' => 'meta_value_num'
            ,'order' => 'ASC'
            ,'tax_query' => array(
                'relation' => 'OR',
                array(
                    'taxonomy' => 'cat_new_machine_family',
                    'field'    => 'id',
                    'terms'    => $families
                ),
                array(
                    'taxonomy' => 'cat_new_power_family',
                    'field'    => 'id',
                    'terms'    => $families
                ),
                array(
                    'taxonomy' => 'cat_new_attachment_family',
                    'field'    => 'id',
                    'terms'    => $families
                ),
            )
        ));

        return $this->machines;
    }

    public function get_machines_link()
    {
        return trailingslashit( get_permalink($this->id ) ).'equipment/all';
    }


    /**
     * Returns the feuatured wquipment for this industry
     * @return WP_Query WP_Query object of featured equipment
     */
    public function featured()
    {
        if(isset($this->featured->posts) )
            return $this->featured;

        //$families = pluck_where('term_id', array('type' => 'term'), $this->products());
        //
        $ids = get_field('featured_equipment', $this->id, false);

        if(empty($ids))
            return array();

        $this->featured = new \WP_Query(array(
            'post_type' => array(
                'cat_new_machine'
                ,'cat_new_power'
                ,'cat_new_attachment'
            )
            ,'posts_per_page' => -1
            ,'meta_key' => 'sort'
            ,'orderby' => 'meta_value_num'
            ,'order' => 'ASC'
            ,'post__in' => $ids
            /*,'tax_query' => array(
                'relation' => 'OR',
                array(
                    'taxonomy' => 'cat_new_machine_family',
                    'field'    => 'id',
                    'terms'    => $families
                ),
                array(
                    'taxonomy' => 'cat_new_power_family',
                    'field'    => 'id',
                    'terms'    => $families
                ),
                array(
                    'taxonomy' => 'cat_new_attachment_family',
                    'field'    => 'id',
                    'terms'    => $families
                ),
            )
            ,'meta_query' => array(
                array(
                    'key'     => 'featured',
                    'value'   => '1',
                    'type'    => 'numeric'
                ),
            )*/
        ));

        return $this->featured;
    }


    /**
     * Returns the related news for the industry
     * @return WP_Query WP_Query object of news posts
     */
    public function news( $limit = -1 )
    {
        if(isset($this->news->posts) )
            return $this->news;

        $this->news = new \WP_Query(array(
            'post_type' => 'post'
            ,'posts_per_page' => $limit
            ,'tax_query' => array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    =>   sanitize_title( get_the_title($this->id) )
                )
            )
        ));

        return $this->news;
    }

    public function get_news_link()
    {
        $term = get_term_by('slug', sanitize_title( get_the_title($this->id) ), 'category' );
        return get_term_link($term);
    }

}