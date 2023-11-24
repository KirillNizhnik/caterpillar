<?php
namespace Cat\Models;

class DSF_Manufacturer extends DSF_Term
{
    public $taxonomy = 'cat_used_machine_manufacturer';

    public static function create_from_xml($item)
    {
        $category = $item->{'product-family-categories'};
        $manufacturer_name = pretty_name($item->attributes()->manufacturer);


        $manufacturer = self::find_or_new( pretty_name($manufacturer_name) );

        if(isset($item->attributes()->{'manufacturer-code'})){
            $manufacturer->manufacturer_code = (string) $item->attributes()->{'manufacturer'};

        }
        $manufacturer->save();


        return $manufacturer;
    }

}