<?php
namespace Cat\Controllers\Admin;

/**
 * CNF_Admin_Product
 * makes any customizations to the admin views
 * for product post type
 *
 * @Package CAT New Feed/Admin
 * @category admin
 * @author WebpageFX
 */

if ( ! defined('ABSPATH') )
	exit;


class Applications
{

	protected static $instance;


	/**
	 * Initializes variables and sets up WordPress hooks/actions.
	 *
	 * @return void
	 */

	protected function __construct()
	{
        add_filter( 'manage_edit-cat_application_columns',        array($this, 'add_column_head') );
        add_action( 'manage_cat_application_posts_custom_column', array($this, 'manage_column_content') );
		add_action( 'add_meta_boxes', array($this, 'add_meta_boxes'), 10 );
        add_action( 'admin_init', array($this, 'add_meta_boxes'), 1 );

        add_action( 'save_post_cat_application', array($this, 'save'), 10, 3 );
	}



	/* Static Singleton Factory Method */
	public static function instance()
	{
		if (!isset(self::$instance)) {
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}




    /**
     * Column Headers
     * @param [type] $columns [description]
     * @return  array array of column headers
     */
    public function add_column_head($columns)
    {
        $new = array();

        foreach($columns as $key => $title) {

            // Add category columns before the date column
            if ($key == 'date') {
                $new['industry'] = 'Industry';
            }

            $new[$key] = $title;
        }


        return $new;
    }


    /**
     * Column Content
     * @param  [type] $name [description]
     * @return null
     */
    public function manage_column_content($name)
    {
        global $post;

        switch ($name) {
            case 'industry':
                $application = new \Cat\Models\Application($post->ID);
                $industry = $application->industry();
                echo $industry->post_title;
                break;
        }
    }






    /**
     * Registers our custom meta boxes
     */

    public function add_meta_boxes($post)
    {
        $path = CAT()->plugin_path.'templates/admin/metaboxes/';

        // List of metaboxes to add
        $metaboxes = array(
        	'industry-families'
        );

        // loop through each metabox file
        foreach ($metaboxes as $box)
        {
            $include = $path.$box.'.php';

            // Grabs Comment Block at top for our form
            // returns an assoc array
            // http://phpdoc.wordpress.org/trunk/WordPress/_wp-includes---functions.php.html#functionget_file_data
            $data = get_file_data($include, array(
                 'title'     => 'Title'
                ,'post type' => 'Post Type'
                ,'context'   => 'Context'
                ,'priority'  => 'Priority'
            ));

            $data['form'] = $include;

            // Add Each Metabox
            add_meta_box(
                 strtolower(str_replace(' ', '_', $data['title']))
                ,$data['title']
                ,array( $this, 'render' )
                ,'cat_application'
                ,$data['context']
                ,$data['priority']
                ,$data
            );
        } // foreach

    }

    /**
     * Renders the html of each metabox
     *
     * @param $post Object, The current post Object
     * @param $metabox Array, The Current metabox with any callback args
     */

    public function render($post, $metabox)
    {
        //include the display of our form.
        include_once $metabox['args']['form'];
    }



    public function save( $post_id, $post, $update )
    {
        global $wpdb;

        if( defined('DOING_AUTOSAVE') AND DOING_AUTOSAVE )
            return;

        // we always completely replace relations
        // so delete all previous relations
        $wpdb->delete( $wpdb->prefix.'cat_term_industries', array( 'application_id' => $post_id ), array( '%d' ) );

        if( isset($_POST['related_families'])
            AND ! empty($_POST['related_families'])
        ){
            $industry_id = intval($_POST['industry_id']);

            // insert the new relations
            foreach($_POST['related_families'] as $item)
            {
                $data      = explode('|', $item);
                $object_id = intval($data[0]);
                $type      = $data[1];

                $wpdb->insert(
                    $wpdb->prefix.'cat_term_industries',
                    array(
                        'industry_id'      => $industry_id,
                        'object_id'        => $object_id,
                        'application_id'   => $post_id,
                        'object_type'      => $type,
                    ),
                    array(
                        '%d',
                        '%d',
                        '%d',
                        '%s'
                    )
                );
            }

        }
    }

}

Applications::instance();
