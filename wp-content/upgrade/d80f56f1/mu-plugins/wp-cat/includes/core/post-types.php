<?php
namespace Cat\Core;
use Cat\Core\Abstracts\Post_type;


class Post_types
{
    /**
     * Plugin instance.
     *
     * @see instance()
     * @var object
     */
    protected static $instance = NULL;

    /**
     * Default slugs for posttypes
     * @var array
     */
    public $default_slugs = array(
        'cat_new_machine'            => 'new-equipment/machines'
        ,'cat_new_power'             => 'new-equipment/power-systems'
        ,'cat_new_attachment'        => 'new-equipment/attachments'
        ,'cat_new_allied'            => 'new-equipment/site-support'
        ,'cat_new_home_power'        => 'new-equipment/home-outdoor-power'

        ,'cat_new_machine_rental'    => 'rental-equipment/machines'
        ,'cat_new_power_rental'      => 'rental-equipment/power-systems'
        ,'cat_new_attachment_rental' => 'rental-equipment/attachments'
        ,'cat_new_allied_rental'     => 'rental-equipment/site-support'
        ,'cat_new_allied_pwr_rental'     => 'rental-equipment/site-support-power'
        ,'cat_new_home_power_rental' => 'rental-equipment/home-outdoor-power'

        ,'cat_used'                  => 'used-equipment'
    );

    /**
     * Default tags for posttypes
     * @var array
     */
    public $default_tags = array(
        'cat_new_machine'     => '%cat_machine_family%'
        ,'cat_new_power'      => '%cat_power_family%'
        ,'cat_new_attachment' => '%cat_attachment_family%'
        ,'cat_new_allied'     => '%cat_allied_family%'
        ,'cat_new_home_power' => '%cat_home_power_family%'
        ,'cat_used_machine'   => '%cat_used_family%'
        ,'cat_application'    => '%cat_industry%'
    );


    /**
     * Static Singleton Factory Method
     * @return self returns a single instance of our class
     */

    public static function instance()
    {
        if (!isset(self::$instance)) {
            $class_name = __CLASS__;
            self::$instance = new $class_name;
        }
        return self::$instance;
    }


    /**
     * Initializes plugin variables and sets up WordPress hooks/actions.
     *
     * @return void
     */

    protected function __construct()
    {
        add_action( 'init', array($this, 'set_query_variables'), 5 );
        add_action( 'init', array($this, 'register_content_types'), 5 );
        
        add_action( 'pre_get_posts' , array( $this, 'cat_pre_get_posts' ) );

        add_filter( 'post_type_link', array( $this, 'filter_post_link' ), 10, 2 );
        add_action( 'wp_ajax_cat_new_update_featured_status', array($this, 'save_featured_status_ajax') );
    }


    /**
     * filtered default slugs
     */
    public function get_default_slugs()
    {
        return apply_filters(
            'cat_default_slugs'
            ,$this->default_slugs
        );
    }

    /**
     * specific default slug
     */
    public function get_default_slug($type)
    {
        $slugs = $this->get_default_slugs();
        return empty($slugs[$type]) ? false : $slugs[$type];
    }

    /**
     * specific slug, check options then default
     */
    public function get_slug($type)
    {
        $slug = get_option($type.'_slug');
        if (empty($slug))
            $slug = $this->get_default_slug($type);
        return $slug;
    }

    /**
     * filtered tags
     */
    public function get_default_tags()
    {
        return apply_filters(
            'cat_default_tags'
            ,$this->default_tags
        );
    }

    /**
     * specific tag
     */
    public function get_default_tag($type)
    {
        $tags = $this->get_default_tags();
        return empty($tags[$type]) ? false : $tags[$type];
    }

    /**
     * Adds our custom query variables publicly
     * so they are passed to query object. Makes
     * custom rewrite variables work
     *
     * @return  void
     */
    public function set_query_variables()
    {/*{{{*/

        foreach ($this->get_default_tags() as $type => $tag)
        {
            if ($type == 'cat_application')
            {
                add_rewrite_tag(
                    '%cat_industry%'
                    ,'([^/]+)'
                    ,''
                );
            }
            else
            {
                add_rewrite_tag(
                    $tag
                    ,'([^/]+)'
                    ,$tag.'='
                );
            }
        }

    }/*}}}*/



