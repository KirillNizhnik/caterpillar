<?php
namespace Cat\Models;

class Product extends View
{
    public $id;
    public $post;
    public $cpc_class;
    public $equipment_id;
    public $images;
    public $links;
    public $features;
    public $videos;
    public $family;
    public $specs = array();

    protected $_rental_url;
    protected $_is_rentable;


    /**
     * Initializes plugin variables and sets up WordPress hooks/actions.
     *
     * @return void
     */

    public function __construct( $product )
    {
        $this->id        = $product->ID;
        $this->post      = $product;
        $this->post_type = $product->post_type;
        $this->title     = $product->post_title;

        $this->family_prefix = "";

        $terms = get_the_terms( $this->id, $this->post_type.'_family' );
        global $wp_query;
        if (empty($terms) or $wp_query->is_cat_rental)
            $terms = get_the_terms( $this->id, $this->post_type.'_rental_family' );

        if( is_array($terms) ) {
            $terms = empty($terms) ? array() : array_values($terms);
        }
        else {
            $terms = array();
        }


        // if( is_array($terms) ) {
        //     $terms = array_values($terms);

        //     // make sure top level parent is first
        //     usort($terms, function($a, $b) {
        //         return $a->parent == $b->parent ? 0 : ( $a->parent > $b->parent ) ? 1 : -1;
        //     });
        // } else {
        //     $terms = array();
        // }

        if($this->post_type === 'cat_used_machine')
        {
            if(! empty($terms)) {

                if( isset($terms[1]) ) {
                    $this->class = $terms[1];
                    $this->family = $terms[0];
                } else {
                    $this->class = $terms[0];
                    $this->family = $terms[0];
                }


                if( isset($terms[2]) )
                    $this->family_type = $terms[2];

                $the_terms = get_the_terms( $this->id, 'cat_used_machine_manufacturer' );
                if (is_array($the_terms)) {
                    $terms = array_values($the_terms);
                    $this->manufacturer = $terms[0];
                }
            }
        }
        else
        {
            $this->family = $terms[0];

            if( isset($terms[1]) )
                $this->family_type = $terms[1];


            $classes = array_flip(CAT()->get_class_post_type_relation());
            $this->cpc_class = empty($classes[$this->post_type]) ? false : $classes[$this->post_type];
        }

        $custom = $this->setup_custom_data(get_post_custom($this->id));

        foreach($custom as $key => $value)
        {
            $this->{$key} = $value;
        }

        if (empty($this->features))
        {
            $features_data = get_post_meta($this->id, 'featured_details', true );
            $temp = array();
            if (is_array($features_data))
            {
                foreach ($features_data as $name => $value)
                {
                    $feature                 = new \stdClass();
                    $feature->name           = $name;
                    $feature->content  = $value;

                    $temp[] = $feature;
                }
            }
            $this->features = $temp;
        }

        $this->determine_empty_images();
        $this->header    = $this->header();
        $this->thumbnail = $this->thumbnail();

        if( $this->post_type !== 'cat_used_machine' )
            $this->specs = $this->specs(false, true);
    }


    /**
     * Function for returning the family header image
     *
     * @return std Class
     */

    public function header()
    {
        $family = CAT()->family($this->family);

        if($family instanceof \Cat\Models\Family)
            return $family->header();
    }



    /**
     * Function for returning the images content
     *
     * @return [array]  array of the image sources
     */

    public function thumbnail()
    {
        if( ! empty($this->images) )
        {
            return reset($this->images);
        }
        else
        {
            $images = get_cat_term_meta($this->family->term_id, 'images', true);
            $image = is_array($images) ? array_shift($images) : '';

            if( ! empty($image) )
                return $image;
        }

        return false;
    }

    /**
     * Get rental URL
     *  - Product if rentable
     *  - Else family if rentable
     *  - Else configured rental url
     */
    public function rental_url()
    {
        if (is_null($this->_rental_url))
        {
            $this->_rental_url = '#';

            if ($this->is_rentable())
            {
                global $wp_query;
                $tmp = $wp_query->is_cat_rental;
                $wp_query->is_cat_rental = true;

                $this->_rental_url = get_permalink($this->id);

                $wp_query->is_cat_rental = $tmp;
            }
            else
            {
                // Loop through families
                $families = get_the_terms($this->id, $this->post_type . "_family");
                foreach ($families as $family)
                {
                    if (empty($family->parent))
                    {
                        $slug = $family->slug;
                        $rental_tax = $this->post_type . "_rental_family";
                        $rental_family = get_term_by( 'slug', $slug, $rental_tax );
                        if (!empty($rental_family))
                        {
                            $this->_rental_url = get_term_link($rental_family, $rental_tax);
                        }
                    }
                }

                /*
                Uncomment to allow default rental link
                if (is_null($this->_rental_url))
                    $this->_rental_url = site_url(get_option('cat_rent_url', '#'));
                 */
            }
        }

        return $this->_rental_url;
    }

