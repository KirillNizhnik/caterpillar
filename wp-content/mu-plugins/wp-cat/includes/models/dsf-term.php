<?php
namespace Cat\Models;


class DSF_Term
{
    public $term_id;
    public $term_taxonomy_id;
    public $name;
    public $taxonomy;
    public $parent = 0;


    public function save()
    {
        $skip = array('term_id','term_taxonomy_id','name', 'taxonomy','parent');
        $properties = get_object_vars($this);

        foreach($properties as $property => $value)
        {
            if(in_array($property, $skip))
                continue;

            add_cat_term_meta( $this->term_taxonomy_id, $property, $value, true ) || update_cat_term_meta( $this->term_taxonomy_id, $property, $value );
        }

    }


    public static function find_or_new($name, $options=array())
    {
        $options = array_merge($options, array(
            'description' => ''
        ));

        $calledClassName = get_called_class();

        $term = new $calledClassName();
        $term->name = (string) $name;

        // check if this name is already a term
        if(! $term_obj = term_exists($term->name, $term->taxonomy))
        {
            // insert the new term
            $term_obj = wp_insert_term($term->name, $term->taxonomy, $options);

            $term->term_id          = $term_obj['term_id'];
            $term->term_taxonomy_id = $term_obj['term_taxonomy_id'];

            add_cat_term_meta( $term->term_taxonomy_id, 'include_in_import', 1, true );
        }
        else
        {
            $term->term_id          = $term_obj['term_id'];
            $term->term_taxonomy_id = $term_obj['term_taxonomy_id'];
        }

        return $term;
    }
}
