<?php
namespace Cat\Controllers\Importers;
use \WP_Error;
use \Cat\Controllers\Progress;
use \Cat\Models\DSF_Manufacturer;
use \Cat\Models\DSF_Family;
use \Cat\Models\DSF_Product;
use \Cat\Controllers\Indexer;

/**
 * The Importer Class to load The used products into the database
 */

// Don't load directly
if ( !defined('ABSPATH') ) die;

class Used_Feed
{
    //Important Spec Names
    const FAMILY_CODE = "family-code";
    const FAMILY_CODE_UNKNOWN = "TU";
    const FAMILY_NAME = "family";
    const PRODUCT_NAME = "model";
    const PRODUCT_NAME_ALT = "description";
    const PRODUCT_ID = "unit-number";

    protected static $instance;

    private $xml_url;
    private $xml = null;
    private $total = 0;
    private $interval = 0;
    private $response;

    /**
     * Initializes plugin variables and sets up WordPress hooks/actions.
     *
     * @return void
     */

    protected function __construct()
    {
        // increase the max execution time
        set_time_limit(900);
        ini_set('max_execution_time', 1800);
        ini_set('memory_limit', '4096M');

        // Set XML URL
        if(empty($this->xml_url))
            $this->xml_url = get_option('cat_used_feed_url');

        // Import Hooks
        add_action('wp_ajax_cat_used_import', array($this, 'import'));
        add_action('wp_ajax_nopriv_cat_used_import', array($this, 'import'));
        add_action('cat_used_cron_import', array($this, 'import'));
    }



    /**
     * Static Singleton Factory Method
     *
     * @return [class] instance of self
     */

    public static function instance()
    {
        if (!isset(self::$instance)) {
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }



    /**
     * Internal method to get an XML element from a given url core
     */

    private function get_xml_element($url)
    {
        Progress::set(0, 1, 'Retrieving XML');

            libxml_use_internal_errors(true);
            $xml = false;
            try {
              //The auth is only used when calling this function which is only used in the new feeds.
              $response = wp_remote_request(
                $url,
                [
                    'timeout'     => 160,
                  'method'  => 'GET',
                ]
              );
        
              $raw      = wp_remote_retrieve_body( $response );
                
              $xml      = simplexml_load_string($raw);

            } catch ( Exception $e ) {
              return new WP_Error( 'XML Failed', __( $e ) );
            }
            if ( ! $xml ) {
              return new WP_Error( 'XML Failed', __( 'Unable to load XML file - there may be a syntax error' ) );
            }
        
            return $xml;
            
            Progress::set(1, 1, 'XML Recieved');
        

        return $xml;
    }

    /**
     * Method to process the overall feed
     */

    public function import()
    {
        // increase the max execution time
         set_time_limit(900);
        ini_set('max_execution_time', 1800);
        ini_set('memory_limit', '4096M');
        @wp_mail(
                 array( get_option('admin_email')),
		'Cat Used Equipment Updated',
                "import started and is underway"
            );
        
        // set transient to use for deleting
        //set_transient( 'current_used_import_time', gmdate("Y-m-d H:i:s"), 3600);
        $this->import_start_time = gmdate("Y-m-d H:i:s");

        $xml = $this->get_xml_element($this->xml_url);
        //var_dump($xml);die();
        $this->total = count($xml->equipment);
        $this->interval = 1;

		// if total less 1 then we exit early
		if( $this->total === 0 ){
		    
			Progress::set(0, 0, "There isn't any equipment to import");
			return; 
		}

        // set the initial Progress for equipment import
        Progress::set(0, $this->total, 'Importing Equipment');
        
        do_action( 'cat_before_used_feed_import', $xml, $this->total );

        // check to make sure we have equipment
        if (isset($xml->equipment))
        {
            foreach ($xml->equipment as $item)
            {
                $total = count( $xml->equipment );
                $skip = false;
                $attrs = $item->attributes();

                foreach($attrs as $k => $v)
                {
                    $key = trim($k);
                    $val = trim($v);
                    if( $key == 'country' && $val != 'US' && $val != 'us' ) {
                        $skip = true;
                    }
                }

                if( ! $skip ) {
                    // create a new equipment model
                    $product               = new DSF_Product($item);
                    $product->families     = DSF_Family::create_from_xml($item);
                    $product->manufacturer = DSF_Manufacturer::create_from_xml($item);

                    // save the data
                    $product->save();
                }
                // update import progress interval
                Progress::update('index', $this->interval);
                
                $this->interval++;
            }
        }

        $this->remove_old_items();
		global $wpseo_sitemaps;
		try {
			$wpseo_sitemaps->cache->clear(); //Clear sitemap cache
			$wpseo_sitemaps->build_root_map(); //Rebuild sitemap
		}
		catch(Exception $exc) {
			//do nothing
		}
        $this->email_admins();
        do_action( 'fx_after_dsf_import' );
        do_action( 'cat_after_used_feed_import', $xml, $this->total  );
    }




    /**
     * Remove any old equipment
     * @return void
     */
    private function remove_old_items()
    {
        global $wpdb;

        $imported = $wpdb->get_results(
            "SELECT p.ID
             FROM {$wpdb->posts} p
             LEFT JOIN {$wpdb->postmeta} m ON p.ID = m.post_id
             WHERE post_type = 'cat_used_machine'
                   AND post_modified_gmt < '$this->import_start_time'
                   AND m.meta_key = 'is_feed'
        ");


        if($imported)
        {
            Progress::set(0, count($imported), 'Deleting Old Equipment');

            $index = 1;
            // loop through and delete those items
            foreach($imported as $i)
            {
                // delete the post
                wp_delete_post( $i->ID, 1 );

                // delete meta
                $wpdb->get_results( "DELETE FROM {$wpdb->postmeta} WHERE post_id = {$i->ID} ");

                Indexer::delete_all($i->ID);

                // Update progress
                Progress::update('index', $index);
                $index++;
            }
        }

        Progress::set(0, 0, 'Finished');
    }


    /**
     * Email admin that used equipment was updated
     * @return void
     */
    private function email_admins()
    {
       /* if( defined('DOING_CRON')
            AND DOING_CRON
            AND get_option('cat_used_email_update', false )
        ){ */
            $message  = __('Updated used equipment: this message indicates the importer reached the end of the process successfully') . "\r\n\r\n";

            @wp_mail(
                 array('amanda@webfx.com', get_option('admin_email')),
		'Cat Used Equipment Updated',
                $message
            );
        //}
    }
}


Used_Feed::instance();