    /**
     * Is product rentable?
     */
    public function is_rentable()
    {
        if (is_null($this->_is_rentable))
        {
            $rental_terms = get_the_terms($this->id, $this->post_type . "_rental_family");
            $this->_is_rentable = !empty($rental_terms);
        }

        return $this->_is_rentable;
    }

    /**
     * Function for returning the equipment specs
     *
     * @param  [int|bool] limit the number of returned specs
     * @param  [bool]     group the specs
     * @return [array]    array of the gallery items
     */

    public function specs($limit=false, $group=false, $priority=true)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cat_product_specs';
        $sql = "SELECT * FROM $table_name WHERE product_id='{$this->equipment_id}' ORDER BY";
        $sql .= ($priority) ? ' priority'  : ' group_sort_custom, group_sort, sort_custom, sort, priority';

        if($limit) {
            $sql .= " LIMIT $limit";
        }

        $specs = $wpdb->get_results( $sql );

        if($group)
        {
            $groups = array();
            foreach ($specs as $spec)
            {
                if(isset($groups[$spec->group_name]))
                {
                    $groups[$spec->group_name][] = $spec;
                }
                else
                {
                    $groups[$spec->group_name] = array($spec);
                }
            }

            $specs = $groups;
        }

        if( empty($specs) )
        {
            $specs = get_post_meta($this->id, 'specs', true );
            $temp  = array();

            if( empty($specs) )
                return false;

            foreach($specs as $name => $value)
            {
                $spec                 = new \stdClass();
                $spec->name           = $name;
                $spec->value_english  = $value;
                $spec->unit_english   = '';
                $spec->value_metric   = $value;
                $spec->unit_metric    = '';

                $temp[] = $spec;
            }

            $specs = $temp;

            if($limit)
            {
                $temp  = array();
                $limit = ($limit > count($specs) ) ? count($specs): $limit;
                $i     = 0;

                foreach($specs as $spec)
                {
                    if($i == $limit)
                        break;

                    $temp[] = $spec;
                    $i++;
                }

                $specs = $temp;
            }

            if( empty($specs) )
                return false;


            if( $group )
            {
                $groups = array('General' => array());

                foreach ($specs as $spec) {
                    $groups['General'][] = $spec;
                }

                $specs = $groups;
            }

        }

        return $specs;
    }

    /**
     * return a wp query of related equipment
     * @param  array  $args query args
     * @return WP_Query
     */
    public function related_equipment( $args=array() )
    {
        $defaults = array(
            'posts_per_page' => 4
        );
        $args = array_merge($defaults, $args);


        $families = array();

        if(!empty($this->family) )
            $families[] = $this->family->slug;

        if(!empty($this->family_type))
            $families[] = $this->family_type->slug;


        $args['tax_query'] =  array(
            array(
                'taxonomy' => 'cat_used_family'
                ,'field'    => 'slug'
                ,'terms'    => $families
                ,'include_children' => true
                ,'operator'         => 'IN'
            ),
        );

        return new WP_Query($args);
    }

    public function attachments()
    {
        global $wpdb;

        if( isset($this->related_attachments) )
            return  $this->related_attachments;

        if ( isset( $this->related['product']['related'] ) )
            $related_attachments = $wpdb->get_col("SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key='equipment_id' AND meta_value IN (".implode(',',$this->related['product']['related']).")");

        // Fixes bug where posts were returned if $related_attachments
        // was an empty array: https://core.trac.wordpress.org/ticket/28099
        if( empty( $related_attachments ) )
            $related_attachments = array(0);

        $this->related_attachments = new \WP_Query(array(
            'post_type'      => 'cat_new_attachment'
            ,'post_status'    => 'publish'
            ,'posts_per_page' => isset($this->related['product']['related']) ? count($this->related['product']['related']) : 0
            ,'post__in' => $related_attachments
        ));

        return $this->related_attachments;
    }


    private function determine_empty_images()
    {
        if( empty($this->images) )
        {
            if (is_file(get_stylesheet_directory().'/images/coming-soon.jpg'))
                $this->images = array(get_stylesheet_directory_uri().'/images/coming-soon.jpg');
            else
                $this->images = array(CAT()->plugin_url.'assets/images/default.jpg');
        }
    }
}
