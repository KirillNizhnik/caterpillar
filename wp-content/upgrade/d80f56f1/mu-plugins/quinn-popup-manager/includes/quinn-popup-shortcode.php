<?php


if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Quinn_Popup_Shortcode {

	protected static $_instance = null;

	public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {

    }
	
	public function set_hooks() {
		
		$this->register_popup_shortcode();
		
	}

    public function register_popup_shortcode() {
		
    	add_shortcode('quinnpopup', array($this, 'popup_shortcode'));
		
    }

    public function popup_shortcode($atts) {
		
		if(is_admin()) {
			
			return "";
			
		}
		
		$output = "";
		
		global $popup_post;
		
		$popup_post = $this->get_popup_post();
		
		if(isset($popup_post)) {
					
			$a = shortcode_atts( array(), $atts );

			$this->register_scripts();

			$this->register_styles();

			ob_start();

			$this->include_popup_view();

			$output = ob_get_clean();
			
		}

    	return $output;
    }

    public function include_popup_view() {
		
    	include QUINN_POPUP_DIR_PATH.'/templates/popup_view.php';
		
    }

	private function register_styles() {

		wp_register_style(
		    'magnific-popup-styles', // handle name
		    QUINN_POPUP_URL_PATH . 'assets/js/vendor/magnific-popup/magnific-popup.css', // the URL of the stylesheet
		    array(), // an array of dependent styles
		    '1.1.0', // version number
		    'screen' // CSS media type
		);

		wp_enqueue_style( 'magnific-popup-styles' );
		
		wp_register_style(
		    'quinn-popup-styles', // handle name
		    QUINN_POPUP_URL_PATH . 'assets/css/main.css', // the URL of the stylesheet
		    array(), // an array of dependent styles
		    '0.2', // version number
		    'screen' // CSS media type
		);

		wp_enqueue_style( 'quinn-popup-styles' );

	}

	private function register_scripts() {
		
		wp_register_script('js-cookie', QUINN_POPUP_URL_PATH . 'assets/js/vendor/js-cookie/js.cookie.js', array(),'1.0', true);
		
		wp_enqueue_script('js-cookie');
		
		wp_register_script('magnific-popup', QUINN_POPUP_URL_PATH . 'assets/js/vendor/magnific-popup/jquery.magnific-popup.min.js', array(),'1.1.0', true);
		
		wp_enqueue_script('magnific-popup');
		
		wp_register_script('quinn-popup-script', QUINN_POPUP_URL_PATH . 'assets/js/main.js', array('jquery', 'js-cookie', 'magnific-popup'),'0.1', true);
		
		wp_enqueue_script('quinn-popup-script');

	}
	
	private function get_popup_post() {
		
		$popup_found = null;
		
		$popup_query = new WP_Query(array('posts_per_page' => 1, 'post_type' => 'quinn-popup', 'orderby' => 'date'));
		
		if($popup_query->have_posts()) {
			
			$popup_found = (array) $popup_query->posts[0];
			
			$popup_found['hide_title'] = get_field('hide_title', $popup_found['ID']);
			$popup_found['more_info_link'] = get_field('more_info_link', $popup_found['ID']);
			
			$popup_found['image_url'] = "";
			
			if (has_post_thumbnail($popup_found['ID'])) {
				
				$popup_found['image_url'] = get_the_post_thumbnail_url($popup_found['ID'], 'full');
				
			}
			
			$post_modified = $popup_found['post_date'];
			
			if($popup_found['post_modified']) {
				
				$post_modified = $popup_found['post_modified'];
				
			}
			
			$post_modified = date_format(date_create($post_modified), 'Y_m_d_H_i_s');
			
			$popup_found['cookie_name'] = 'quinn_popup_'.$popup_found['ID'].'_'.$post_modified.'_page_'.get_the_ID();
			
		}
		
		return $popup_found;
		
	}

}

Quinn_Popup_Shortcode::instance()->set_hooks();