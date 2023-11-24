<?php
namespace Cat\Controllers\Admin;

/**
 * CNF_Admin_Product
 * makes any customizations to the admin views
 * for product post type
 *
 * @Package CAT New Feed/Admin
 * @category admin
 * @author WebFX
 */

if ( ! defined('ABSPATH') )
	exit;


class New_Home_Outdoor_Power extends New_Abstract
{
	protected static $instance;

    public $post_type = "cat_new_home_power";

	/* Static Singleton Factory Method */
	public static function instance()
	{/*{{{*/
		if (!isset(self::$instance)) {
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}/*}}}*/

}
New_Home_Outdoor_Power::instance();
