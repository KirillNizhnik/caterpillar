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


class New_Home_Outdoor_Power_Family extends \Cat\Core\Abstracts\Taxonomy
{
    public $name = 'cat_new_home_power_family';
}

new New_Home_Outdoor_Power_Family();

class New_Home_Outdoor_Power_Rental_Family extends \Cat\Core\Abstracts\Taxonomy
{
    public $name = 'cat_new_home_power_rental_family';
}

new New_Home_Outdoor_Power_Rental_Family();
