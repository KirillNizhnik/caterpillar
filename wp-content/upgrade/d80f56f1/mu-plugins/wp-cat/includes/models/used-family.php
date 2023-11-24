<?php
namespace Cat\Models;

if ( ! defined('ABSPATH') )
    exit;


class Used_Family
{
    private static $related_families = array(
        'Track Type Tractors (Dozers)' => 'Dozers'
        ,'Compact Excavators'          => 'Excavators'
        ,'Hydraulic Excavators'        => 'Excavators'
        ,'Skid Steer Loader'           => 'Skid Steer Loaders'
        ,'Multi Terrain Loader'        => 'Compact Track and Multi Terrain Loaders'
        ,'Telescopic Handlers'         => 'Telehandlers'
        ,'Off Highway Trucks'          => 'Off-Highway Trucks'
        ,'Scoops'                      => 'Underground – Room and Pillar'
        ,'Paving'                      => 'Asphalt Pavers'
        ,'Wheel Excavator'             => 'Wheel Excavators'
        ,'Articulated Dump Trucks'     => 'Articulated Trucks'
        ,'Generators'                  => 'Electric Power Generation'
        ,'Broom'                       => 'Brooms'
    );

    public static function get_make_code($make)
    {
        global $wpdb;

        $keys = $wpdb->get_col($wpdb->prepare(
            "SELECT `key` FROM `{$wpdb->base_prefix}used_make_keys`
             WHERE `make` = %s
            ",
            $make
        ));

        if($keys) {
            return $keys[0];
        }
    }


    public static function get_related_family_name($family)
    {
        $family = ucwords(strtolower($family));



        if(isset(self::$related_families[$family])) {
            return self::$related_families[$family];
        }

        return $family;
    }

    public static function get_family_name_from_related($family)
    {
        $family = ucwords(strtolower($family));
        $families = array_flip(self::$related_families);


        if(isset($families[$family])) {
            return $families[$family];
        }

        return $family;
    }

}