    /**
     *  Register Custom Content Types
     *  @return  void
     */

    public function register_content_types()
    {/*{{{*/
        // get the classes to include
        // setup for modular loading of content types
        CAT()->classes = $classes = get_option('cat_new_class_limitation');

        $class_map = CAT()->get_class_post_type_relation();

        $rental = get_option('cat_rental_environment', '');

        if(!empty($classes)) {

            foreach ($classes as $class_id)
            {
                $type = $class_map[$class_id];
                $name = str_replace("cat_new_", "", $type);

                if(in_array($class_id, $classes)){
                    $tax_slug = $this->get_slug($type);
                    new Post_type($name,array(
                        'post_slug' => '__cat_type_slug_'.$class_id.'__/%cat_'.$name.'_family%'
                        ,'tax_slug' => $tax_slug
                    ));
                }

            }

            if( CAT()->usingIndustries ) {
                $this->industry();
            }
        }

        // All rental families should be enabled if rental is enabled
        if ($rental)
        {
            foreach ($class_map as $class_id => $type)
            {
                $name = str_replace("cat_new_", "", $type);
                $tax_slug = $this->get_slug($type."_rental");

                new Post_type($name.'_rental',array(
                    'create_post_type' => false
                    ,'tax_slug'        => $tax_slug
                    ,'tax_post_type'   => $name
                ));
                if($name === 'allied') {
                    $tax_slug = $this->get_slug($type."_pwr_rental");

                    new Post_type($name.'_pwr_rental',array(
                        'create_post_type' => false
                        ,'tax_slug'        => $tax_slug
                        ,'tax_post_type'   => $name
                    ));
                }
            }
        }

        $this->used();
        $this->used_family();
        $this->used_manufacturer();

    }/*}}}*/


