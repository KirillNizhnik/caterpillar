<?php
namespace Cat\Controllers\Admin;

/**
 * New_Abstract
 * makes any customizations to the admin views
 * for product post type
 *
 * @Package CAT New Feed/Admin
 * @category admin
 * @author WebpageFX
 */

if ( ! defined('ABSPATH') )
	exit;


class New_Abstract
{
    // Define per class
    public $post_type = null;

    // List of metaboxes to add
    public $metaboxes = array(
        'images'        => 'images'
        ,'videos'        => 'videos'
        ,'specs'        => 'specs'
        ,'featured'     => 'featured'
//        ,'rental-rates' => 'rental-rates'
    );

    // Rental environment
    public $rental_environment = '';

	/**
	 * Initializes variables and sets up WordPress hooks/actions.
	 *
	 * @return void
	 */

	public function __construct( $post_type=null )
	{/*{{{*/

        if (!is_null($post_type))
            $this->post_type = $post_type;

        // Check if rental is enabled
        $this->rental_environment = get_option('cat_rental_environment', '');

        if (!is_null($this->post_type))
        {
//            add_action( 'restrict_manage_posts', array($this, 'add_family_filters') );
            add_filter( 'manage_edit-' . $this->post_type . '_columns', array($this, 'add_column_head') );
            add_action( 'manage_' . $this->post_type . '_posts_custom_column', array($this, 'manage_column_content') );

            add_action( 'restrict_manage_posts', array($this, 'add_rentable_filter') );

            if ($this->rental_environment)
            {
                add_action( 'pre_get_posts', array($this, 'filter_rental_only') );
                add_action( 'admin_menu', array($this, 'add_rental_menu_item') );
            }

            add_action( 'add_meta_boxes', array($this, 'add_meta_boxes'), 10 );
            add_action( 'save_post_' . $this->post_type . '', array($this, 'save'), 10,3 );
        }
	}/*}}}*/

    /**
     * Add Family Filtering
     */
	public function add_family_filters()
	{/*{{{*/
		global $typenow;

        $rentable_only = (!empty($_GET[$this->post_type . '_rentable']) and $_GET[$this->post_type . '_rentable'] == "1");

		// must set this to the post type you want the filter(s) displayed on
		if( $typenow == $this->post_type  )
		{
			$terms = get_terms('family', array('parent' => 0));

			if(count($terms) > 0 and !$rentable_only): ?>

                <select name='<?php echo $this->post_type ?>_family' id='<?php echo $this->post_type ?>_family' class='postform'>";
				    <option value="">Show All Families</option>
				    <?php foreach ($terms as $term): ?>
                        <option value="<?php echo $term->slug; ?>" <?php
                            selected(
                                isset($_GET[$this->post_type . '_family'])
                                and $_GET[$this->post_type . '_family'] == $term->slug
                            );
                        ?>>
                        <?php echo $term->name .' (' . $term->count .')'; ?></option>
				    <?php endforeach; ?>
				</select>

			<?php endif;

			$terms = get_terms($this->post_type . '_rental_family', array('parent' => 0));

			if(count($terms) > 0 and $rentable_only): ?>

                <select name='<?php echo $this->post_type ?>_rental_family' id='<?php echo $this->post_type ?>_rental_family' class='postform'>";
				    <option value="">Show All Rental Families</option>
				    <?php foreach ($terms as $term): ?>
                        <option value="<?php echo $term->slug; ?>" <?php
                            selected(
                                isset($_GET[$this->post_type . '_rental_family'])
                                and $_GET[$this->post_type . '_rental_family'] == $term->slug
                            );
                        ?>>
                        <?php echo $term->name .' (' . $term->count .')'; ?></option>
				    <?php endforeach; ?>
				</select>

			<?php endif;
		}
	}/*}}}*/

