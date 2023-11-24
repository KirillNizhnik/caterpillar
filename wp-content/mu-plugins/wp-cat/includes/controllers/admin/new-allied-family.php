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


class New_Allied_Family extends \Cat\Core\Abstracts\Taxonomy
{
    public $name = 'cat_new_allied_family';
}

new New_Allied_Family();

class New_Allied_Rental_Family extends \Cat\Core\Abstracts\Taxonomy
{
    public $name = 'cat_new_allied_rental_family';
}

new New_Allied_Rental_Family();

class New_Allied_Pwr_Rental_Family extends \Cat\Core\Abstracts\Taxonomy
{
    public $name = 'cat_new_allied_pwr_rental_family';
}

new New_Allied_Pwr_Rental_Family();