    /**
     * Configures and Registers Industries
     * @return void
     */
    private function industry()
    {/*{{{*/
        if( ! defined('EP_INDUSTRY') )
            define('EP_INDUSTRY', 8388608); // 2^23

        $slug = get_option('cat_industry_slug') ? get_option('cat_industry_slug') : 'industries';


        // register the industries
        register_post_type(
            'cat_industry',
            apply_filters(
            'cat_industry_config'
            ,array(
                'public'         => true
                ,'map_meta_cap'  => true
                ,'has_archive'   => false
                ,'rewrite'       => array(
                   'slug'        => $slug
                   ,'with_front' => false
                   ,'ep_mask'    => EP_INDUSTRY
                )
                ,'supports'      => array('title', 'editor', 'thumbnail')
                ,'menu_icon'     => 'dashicons-admin-generic'
                ,'menu_position' => 40
                ,'labels'        => apply_filters(
                    'cat_new_equipment_labels'
                    ,array(
                        'name'                => _x( 'Industries', 'post type general name', CAT()->domain )
                        ,'singular_name'      => _x( 'Industry', 'post type singular name', CAT()->domain )
                        ,'menu_name'          => _x( 'CAT Industries', 'admin menu', CAT()->domain )
                        ,'name_admin_bar'     => _x( 'New Industry', 'add new on admin bar', CAT()->domain )
                        ,'add_new'            => _x( 'Add New', 'new machine', CAT()->domain )
                        ,'add_new_item'       => __( 'Add Industry', CAT()->domain )
                        ,'new_item'           => __( 'New Industry', CAT()->domain )
                        ,'edit_item'          => __( 'Edit Industry', CAT()->domain )
                        ,'view_item'          => __( 'View Industry', CAT()->domain )
                        ,'all_items'          => __( 'All Industries', CAT()->domain )
                        ,'search_items'       => __( 'Search new industries', CAT()->domain )
                        ,'parent_item_colon'  => __( 'Parent industry:', CAT()->domain )
                        ,'not_found'          => __( 'No industries found.', CAT()->domain )
                        ,'not_found_in_trash' => __( 'No industries found in trash.', CAT()->domain )
                    )
                )
            )
        ));

        if( CAT()->usingApplications )
        {
            register_post_type(
                'cat_application',
                apply_filters(
                'cat_application_config'
                ,array(
                    'public'         => true
                    ,'map_meta_cap'  => true
                    ,'has_archive'   => false
                    ,'rewrite'       => array(
                       'slug'        => 'industries/%cat_industry%/application'
                       ,'with_front' => false
                    )
                    ,'supports'      => array('title', 'editor', 'thumbnail')
                    ,'show_in_menu'  => 'edit.php?post_type=cat_industry'
                    ,'labels'        => apply_filters(
                        'cat_new_equipment_labels'
                        ,array(
                            'name'                => _x( 'Applications', 'post type general name', CAT()->domain )
                            ,'singular_name'      => _x( 'Application', 'post type singular name', CAT()->domain )
                            ,'menu_name'          => _x( 'CAT Applications', 'admin menu', CAT()->domain )
                            ,'name_admin_bar'     => _x( 'New Application', 'add new on admin bar', CAT()->domain )
                            ,'add_new'            => _x( 'Add New', 'new machine', CAT()->domain )
                            ,'add_new_item'       => __( 'Add Application', CAT()->domain )
                            ,'new_item'           => __( 'New Application', CAT()->domain )
                            ,'edit_item'          => __( 'Edit Application', CAT()->domain )
                            ,'view_item'          => __( 'View Application', CAT()->domain )
                            ,'all_items'          => __( 'Applications', CAT()->domain )
                            ,'search_items'       => __( 'Search new application', CAT()->domain )
                            ,'parent_item_colon'  => __( 'Parent application:', CAT()->domain )
                            ,'not_found'          => __( 'No applications found.', CAT()->domain )
                            ,'not_found_in_trash' => __( 'No applications found in trash.', CAT()->domain )
                        )
                    )
                )
            ));
        }


    }/*}}}*/




    /**
     * Configures and Registers Allied Rentals
     * @return void
     */
    private function used()
    {/*{{{*/
        $slug = $this->get_slug('cat_used');

        $args = apply_filters(
            'cat_used_config'
            ,array(
                'public'         => true
                ,'map_meta_cap'  => true
                ,'has_archive'   => true
                ,'rewrite'       => array(
                   'slug'        => $slug.'/%cat_used_family%'
                   ,'with_front' => false
                )
                ,'supports'      => array('title', 'editor', 'excerpt', 'thumbnail')
                ,'menu_icon'     => 'dashicons-admin-generic'
                ,'menu_position' => 45
                ,'labels'        => apply_filters(
                    'cat_new_attachment_labels'
                    ,array(
                        'name'               => _x( 'Used Machines', 'post type general name', CAT()->domain ),
                        'singular_name'      => _x( 'Used Machine', 'post type singular name', CAT()->domain ),
                        'menu_name'          => _x( 'Used Machines', 'admin menu', CAT()->domain ),
                        'name_admin_bar'     => _x( 'Used Machine', 'add new on admin bar', CAT()->domain ),
                        'add_new'            => _x( 'Add New', 'new product', CAT()->domain ),
                        'add_new_item'       => __( 'Add Product', CAT()->domain ),
                        'new_item'           => __( 'New Product', CAT()->domain ),
                        'edit_item'          => __( 'Edit Product', CAT()->domain ),
                        'view_item'          => __( 'View Product', CAT()->domain ),
                        'all_items'          => __( 'All Machines', CAT()->domain ),
                        'search_items'       => __( 'Search used machines', CAT()->domain ),
                        'parent_item_colon'  => __( 'Parent Product:', CAT()->domain ),
                        'not_found'          => __( 'No products found.', CAT()->domain ),
                        'not_found_in_trash' => __( 'No products found in trash.', CAT()->domain )
                    )
                )
                // ,'capability_type'     => array('used-youtube','cat_used_machine')
            )
        );

        register_post_type( 'cat_used_machine', $args );
    }/*}}}*/



