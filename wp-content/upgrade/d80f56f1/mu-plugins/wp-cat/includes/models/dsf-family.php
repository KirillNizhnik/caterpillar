<?php
namespace Cat\Models;

class DSF_Family extends DSF_Term
{
    public $taxonomy = 'cat_used_machine_family';


    public static function create_from_xml($item)
    {
        // if( isset($item->{'product-family-categories'}) )
        // {
        //     return self::create_from_tag($item);
        // }
        // else
        // {
        //     return self::create_from_attributes($item);
        // }
        
        if ( isset($item->attributes()->{'product-family'}) ) {
            return self::create_from_attributes($item);
        } else {
            return self::create_from_tag($item);
        }
    }

    private static function create_from_attributes($item)
    {
        $return         = array();
        $family_name    = pretty_name($item->attributes()->{'product-family'});
        $name_segments  = explode('-', $family_name);
        $class          = self::find_or_new( 'Machinery' );

        if( count($name_segments) > 1)
        {
            $family_name = pluralize(pretty_name(trim($name_segments[0])));
            $type_name   = pluralize(pretty_name(trim($name_segments[1])));

            $family_term = self::find_or_new( $family_name, array('parent' => $class->term_id)  );
            $type_term   = self::find_or_new( $type_name, array('parent' => $family_term->term_id) );
        }
        else
        {
            $family_name = pluralize($family_name);
            $family_term = self::find_or_new( $family_name , array('parent' => $class->term_id) );
        }

        $family_term->image_on    = '';
        $family_term->image_off   = '';
        $family_term->class       = $class->term_id;
        $family_term->save();

        $return[] = $class;
        $return[] = $family_term;

        if( isset($type_term) ) {
            $return[] = $type_term;
        }

        return $return;
    }

    private static function create_from_tag($item)
    {
        $return = array();
        $tag    = $item->{'product-family-categories'};
        $class_name = $tag->{'category-class'}->attributes()->name;
        if ( empty($class_name)) return [];

        $class  = self::find_or_new( (string) pretty_name($tag->{'category-class'}->attributes()->name) );

        $class->save();

        $family_name   = $tag->category->attributes()->name;
        $name_segments = explode(' / ', $family_name);

        if( count($name_segments) > 1)
        {
            $family_name = pluralize(pretty_name(trim($name_segments[0])));
            $type_name   = pluralize(pretty_name(trim($name_segments[1])));

            $family_term = self::find_or_new( $family_name, array('parent' => $class->term_id)  );
            $type_term   = self::find_or_new( $type_name, array('parent' => $family_term->term_id) );
        }
        else
        {
            $family_name = pluralize($family_name);
            $family_term = self::find_or_new( $family_name , array('parent' => $class->term_id) );
        }

        $family_term->image_on  = (string) $tag->category->attributes()->{'image-on'};
        $family_term->image_off = (string) $tag->category->attributes()->{'image-off'};
        $family_term->class     = $class->name;
        $family_term->save();


        $return[] = $class;
        $return[] = $family_term;

        if( isset($type_term) ) {
            $return[] = $type_term;
        }

        return $return;
    }

}
