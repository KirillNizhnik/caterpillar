<?php

namespace Cat\Models;

class Family extends View
{
    public $id;
    public $wp_term;
    public $short_description;
    public $long_description;
    public $before_content;
    public $after_content;
    public $links;
    public $features;
    public $videos;
    public $thumbnail;
    public $header;
    public $url;

    protected $join_key = 'cat_term_id';

    public function __construct($family)
    {
        $this->id = $family->term_id;
        $this->wp_term = $family;

        $properties = get_object_vars($family);

        foreach ($properties as $key => $value) {
            $this->{$key} = $value;
        }

        $custom = $this->setup_custom_data(get_cat_term_custom($this->term_id));
        if (strpos($family->taxonomy, 'family') === 0) {
            $this->cpc_id = isset($custom['family_id']) ? $custom['family_id'] : false;
            $this->class_id = isset($custom['class']) ? $custom['class'] : false;
            $this->class = isset($custom['class']) ? CAT()->get_available_classes($custom['class']) : false;
        } else {
            if ($family->parent > 0) {
                $class = get_term_by('term_id', $this->parent, $this->taxonomy);
                // var_dump( $class );
                $this->class = $class->name;
            }
        }


        $this->short_description = isset($custom['short_description'])
            ? wpautop($custom['short_description'])
            : '';

        $this->long_description = isset($custom['long_description'])
            ? wpautop($custom['long_description'])
            : '';

        $this->before_content = isset($custom['content_before_products'])
            ? wpautop($custom['content_before_products'])
            : '';
        $this->after_content = isset($custom['content_after_products'])
            ? wpautop($custom['content_after_products'])
            : '';

        if (has_shortcode($this->before_content, 'accordion')) {
            $this->before_content = apply_filters('the_content', $this->before_content);
        }

        if (has_shortcode($this->after_content, 'accordion')) {
            $this->after_content = apply_filters('the_content', $this->after_content);
        }

        $this->images = isset($custom['images']) ? $custom['images'] : '';
        $this->links = isset($custom['images']) ? $custom['links'] : '';
        $this->features = isset($custom['images']) ? $custom['features'] : '';
        $this->videos = isset($custom['images']) ? $custom['videos'] : '';
        $this->thumbnail = $this->thumbnail();
        $this->header = $this->header();

        $this->url = get_term_link($this->wp_term);

        if (!empty($custom['redirect'])) {
            $this->url = trim($custom['redirect']);
        }
    }

    /**
     * Function for returning the family thumbnail image
     *
     * @param  [int]    the id of the family term id
     * @return [array]  array of the src and title
     */

    public function thumbnail($family_id = '', $size = 'thumbnail')
    {
        if (empty($family_id))
            $family_id = $this->term_id;

        $img = new \stdClass();
        //echo '<pre>'; var_dump(get_cat_term_meta($family_id, 'thumbnail', true)); echo '</pre>';

        // check custom thumbnail first
        if ($thumbnail = get_cat_term_meta($family_id, 'thumbnail', true)) {
            $thumbnail = wp_get_attachment_image_src($thumbnail, $size);
            $img->src = $thumbnail[0];

            return $img;
        }

        // fallback to cat feed
        $images = get_cat_term_meta($family_id, 'images', true);

        if (!empty($images)) {
            $img = reset($images);
            return $img;
        } else {
            // if( $this->parent > 0 )
            // {
            //     $parent = new self(get_term_by('term_id', $this->parent, $this->taxonomy));
            //     $thumbnail = $parent->thumbnail($this->parent, $size);

            //     return $thumbnail;
            // }

            if (is_file(get_stylesheet_directory() . '/img/coming-soon-img.jpg'))
                $img->src = get_stylesheet_directory_uri() . '/assets/img/coming-soon-img.jpg';
            else
                $img->src = CAT()->plugin_url . 'assets/images/default.jpg';

            return $img;
        }


    }


    /**
     * Function for returning the family header image
     *
     * @param  [int]    the id of the family term id
     * @return [array]  array of the src and title
     */

    function header($family_id = '', $size = 'full')
    {
        if (empty($family_id))
            $family_id = $this->term_id;

        // check custom header first
        if ($image = get_cat_term_meta($family_id, 'header', true)) {
            $image = wp_get_attachment_image_src($image, $size);

            $img = new \stdClass();
            $img->src = $image[0];

            return $img;
        }

        // fallback to cat feed
        $images = get_cat_term_meta($family_id, 'images', true);

        if (!empty($images)) {
            $hero = array_filter($images, function ($image) {
                return $image->type === 'marketing-hero';
            });

            $hero = array_shift($hero);

            if (isset($hero) and !empty($hero)) {
                return $hero;
            }
        }

        // Fallback to parent
        if ($this->parent > 0) {
            $parent = new self(get_term_by('term_id', $this->parent, $this->taxonomy));
            $header = $parent->header($this->parent, $size);
            return $header;
        }

        return false;
    }


    /**
     * Returns a WP Query for the families industries
     * @return [type] [description]
     */
    public function industries()
    {
        if (empty($this->join))
            $this->get_join_table();

        $industry_ids = array_unique(pluck('industry_id', $this->join));
        $industries = new \WP_Query(array(
            'post_type' => 'cat_industry'
        , 'post__in' => $industry_ids
        , 'post_status' => 'publish'
        ));

        return $industries;
    }


}