    /**
     * When selected, show only rentable products
     */
    public function filter_rental_only($wp_query)
    {
        if (!is_admin())
            return;

        $query_type = $wp_query->get('post_type');

        $rentable_only = (!empty($_GET[$this->post_type . '_rentable']) and $_GET[$this->post_type . '_rentable'] == "1");

		// must set this to the post type you want the filter(s) displayed on
		if( $query_type == $this->post_type )
		{
            $tax_queries = $wp_query->tax_query->queries;
            $new_tax_queries = array();

            $rental_queried=false;
            foreach ($tax_queries as $tax_query)
            {
                $tax = empty($tax_query['taxonomy']) ? "" : $tax_query['taxonomy'];

                // Exclude new query if rental only
                if ($tax == $this->post_type . "_family" and $rentable_only)
                    continue;

                if ($tax == $this->post_type . "_rental_family")
                {
                    if (!$rentable_only)
                        continue;
                    else
                        $rental_queried = true;
                }

                $new_tax_queries[]= $tax_query;
            }

            // if not already querying rental tax, we add our custom tax query
            if ($rentable_only and !$rental_queried)
            {
                $terms = get_terms($this->post_type . '_rental_family', array('fields'=>'ids'));
                $new_tax_queries[]= array(
                    'taxonomy' => $this->post_type . '_rental_family'
                    ,'field'    => 'term_id'
                    ,'terms'    => $terms
                );
            }

            $wp_query->set('tax_query', $new_tax_queries);
            //die("<pre>".print_r($wp_query,true)."</pre>");

        }
    }

    /**
     * Menu item to jump straight to rental-only view
     */
    public function add_rental_menu_item()
    {
        add_submenu_page(
            'edit.php?post_type=' . $this->post_type
            ,'Rental Products'
            ,'Rental Products'
            ,'edit_posts'
            ,'edit.php?post_type=' . $this->post_type
                . '&' . $this->post_type . '_rentable=1'
        );
    }

    /**
     * Add Type Filtering
     */
	public function add_rentable_filter()
	{
		global $typenow;


		// must set this to the post type you want the filter(s) displayed on
		if( $typenow == $this->post_type  )
		{

			if(!empty($rental_environment)): ?>

                <select name='<?php echo $this->post_type ?>_rentable' id='<?php echo $this->post_type ?>_rentable' class='postform'>";
				    <option value="">Show All Products</option>
                    <option value="1" <?php
                        selected(
                            !empty($_GET[$this->post_type . '_rentable'])
                            and $_GET[$this->post_type . '_rentable'] == "1"
                        );
                    ?>>Show Only Rentable Products</option>
				    <?php foreach ($terms as $term): ?>
                        <option value="<?php echo $term->slug; ?>" <?php
                            selected(
                                isset($_GET[$this->post_type . '_rental_family'])
                                and $_GET[$this->post_type . '_rental_family'] == $term->slug
                            );
                        ?>>
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
        $rentable_only = (!empty($_GET[$this->post_type . '_rentable']) and $_GET[$this->post_type . '_rentable'] == "1");
        $new = array();

        foreach($columns as $key => $title) {

            // Add thumbnail before the title
            if($key == 'title') {
//                $new['thumbnail'] = '';
            }

            // Add category columns before the date column
            if ($key == 'date') {
//                $new['eid'] = 'CPC ID';
//                $new['featured'] = 'Featured';
            }

            // Exclude some columns based on type selection
            if (
                ($rentable_only and $key=='taxonomy-' . $this->post_type . '_family')
                or
                (!$rentable_only and $key=='taxonomy-' . $this->post_type . '_rental_family')
            )
                continue;

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
        $post_obj = get_post();
        $rental_terms = array();
        if (is_object($post_obj) and $this->rental_environment)
        {
            $rental_terms = get_the_terms($post_obj->ID, $post_obj->post_type . "_rental_family");
        }
        $rentable = !empty($rental_terms);

        $path = CAT()->plugin_path.'templates/admin/metaboxes/';

        // List of metaboxes to add
        $metaboxes = $this->metaboxes;
        if (!$rentable)
        {
            // remove rental metabox
            unset($metaboxes['rental-rates']);
        }


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
                ,$this->post_type . ''
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

        $defaults = array(
            'additional-images' => array(),
            'rental_rates' => array(),
            'specs' => array(),
            'featured_details' => array()
        );

        $values = array_merge($defaults, $_POST['post_meta']);


        // loop through each box and do some magic
        foreach($values as $key => $field)
        {
            add_post_meta($post_id, $key, $field, true) || update_post_meta($post_id, $key, $field);
        }

        $sort = get_post_meta( $post_id, 'sort', true );
        $sort = ($sort) ? $sort : 1;

        update_post_meta($post_id, 'sort', $sort);

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
