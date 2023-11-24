<?php
namespace Cat\Core\Abstracts;
use \WP_Error;

/**
 * Settings
 * base logic for models, only used to extend common logic
 *
 * @Package CAT New Feed/Base
 *
 * @category abstract
 * @author WebpageFX
 */

if ( ! defined('ABSPATH') )
    exit;


abstract class Importer
{
    
    protected $token;
    
    protected function __construct( )
    {
    }

    abstract public function import();


    /**
     * Returns the XML tree from a url
     *
     * @param  [string] $url the url to get the xml from
     * @return [simple_xml_element] xml tree from the url
     */

    protected function get_xml($url)
    {

        try {
            //$url = "https://cpc.cat.com/api/v2/xml/H430/406tree_en.xml";
            //The auth is only used when calling this function which is only used in the new feeds.
            $token = $this->get_token();
            $response = wp_remote_request( $url, [
                'method' => 'GET',
                'headers' => array(
                    'Authorization' => 'Bearer ' . $token,
                ),
            ]);
             $raw = wp_remote_retrieve_body($response);
             $raw = str_ireplace('http://s7d2.scene7.com', 'https://s7d2.scene7.com', $raw);
             $xml = simplexml_load_string( $raw );
        } catch(Exception $e) {
            return new WP_Error('XML Failed', __($e));
        }

        if (! $xml){
            return new WP_Error('XML Failed', __("Unable to load XML file - there may be a syntax error"));
        }
        
       // var_dump($url); die();

        //$body = print_r($xml);
       /* $filename = str_replace('/', '', $url);
        $filename = str_replace('https', '', $filename);
        $filename = str_replace(':', '', $filename);
        ob_start();                    // start buffer capture
        var_dump( $xml);           // dump the values
        $contents = ob_get_contents(); // put the buffer into a variable
        ob_end_clean();
        if (!file_exists(plugin_dir_path( CAT_PLUGIN_FILE ) . 'all-urls-body/' . date("Y-m-d"))) {
                mkdir(plugin_dir_path( CAT_PLUGIN_FILE ) . 'all-urls-body/' . date("Y-m-d"), 0777, true);
            }
        file_put_contents(plugin_dir_path( CAT_PLUGIN_FILE ) . 'all-urls-body/' . date("Y-m-d") . '/' . $filename . '.log', '');
        $f = @fopen(plugin_dir_path( CAT_PLUGIN_FILE ) . 'all-urls-body/' . date("Y-m-d") . '/'  . $filename . '.log', "r+");
            if ($f !== false) {
                ftruncate($f, 0);
                fclose($f);
            }
        error_log( $contents . PHP_EOL, 3, plugin_dir_path( CAT_PLUGIN_FILE ) . 'all-urls-body/' . date("Y-m-d") . '/'  . $filename . '.log' ); */
        
        return $xml;
    }
    
    /**
     * Get auth token for API
     *
     * @return string $token Bearer token for API auth
     */
    protected function get_token()
    {
        if ($this->token) {
            return $this->token;
        } else {
            $response = wp_remote_request('https://fedlogin.cat.com/as/token.oauth2', [
                'method' => 'POST',
                'body' => [
                    'grant_type' => 'client_credentials',
                    'scope' => 'read:all',
                ],
                'headers' => array(
                    'Authorization' => 'Basic ' . base64_encode( CAT()->fetch_sales_channel_code_user() . ':' . CAT()->fetch_api_auth_secret_key() ),
                ),
            ]);

            $response = json_decode($response['body'], false);

            if ( isset($response->access_token) ) {
                $token = $response->access_token;
                $this->token = $token;
            } else {
                //To view error in the devtools network tab
                echo json_encode($response);
                //To view in the error logs if the error happens when running the cron
                throw new WP_Error($response->error_description, 1);
            }

            return $token;
        }
    }

    /**
     * Check constant to see if we should cache
     * API calls for quicker development
     */
    protected function cache_api() {
        // Set this constant to true in wp-config
        // FOR DEVELOPMENT/TESTING ONLY!
        return defined('WP_CAT_CACHE_API_CALLS') and WP_CAT_CACHE_API_CALLS;
    }
}