    /**
     * Configures and Registers used Families
     * @return void
     */
    private function used_family()
    {/*{{{*/
        $slug = $this->get_slug('cat_used');

        $args = apply_filters(
            'cat_used_tax_config'
            ,array(
                'rewrite' => array(
                    'slug' => $slug
                    ,'with_front' => false
                )
                ,'hierarchical' => true
                ,'show_admin_column' => true
                ,'labels' => apply_filters(
                    'cat_new_used_tax_labels'
                    ,array(
                        'name'              => _x( 'Used Families', 'taxonomy general name' )
                        ,'singular_name'     => _x( 'Used Family', 'taxonomy singular name' )
                        ,'search_items'      => __( 'Search Families' )
                        ,'all_items'         => __( 'All Families' )
                        ,'parent_item'       => __( 'Parent Family' )
                        ,'parent_item_colon' => __( 'Parent Family:' )
                        ,'edit_item'         => __( 'Edit Family' )
                        ,'update_item'       => __( 'Update Family' )
                        ,'add_new_item'      => __( 'Add New Family' )
                        ,'new_item_name'     => __( 'New Family Name' )
                        ,'menu_name'         => __( 'Families' )
                    )
                )
            )
        );

        register_taxonomy(
             'cat_used_machine_family'
            ,apply_filters( 'cat_used_tax_post_types', array('cat_used_machine') )
            ,$args
        );
    }/*}}}*/

    /**
     * Configures and Registers used Families
     * @return void
     */
    private function used_manufacturer()
    {/*{{{*/
        $slug = $this->get_slug('cat_used');

        $args = apply_filters(
            'cat_used_tax_config'
            ,array(
                'rewrite' => array(
                    'slug' => $slug.'/make'
                    ,'with_front' => false
                )
                ,'hierarchical' => true
                ,'show_admin_column' => true
                ,'labels' => apply_filters(
                    'cat_new_used_tax_labels'
                    ,array(
                        'name'              => _x( 'Used Manufacturers', 'taxonomy general name' )
                        ,'singular_name'     => _x( 'Used Manufacturer', 'taxonomy singular name' )
                        ,'search_items'      => __( 'Search Manufacturers' )
                        ,'all_items'         => __( 'All Manufacturers' )
                        ,'parent_item'       => __( 'Parent Manufacturer' )
                        ,'parent_item_colon' => __( 'Parent Manufacturer:' )
                        ,'edit_item'         => __( 'Edit Manufacturer' )
                        ,'update_item'       => __( 'Update Manufacturer' )
                        ,'add_new_item'      => __( 'Add New Manufacturer' )
                        ,'new_item_name'     => __( 'New Manufacturer Name' )
                        ,'menu_name'         => __( 'Manufacturers' )
                    )
                )
            )
        );

        register_taxonomy(
             'cat_used_machine_manufacturer'
            ,apply_filters( 'cat_used_manufacturer_post_types', array('cat_used_machine') )
            ,$args
        );
    }/*}}}*/
    
    public function cat_pre_get_posts( $query ) {
        // WebFX MAJ Note:
        //     - Make sure the query does not affect other queries ("nav_menu_item", "acf_field", etc.)
        // ref: 
        //     - https://wordpress.stackexchange.com/questions/35264/wp-nav-menu-not-appearing-for-a-couple-pages
        //     - https://wordpress.org/support/topic/wp-nav-menu-dissapears-in-category-pages-1/
        if ( ! is_admin() && is_tax( 'cat_used_machine_family' ) && empty( $query->get( 'post_type' ) ) ) {
            $query->set( 'meta_query', array(
                array(
                    'relation' => 'OR',
                    'model_clause' => array(
                        'key'     => 'model',
                        'compare' => 'EXISTS',
                    ),
                    'year_clause' => array(
                        'key'     => 'year',
                        'compare' => 'EXISTS',
                    ),
                    'model_clause' => array(
                        'key'     => 'model',
                        'compare' => 'NOT EXISTS',
                    ),
                )
            ) );
            $query->set( 'orderby', array(
                'model_clause' => 'ASC',
                'year_clause'  => 'ASC',
                'title' => 'ASC'
            ) );
        }
    }

