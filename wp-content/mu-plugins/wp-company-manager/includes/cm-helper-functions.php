<?php
/**
 * Helpers
 */
if ( ! function_exists( 'wpcm_template' ) ) {
    /**
     * Get WPCM Templates
     */
    function wpcm_template( $template, $args = array(), $require_once = false ) {
        if ( $args && is_array( $args ) ){
            extract( $args );
        }
        if ( substr( $template, -4 ) != '.php' ){
            $file = $template . '.php';
        } else {
            $file = $template;
        }
        if ( ! $template = locate_template( array( 'company/' . $file ), false, $require_once ) ) {
            $template = WPCM()->plugin_path . 'templates/' . $file;
        }
        if ( $require_once ) {
            include_once $template;
        } else {
            include $template;
        }
    }
}

if ( ! function_exists( 'get_geo_info_address' ) ) {
    /**
     * Geocode
     */
    function get_geo_info_address( $address ) {
        global $wpdb;
        $checkdb = check_local_db( $address );
        if ( ! empty( $checkdb ) ) {
            $result = $checkdb;
            $count = $wpdb->get_results( "SELECT `total_queries` FROM `{$wpdb->base_prefix}wpcm_queries` WHERE userquery = '{$address}'" );
            $newcount = (int) $count[ 0 ]->total_queries + 1;
            $wpdb->update( "{$wpdb->base_prefix}wpcm_queries", array(
                'total_queries' => $newcount,
                'last_queried' => date( "Y-m-d H:i:s" )
            ), array( 'userquery' => $address ) );
        } else {
            $transient = 'google_address_' . md5( $address );
            $result = get_transient( $transient );
            if ( ! $result ) {
                // GEOCODE_API_KEY is defined in wp-company-manager.php
                $url = esc_url_raw( 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&key='.GEOCODE_API_KEY );
                $result = wp_remote_get($url, array(
                    'headers' => array(
                        'Content-type: application/json'
                    )
                ));
                set_transient( $transient, $result, DAY_IN_SECONDS );
            }
            $json = json_decode( wp_remote_retrieve_body( $result ) );
            $location = new stdClass;
            if ( ! empty( $json->results ) ) {
                // Successful lookup
                $location->lat = $json->results[ 0 ]->geometry->location->lat;
                $location->lng = $json->results[ 0 ]->geometry->location->lng;
            } else {
                // Failed lookup - save transient as "Invalid Lookup"
                $location->lat = '';
                $location->lng = '';
            }
            $result = $location;
            if ( CM_SAVE_SEARCH ) {
                $inserted = $wpdb->insert( "{$wpdb->base_prefix}wpcm_queries", array(
                    'userquery' => $address,
                    'latitude' => $result->lat,
                    'longitude' => $result->lng,
                    'created_at' => date( "Y-m-d H:i:s" ),
                    'last_queried' => date( "Y-m-d H:i:s" ),
                    'total_queries' => 1
                ));
            }
        }
        return $result;
    }
}

if ( ! function_exists( 'distance' ) ) {
    /**
     * Calculate Distance
     */
    function distance( $lat1, $lng1, $lat2, $lng2, $miles = true ) {
        $pi80 = M_PI / 180;
        $lat1 *= $pi80;
        $lng1 *= $pi80;
        $lat2 *= $pi80;
        $lng2 *= $pi80;
        $r = 6372.797; // mean radius of Earth in km
        $dlat = $lat2 - $lat1;
        $dlng = $lng2 - $lng1;
        $a = sin( $dlat / 2 ) * sin( $dlat / 2 ) + cos( $lat1 ) * cos( $lat2 ) * sin( $dlng / 2 ) * sin( $dlng / 2 );
        $c = 2 * atan2( sqrt( $a ), sqrt( 1 - $a ) );
        $km = $r * $c;
        return ( $miles ? ( $km * 0.621371192 ) : $km );
    }
}

if ( ! function_exists( 'valid_postal_code' ) ) {
/**
 * Validate a postal code.
 *
 * @param $value
 *   A postal code as string.
 * @return
 *   TRUE or FALSE if it validates.
 */
    function valid_postal_code($value) {
        $zip_regex = '/\\A\\b[0-9]{5}(?:-[0-9]{4})?\\b\\z/i';
        return preg_match($zip_regex, $value);
    }
}

if ( ! function_exists( 'check_local_db' ) ) {
    function check_local_db($value) {
        global $wpdb;
        if ( CM_SAVE_SEARCH ) {
            $results = $wpdb->get_results( "SELECT * FROM `{$wpdb->base_prefix}wpcm_queries` WHERE userquery = '{$value}'" );
            if ( ! empty( $results ) ) {
                $location = new \stdClass;
                $location->lat = $results[ 0 ]->latitude;
                $location->lng = $results[ 0 ]->longitude;
                return $location;
            }
        }
        return false;
    }
}
