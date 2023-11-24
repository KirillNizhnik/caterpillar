<?php

/**
 * CM_Posttype
 * base logic for post types, only used to extend common logic
 */

if ( ! defined('ABSPATH') )
    exit;


abstract class CM_Abstract_Post_Type
{
    /**
     * Post type for this admin
     * @var string
     */
    public $post_type;


    public function __construct( )
    {
        add_action( 'add_meta_boxes', array($this, 'add_meta_boxes'), 10 );
        add_action( 'save_post_'.$this->post_type, array($this, 'save'), 10, 2 );
        add_action( 'edit_form_advanced', array($this, 'add_nonce'));

        do_action('cm_post_type_'.$this->post_type.'_actions' );
    }


    /**
     * Return an arrow of metaboxes to add to the screen
     * @return [type] [description]
     */
    abstract protected function metaboxes();

    /**
     * Registers our custom meta boxes
     */

    public function add_meta_boxes($post)
    {
        $metaboxes = $this->metaboxes();

        // loop through each metabox file
        foreach( $metaboxes as $box )
        {
            // Grabs Comment Block at top for our form
            // returns an assoc array
            // http://phpdoc.wordpress.org/trunk/WordPress/_wp-includes---functions.php.html#functionget_file_data
            $data = get_file_data($box, array(
                 'title'     => 'Title'
                ,'post type' => 'Post Type'
                ,'context'   => 'Context'
                ,'priority'  => 'Priority'
            ));

            $data['form'] = $box;

            // Add Each Metabox
            add_meta_box(
                 strtolower(str_replace(' ', '_', $data['title']))
                ,$data['title']
                ,array( $this, 'render' )
                ,$data['post type']
                ,$data['context']
                ,$data['priority']
                ,$data
            );
        }
    }

    public function add_nonce($post)
    {
        if( $post->post_type == $this->post_type ) {
            wp_nonce_field( 'cm_meta', '_cm_wpnonce' );
        }
    }


    /**
     * Renders the html of each metabox
     *
     * @param $post Object, The current post Object
     * @param $metabox Array, The Current metabox with any callback args
     */

    public function render($post, $metabox)
    {
        include_once $metabox['args']['form'];
    }



    /**
     * Saves the custom post data
     *
     * @param $post_id int, The current post ID
     */

    public function save($post_id, $post )
    {
        if( ! isset($_POST['post_meta']) )
            return;

        // make sure user has permissions
        if( ! current_user_can( 'edit_page', $post_id ) )
            return;
        if( ! current_user_can( 'edit_post', $post_id ) )
            return;

        if( wp_verify_nonce($_POST['_cm_wpnonce'], 'cm_meta') !== 1 )
            return;


        // loop through each box and do some magic
        foreach($_POST['post_meta'] as $key => $field)
        {
            if(is_array($field))
            {
                array_walk_recursive($field, function(&$val, $key) {
                    $val = sanitize_text_field($val);
                });
                $data = $field;
            }
            else
            {
                $data = sanitize_text_field($field);
            }

            update_post_meta($post_id, $key, $data);
        }

        do_action('cm_meta_'.$this->post_type.'_saved', $post_id, $post);
    }

}
