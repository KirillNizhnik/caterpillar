<?php
namespace Cat\Models;

/**
 * CNF_Importer_Class
 * reads the class tree files, and pulls out families and products
 *
 * @Package CAT New Feed/Importer
 * @category importer
 * @author WebFX
 */

if ( ! defined('ABSPATH') )
	exit;


class CPC_Class
{
	public $name;
    public $post_type;
	public $families = array();
	public $products = array();
    private $xml;


	public function __construct($xml = '')
	{
		if(!empty($xml)){
            $this->xml = $xml;
            $this->process_from_xml();
        }

	}



    /**
     * Inserts the class Type terms and starts processing families
     *
     * @return null
     */

	public function process_from_xml()
	{
        if(empty($this->xml))
            return false;

        // insert / grab the class term
        $this->name = trim($this->xml->name);

        // grab families and and products for class
        $this->process_families($this->xml->listofgroups, false);

        return $this;
	}




	/**
     * Read the families into an array
     *
     * @param [simple XML obj] $families The xml element to loop through
     * @return null
     */

	public function process_families($families=array(), $parent=array(), $process_products=true)
	{
		foreach($families->product_group as $f)
		{
			$fam = array();
			$attrs = $f->attributes();

			foreach($attrs as $k => $v)
            {
				$fam[trim($k)] = trim($v);
            }

			$fam['name'] = trim($f->name);
			$fam['parent'] = $parent;

			$this->families[] = $fam;

			// process any sub families
			if(isset($f->listofgroups))
				$this->process_families($f->listofgroups, $fam, $process_products);

			if(isset($f->listofproducts) AND $process_products)
				$this->process_products($f->listofproducts, $fam);
		}
	}




	/**
     * Read the products into an array
     *
     * @param [simple XML obj] $products The xml element to loop through
     * @param [array] $family The family the product belongs to
     * @return null
     */

	private function process_products($products=array(), $family=array())
	{
		foreach($products->product as $product)
		{
			$attrs = $product->attributes();
			$pro = array();

			foreach($attrs as $k => $v)
				$pro[trim($k)] = trim($v);

			if(isset($family['parent']['id']))
			{
				$pro['family_id'] = trim($family['parent']['id']);
				$pro['subfamily_id'] = trim($family['id']);
			}
			else
			{
				$pro['family_id'] = trim($family['id']);
			}

			$pro['family'] = trim($family['name']);

			$this->products[] = $pro;
		}
	}

} // end of class CNF_Importer_Class
