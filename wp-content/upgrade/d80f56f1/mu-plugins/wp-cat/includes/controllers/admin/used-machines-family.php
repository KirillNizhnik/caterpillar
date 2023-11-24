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


class Used_Machines_Family extends \Cat\Core\Abstracts\Taxonomy
{
    public $name = 'cat_used_machine_family';
}

return new Used_Machines_Family();
