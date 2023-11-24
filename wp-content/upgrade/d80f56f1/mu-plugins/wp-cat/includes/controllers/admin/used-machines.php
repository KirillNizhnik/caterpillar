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


class Used_Machines
{

	protected static $instance;


	/**
	 * Initializes variables and sets up WordPress hooks/actions.
	 *
	 * @return void
	 */

	protected function __construct( )
	{
		add_action( 'restrict_manage_posts',                      array($this, 'add_family_filter') );
        add_filter( 'manage_edit-cat_used_machine_columns',        array($this, 'add_column_head') );
        add_action( 'manage_cat_used_machine_posts_custom_column', array($this, 'manage_column_content') );


		add_action( 'add_meta_boxes', array($this, 'add_meta_boxes'), 10 );
        // add_action( 'admin_init', array($this, 'add_meta_boxes'), 1 );
        add_action( 'save_post_cat_used_machine', array($this, 'save'), 10,3 );
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
     * Add Family Filtering
     */
	public function add_family_filter()
	{
		global $typenow;

		// must set this to the post type you want the filter(s) displayed on
		if( $typenow == 'cat_used_machine' )
		{
			$terms = get_terms('cat_used_machine_family', array('parent' => 0));

			if(count($terms) > 0): ?>

				<select name='cat_used_machine_family' id='cat_used_machine_family' class='postform'>";
				    <option value="">Show All Families</option>
				    <?php foreach ($terms as $term): ?>
					<option value="<?php echo $term->slug; ?>"
                        <?php
                            echo ( isset($_GET['cat_used_machine_family'])
                                   && $_GET['cat_used_machine_family'] == $term->slug
                                 )
                                 ? ' selected="selected"'
                                 : ''; ?>>
                        <?php echo $term->name .' (' . $term->count .')'; ?></option>
				    <?php endforeach; ?>
				</select>

			<?php endif;
		}
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

            // Add thumbnail before the title
            if($key == 'title') {
                $new['thumbnail'] = '';
            }

            // Add category columns before the date column
            if ($key == 'date') {
                $new['eid'] = 'DSF ID';
                $new['featured'] = 'Featured';
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
            case 'thumbnail':
                //$images  = get_post_meta( $post->ID, 'images', true );
                //$image = array_shift($images);
                //echo '<img src="'.$image->src.'?wid=50&hei=50" alt="" />';
                //echo get_cnf_preview( $post->ID, array(50,50) );
                break;

            case 'eid':
                echo get_post_meta( $post->ID, 'equipment_id',true );
                break;

            case 'featured':
                $is_featured = ($v = get_post_meta( $post->ID, 'featured', true)) ? $v : 0;
                echo '<input class="post_meta_featured_quick" name="post_meta[featured]" type="checkbox" value="1" data-post_id="'.$post->ID.'" '.checked($is_featured, 1, false).'>';
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
        	'details'
            ,'images'
            ,'specs'
            ,'features'
            ,'featured'
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
                ,'cat_used_machine'
                ,$data['context']
                ,$data['priority']
                ,$data
            );
        }
    }


    /**
     * Saves the custom post data
     *
     * @param $post_id int, The current post ID
     */

    public function save($post_id, $post, $update )
    {
        // Make sure we have some of our custom meta boxes
        if ( ! isset($_POST['post_meta']) )
            return;

        // make sure user has permissions
        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) )
                return;
        }


        // loop through each box and do some magic
        foreach($_POST['post_meta'] as $key => $field)
        {
            add_post_meta($post_id, $key, $field, true) || update_post_meta($post_id, $key, $field);
        }

    } // public method save



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

}
Used_Machines::instance();
