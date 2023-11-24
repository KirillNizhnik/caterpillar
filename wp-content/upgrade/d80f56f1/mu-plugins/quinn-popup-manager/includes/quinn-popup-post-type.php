<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

class Quinn_Popup_Post_Type {

	public $domain = 'quinn-popup-manager';

	public $singular_name = 'Pop-Up';

	public $plural_name = 'Pop-Ups';

    public $type = 'quinn-popup';
	
	protected static $_instance = null;

	public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
	
	public function __construct() {
		
	}
	
    public function register_post_type() {

    	$labels = array(
            'name'                => __( $this->plural_name, $this->domain ),
            'singular_name'       => __( $this->singular_name, $this->domain ),
            'all_items'			  => __( 'All '.$this->plural_name, $this->domain ),
            'add_new'             => _x( 'Add New '.$this->singular_name, $this->domain, $this->domain ),
            'add_new_item'        => __( 'Add New '.$this->singular_name, $this->domain ),
            'edit_item'           => __( 'Edit '.$this->singular_name, $this->domain ),
            'new_item'            => __( 'New '.$this->singular_name, $this->domain ),
            'view_item'           => __( 'View '.$this->singular_name, $this->domain ),
            'search_items'        => __( 'Search New '.$this->plural_name, $this->domain ),
            'not_found'           => __( 'No '.$this->plural_name.' found', $this->domain ),
            'not_found_in_trash'  => __( 'No '.$this->plural_name.' found in trash', $this->domain ),
            'parent_item_colon'   => __( 'Parent '.$this->plural_name.':', $this->domain ),
            'menu_name'           => __( $this->plural_name, $this->domain ),
        );

        $args = array(
            'labels'              => $labels,
            'hierarchical'        => false,
            'public'              => true,
            'menu_icon'           => 'dashicons-admin-comments',
            'show_in_nav_menus'   => true,
            'supports'            => array(
                'title',
                'editor',
                'thumbnail'
            )
        );

        register_post_type($this->type, $args);

    }
	
	public function set_hooks() {
		
		add_action( 'init', array($this, 'register_post_type') );
		
	}
	
}

Quinn_Popup_Post_Type::instance()->set_hooks();