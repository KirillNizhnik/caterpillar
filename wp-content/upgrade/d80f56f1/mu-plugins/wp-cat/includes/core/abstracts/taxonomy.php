<?php
namespace Cat\Core\Abstracts;


class Taxonomy
{
    public  $name = '';

    /**
     * Initializes plugin variables and sets up WordPress hooks/actions.
     *
     * @return void
     */

    public function __construct( $name=null )
    {
        if (!is_null($name))
            $this->name = $name;

        add_action( $this->name.'_edit_form_fields', array($this, 'add_form_fields'), 10, 2 );
        add_action( 'edited_'.$this->name, array($this, 'save'), 10, 2 );
    }


    public function add_form_fields($term, $taxonomy)
    {
        $term_meta = get_cat_term_custom($term->term_id);
        include CAT()->plugin_path.'templates/admin/taxonomies/family.php';
    }



    public function save( $term_id, $term_taxonomy_id )
    {
        if ( isset( $_POST['term_meta'] ) )
        {
            foreach ( $_POST['term_meta'] as $k => $v ) {
                add_cat_term_meta( $term_id, $k, $v, true ) || update_cat_term_meta( $term_id, $k, $v);
            }
        }
    }
}
