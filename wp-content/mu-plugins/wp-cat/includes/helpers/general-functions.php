<?php

/**
 * array_pluck
 * pluck a column out of a collection
 *
 * @var 'string' $pluck the key to pluck
 * @var  'array' $arr the array to pluck the values out of
 * @return  array the resulting array of values
 */
if( ! function_exists('pluck'))
{
    function pluck($pluck, $arr)
    {
        return array_map(function ($item) use ($pluck) {
            if(is_array($item) )
                return $item[$pluck];
            else
                return $item->{$pluck};
        }, $arr);
    }
}

/**
 * array_pluck_where
 * pluck a column out of a collection
 *
 * @var 'string' $pluck the key to pluck
 * @var  'array' $arr the array to pluck the values out of
 * @return  array the resulting array of values
 */
if( ! function_exists('pluck_where'))
{
    function pluck_where($pluck, $where, $arr)
    {
        $items = array_map(function ($item) use ($pluck, $where) {
            $where_key   = array_keys($where);
            $where_key   = array_shift($where_key);
            $where_value = array_shift($where);

            if(is_array($item) ){
                if( $item[$where_key] == $where_value )
                    return $item[$pluck];
            }
            else {
                if( $item->{$where_key} == $where_value )
                    return $item->{$pluck};
            }
        }, $arr);

        return array_filter( $items );
    }
}


/**
 * Get an item from an array or object using "dot" notation.
 *
 * @param  mixed   $target
 * @param  string  $key
 * @param  mixed   $default
 * @return mixed
 */
if ( ! function_exists('data_get'))
{
    function data_get($target, $key, $default = null)
    {
        if (is_null($key)) return $target;
        foreach (explode('.', $key) as $segment)
        {
            if (is_array($target))
            {
                if ( ! array_key_exists($segment, $target))
                {
                    return $default;
                }
                $target = $target[$segment];
            }
            elseif ($target instanceof ArrayAccess)
            {
                if ( ! isset($target[$segment]))
                {
                    return $default;
                }
                $target = $target[$segment];
            }
            elseif (is_object($target))
            {
                if ( ! isset($target->{$segment}))
                {
                    return $default;
                }
                $target = $target->{$segment};
            }
            else
            {
                return $default;
            }
        }
        return $target;
    }
}



/**
 * Return the first element in an array passing a given truth test.
 *
 * @param  array  $array
 * @param  callable  $callback
 * @param  mixed  $default
 * @return mixed
 */
if ( ! function_exists('array_first'))
{
    function array_first($array, $callback, $default = null)
    {
        foreach ($array as $key => $value)
        {
            if (call_user_func($callback, $key, $value)) return $value;
        }
        return value($default);
    }
}


/**
 * Return the default value of the given value.
 *
 * @param  mixed  $value
 * @return mixed
 */
if ( ! function_exists('value'))
{
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}



/**
 * Fix word Capitalization and trim whitespace.
 */
if( ! function_exists('pretty_name'))
{
    function pretty_name($name)
    {
        return ucwords(strtolower(trim($name)));
    }
}



/**
 * Make a word plural
 * @param  string $word word in single form
 * @return string       word in plural form
 */
