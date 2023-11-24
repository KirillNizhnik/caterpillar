<?php
namespace Cat\Core\Abstracts;


class Post_type
{
    public $name = '';

    protected $args = array(
        'create_post_type'   => true

        ,'post_slug'         => ''
        ,'post_title'        => ''
        ,'post_title_plural' => ''

        ,'create_taxonomy'   => true

        ,'tax_slug'          => ''
        ,'tax_title'         => ''
        ,'tax_post_type'     => ''
    );

    /**
     * Register a new feed post type and it's taxonomy
     * @return void
     */

    public function __construct($name, $args=array())
    {/*{{{*/
        $this->name = $name;

        $defaults = $this->args;
        $title = ucwords(str_replace('_', ' ', $name));

        $defaults['post_slug'] = $name;
        $defaults['post_title'] = $title;
        $defaults['post_title_plural'] = $title."s";
        $defaults['tax_post_type'] = $name;

        $this->args = wp_parse_args($args, $defaults);

        if (!empty($this->args['create_post_type']))
        {
            $this->register_post_type();
        }

        if (!empty($this->args['create_taxonomy']))
        {
            $this->register_taxonomy();
        }
    }/*}}}*/

    protected function register_post_type()
    {/*{{{*/
        $name = $this->name;
        $slug = $this->args['post_slug'];
        $title = $this->args['post_title'];
        $title_plural = $this->args['post_title_plural'];
         
        $args = apply_filters(
            'cat_new_'.$name.'_config'
            ,array(
                'public'         => true
                ,'map_meta_cap'  => true
                ,'has_archive'   => true
                ,'rewrite'       => array(
                   'slug'        => $slug
                   ,'with_front' => false
                )
                ,'supports'      => array('title', 'editor', 'excerpt', 'thumbnail')
                ,'menu_icon'     => 'dashicons-admin-generic'
                ,'menu_position' => 35
                ,'labels'        => apply_filters(
                    'cat_new_equipment_labels'
                    ,array(
                        'name'                => _x( 'New '.$title_plural, 'post type general name', CAT()->domain )
                        ,'singular_name'      => _x( 'New '.$title, 'post type singular name', CAT()->domain )
                        ,'menu_name'          => _x( 'CAT '.$title_plural, 'admin menu', CAT()->domain )
                        ,'name_admin_bar'     => _x( 'New '.$title.'', 'add new on admin bar', CAT()->domain )
                        ,'add_new'            => _x( 'Add New', 'new '.$name, CAT()->domain )
                        ,'add_new_item'       => __( 'Add '.$title, CAT()->domain )
                        ,'new_item'           => __( 'New '.$title, CAT()->domain )
                        ,'edit_item'          => __( 'Edit '.$title, CAT()->domain )
                        ,'view_item'          => __( 'View '.$title, CAT()->domain )
                        ,'all_items'          => __( 'All '.$title_plural, CAT()->domain )
                        ,'search_items'       => __( 'Search new '.$name.'s', CAT()->domain )
                        ,'parent_item_colon'  => __( 'Parent '.$name.':', CAT()->domain )
                        ,'not_found'          => __( 'No '.$name.'s found.', CAT()->domain )
                        ,'not_found_in_trash' => __( 'No '.$name.'s found in trash.', CAT()->domain )
                    )
                )
            )
            ,$name
        );

        register_post_type( 'cat_new_'.$name, $args );
    }/*}}}*/

    protected function register_taxonomy()
    {
        $name = $this->name;
        $slug = $this->args['tax_slug'];
        $title = $this->args['tax_title'];
        $post_type = $this->args['tax_post_type'];

        if (empty($slug))
        {
            $slug = empty($this->args['post_slug'])
                ? $this->$name . "s"
                : $this->args['post_slug'];
        }

        if (empty($title))
        {
            $title = empty($this->args['post_title'])
                ? uc_words($this->$name)
                : $this->args['post_title'];
        }
        
        $args = apply_filters(
            'cat_new_'.$name.'_tax_config'
            ,array(
                'rewrite' => array(
                    'slug' => $slug
                    ,'with_front' => false
                )
                ,'hierarchical' => true
                ,'show_admin_column' => true
                ,'labels' => apply_filters(
                    'cat_new_'.$name.'_tax_labels'
                    ,array(
                        'name'              => _x( $title.' Families', 'taxonomy general name' )
                        ,'singular_name'     => _x( $title.' Family', 'taxonomy singular name' )
                        ,'search_items'      => __( 'Search Families' )
                        ,'all_items'         => __( 'All Families' )
                        ,'parent_item'       => __( 'Parent Family' )
                        ,'parent_item_colon' => __( 'Parent Family:' )
                        ,'edit_item'         => __( 'Edit Family' )
                        ,'update_item'       => __( 'Update Family' )
                        ,'add_new_item'      => __( 'Add New Family' )
                        ,'new_item_name'     => __( 'New Family Name' )
                        ,'menu_name'         => __( $title.' Families' )
                    )
                    ,$name
                )
            )
        );

        register_taxonomy(
             'cat_new_'.$name.'_family'
            ,apply_filters( 'cat_new_'.$name.'_tax_post_types', array('cat_new_'.$post_type) )
            ,$args
        );
    }

}
