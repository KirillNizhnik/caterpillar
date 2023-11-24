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


class New_Machines_Family extends \Cat\Core\Abstracts\Taxonomy
{
    public $name = 'cat_new_machine_family';
}

new New_Machines_Family();

class New_Machines_Rental_Family extends \Cat\Core\Abstracts\Taxonomy
{
    public $name = 'cat_new_machine_rental_family';
}

new New_Machines_Rental_Family();