if( ! function_exists('pluralize'))
{
    function pluralize($word){
        $plural = array(
            '/(quiz)$/i'               => '\1zes',
            '/^(ox)$/i'                => '\1en',
            '/([m|l])ouse$/i'          => '\1ice',
            '/(matr|vert|ind)ix|ex$/i' => '\1ices',
            '/(x|ch|ss|sh)$/i'         => '\1es',
            '/([^aeiouy]|qu)ies$/i'    => '\1y',
            '/([^aeiouy]|qu)y$/i'      => '\1ies',
            '/(hive)$/i'               => '\1s',
            '/(?:([^f])fe|([lr])f)$/i' => '\1\2ves',
            '/sis$/i'                  => 'ses',
            '/([ti])um$/i'             => '\1a',
            '/(buffal|tomat)o$/i'      => '\1oes',
            '/(bu)s$/i'                => '\1ses',
            '/(alias|status)/i'        => '\1es',
            '/(octop|vir)us$/i'        => '\1i',
            '/(ax|test)is$/i'          => '\1es',
            '/s$/i'                    => 's',
            '/$/'                      => 's');

        $uncountable = array('equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep');

        $irregular = array(
            'person' => 'people',
            'man'    => 'men',
            'child'  => 'children',
            'sex'    => 'sexes',
            'move'   => 'moves');

        $lowercased_word = strtolower($word);

        foreach($uncountable as $_uncountable){
            if(substr($lowercased_word, (-1 * strlen($_uncountable))) == $_uncountable){
                return $word;
            }
        }

        foreach($irregular as $_plural => $_singular){
            if(preg_match('/(' . $_plural . ')$/i', $word, $arr)){
                return preg_replace('/(' . $_plural . ')$/i', substr($arr[0], 0, 1) . substr($_singular, 1), $word);
            }
        }

        foreach($plural as $rule => $replacement){
            if(preg_match($rule, $word)){
                return preg_replace($rule, $replacement, $word);
            }
        }
        return false;
    }
}


/**
 * Singularizes English nouns.
 *
 * @param  string  $word    English noun to singularize
 * @return string Singular noun.
 */
if( ! function_exists('singularize'))
{
    function singularize($word){
        $singular = array(
            '/(quiz)zes$/i'                                                    => '\1',
            '/(matr)ices$/i'                                                   => '\1ix',
            '/(vert|ind)ices$/i'                                               => '\1ex',
            '/^(ox)en/i'                                                       => '\1',
            '/(alias|status)es$/i'                                             => '\1',
            '/([octop|vir])i$/i'                                               => '\1us',
            '/(cris|ax|test)es$/i'                                             => '\1is',
            '/(shoe)s$/i'                                                      => '\1',
            '/(o)es$/i'                                                        => '\1',
            '/(bus)es$/i'                                                      => '\1',
            '/([m|l])ice$/i'                                                   => '\1ouse',
            '/(x|ch|ss|sh)es$/i'                                               => '\1',
            '/(m)ovies$/i'                                                     => '\1ovie',
            '/(s)eries$/i'                                                     => '\1eries',
            '/([^aeiouy]|qu)ies$/i'                                            => '\1y',
            '/([lr])ves$/i'                                                    => '\1f',
            '/(tive)s$/i'                                                      => '\1',
            '/(hive)s$/i'                                                      => '\1',
            '/([^f])ves$/i'                                                    => '\1fe',
            '/(^analy)ses$/i'                                                  => '\1sis',
            '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
            '/([ti])a$/i'                                                      => '\1um',
            '/(n)ews$/i'                                                       => '\1ews',
            '/s$/i'                                                            => '',
        );

        $uncountable = array('equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep');

        $irregular = array(
            'person' => 'people',
            'man'    => 'men',
            'child'  => 'children',
            'sex'    => 'sexes',
            'move'   => 'moves');

        $lowercased_word = strtolower($word);
        foreach($uncountable as $_uncountable){
            if(substr($lowercased_word, (-1 * strlen($_uncountable))) == $_uncountable){
                return $word;
            }
        }

        foreach($irregular as $_plural => $_singular){
            if(preg_match('/(' . $_singular . ')$/i', $word, $arr)){
                return preg_replace('/(' . $_singular . ')$/i', substr($arr[0], 0, 1) . substr($_plural, 1), $word);
            }
        }

        foreach($singular as $rule => $replacement){
            if(preg_match($rule, $word)){
                return preg_replace($rule, $replacement, $word);
            }
        }

        return $word;
    }
}

/**
 * Make cat_new_machine_family taxonomy archive pages give machines in alphabetical order
 *
 */
function fx_order_machine_families($query) {
	if ( !is_admin() && $query->is_tax( 'cat_new_machine_family' ) && $query->is_main_query() ) {	
		$query->set( 'orderby', 'title' );
		$query->set( 'order', 'ASC' );
		return $query;
	}
}
add_filter('pre_get_posts', 'fx_order_machine_families', 100);

function LOGME($msg=[])
{
    error_log(json_encode($msg) . PHP_EOL, 3, plugin_dir_path(CAT_PLUGIN_FILE) . 'families.log');
}