    /**
     * Filters a post permalink to replace the tag placeholder with the first
     * used term from the taxonomy in question.
     *
     * @since 1.0.0
     *
     * @param string $permalink The existing permalink URL.
     * @return object $post The post object that this permalink belongs to.
     */
    public function filter_post_link( $permalink, $post )
    {/*{{{*/
        $post_type = get_post_type($post->ID);
        $terms = array();
        $using_rental_terms = false;

        // Replace cat_type_slug with new or rental
        if (substr($post_type, 0, 8) == 'cat_new_')
        {

            // Get the custom taxonomy terms in use by this post
            $terms = get_the_terms( $post->ID, $post_type.'_family' );
            global $wp_query;
            if (empty($terms) or $wp_query->is_cat_rental )
            {
                $terms = get_the_terms( $post->ID, $post_type.'_rental_family' );
                if (!empty($terms))
                    $using_rental_terms = true;
            }

            global $wp_query;

            $slug_type = $post_type;
            if ($wp_query->is_cat_rental or $using_rental_terms)
                $slug_type = $post_type . '_rental';
            $slug = $this->get_slug($slug_type);

            $permalink = preg_replace('/__cat_type_slug_[^_]+__/', $slug, $permalink);
        }

        // Replace cat_type_slug with new or rental
        if (substr($post_type, 0, 9) == 'cat_used_')
        {
            // Get the custom taxonomy terms in use by this post
            $terms = get_the_terms( $post->ID, 'cat_used_machine_family' );
            $slug = $this->get_slug('cat_used');
            $permalink = preg_replace('/__cat_type_slug_[^_]+__/', $slug, $permalink);
        }

        $replace = $this->get_default_tag($post_type);

        // Abort early if no tag for post type
        if ( false === $replace )
            return $permalink;

        // Abort early if the placeholder rewrite tag isn't in the URL
        if ( false === strpos( $permalink, $replace ) )
            return $permalink;

        if($replace == '%cat_industry%')
        {
            global $wpdb;
            $id = $wpdb->get_col(
                "SELECT DISTINCT industry_id
                 FROM {$wpdb->prefix}cat_term_industries
                 WHERE application_id = {$post->ID}"
            );

            if ( ! empty( $id ) )
            {
                $industry = get_post(array_shift($id));

                if(isset($industry)){
                    $permalink  = str_replace( $replace, $industry->post_name, $permalink );
                }
            }
        }
        else
        {


            // If no terms are assigned to this post, use the taxonomy slug instead (can't leave the placeholder there)
            if ( is_array($terms) and !empty( $terms ) )
            {
                $first_term = reset( $terms );
                $permalink  = str_replace( $replace, $first_term->slug, $permalink );
            }
        }
        return $permalink;
    }/*}}}*/


    /**
     * Listens for ajax call to update status
     * @return [type] [description]
     */
    public function save_featured_status_ajax()
    {/*{{{*/
        $post_id = $_POST['post_id'];
        $checked = intval($_POST['checked']);

        $result = ($checked > 0 )
                  ? update_post_meta( $post_id, 'featured', $checked)
                  : delete_post_meta( $post_id, 'featured', '1' );

        die(json_encode(array('post_id' => $post_id, 'checked' => $checked, 'result' => $result )));
    }/*}}}*/


    public static function get_class_default_slug($id, $type='new')
    {/*{{{*/
        $self = self::instance();

        $suffix = "";
        if (!empty($type) and $type != "new")
        {
            $suffix = "_" . $type;
        }

        $class_type_map = CAT()->get_class_post_type_relation();

        if (isset($class_type_map[$id]))
        {
            return $self->get_default_slug($class_type_map[$id].$suffix);
        }

        if ($id == 'used')
            return $self->get_default_slug('cat_used');

    }/*}}}*/

}


Post_types::instance();
