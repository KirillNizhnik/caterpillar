<?php

/**
 * CM_Admin_Rep
 * makes any customizations to the admin views
 * for Representative post type
 *
 * @category admin
 * @author WebpageFX
 */

if ( ! defined('ABSPATH') )
    exit;


class CM_Admin_Rep extends CM_Abstract_Post_Type
{

    public $post_type = 'rep';

    /**
     * Initializes variables and sets up WordPress hooks/actions.
     *
     * @return void
     */

    public function __construct( )
    {
        add_filter('manage_rep_posts_columns', array($this, 'add_column_head'));
        add_action('manage_rep_posts_custom_column', array($this, 'manage_column_content'), 10, 2);
        add_action('wp_ajax_update_hotlist_meta', array($this, 'save_hotlist_status_ajax') );

        parent::__construct();
    }

    /**
     * return an array of box names to include
     * @return [type] [description]
     */
    protected function metaboxes()
    {
        $path = __DIR__.'/metaboxes/';

        return apply_filters( 'cm_'.$this->post_type.'_metaboxes', array(
            $path.'contact.php'
            ,$path.'service-area.php'
        ));
    }



    public function add_column_head($columns)
    {
        $new = array();

        foreach($columns as $key => $title)
        {
            if ($key == 'date')
            {
                $new['hotlist'] = 'Show on Hotlist';
            }
            $new[$key] = $title;
        }

        return $new;
    }



    public function manage_column_content($name, $post_id)
    {
        switch ($name)
        {
            case 'hotlist':
                $is_hotlist = ($v = get_post_meta( $post_id, 'hotlist', true)) ? $v : 0;
                echo '<input class="post_meta_hotlist_quick" name="post_meta[hotlist]" type="checkbox" value="1" data-post_id="'.$post_id.'" '.checked($is_hotlist, 1, false).'>';
                break;
        }
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

        // remove old post codes
        delete_post_meta( $post_id, 'zipcode' );

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

            if( 'zipcodes' === $key ) {

                $data = explode(',', $data);
                foreach( $data as $zip) {
                    add_post_meta( $post_id, 'zipcode', trim($zip), false );
                }
                // exit early from loop
                continue;
            }

            update_post_meta($post_id, $key, $data);
        }

        do_action('cm_meta_'.$this->post_type.'_saved', $post_id, $post);
    }


    public function save_hotlist_status_ajax()
    {
        $post_id = $_POST['post_id'];
        $checked = $_POST['checked'];

        $result = update_post_meta( $post_id, 'hotlist', $checked);

        die(json_encode(array('post_id' => $post_id, 'checked' => $checked, 'result' => $result )));
    }

}
return new CM_Admin_Rep();
