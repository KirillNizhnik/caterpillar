<?php
namespace Cat\Controllers\Importers;
use \Exception;
use \WP_Error;
use \SimpleXMLElement;
use \SoapClient;
use \SoapHeader;
use \SoapVar;
use \Cat\Controllers\Progress;
use \Cat\Models\Rental_Product;

/**
 * CNF_Importer
 * handles the start of the import process delimiting
 * tasks to proper import
 *
 * @Package CAT Rental Feed
 * @category Core
 * @author WebpageFX
 */

if ( ! defined('ABSPATH') )
	exit;


class Rental_Feed extends \Cat\Core\Abstracts\Importer
{
	protected static $instance;

    protected $lang = 'en';
    protected $region = 'R760';

    protected $api_environment = '';
    protected $api_cpc_url = '';
    protected $api_url = '';
    protected $api_user = '';
    protected $api_password = '';

    protected $_client = null;

    protected $cpc_product_map = null;
    protected $cpc_family_map = null;
    protected $new_feed = null;

    // Update List Data
    protected $update_num_products = 0;
    protected $delete_num_products = 0;
	protected $total_num_products = 0;
    protected $more_available = false;
    protected $last_update_date = "";
    protected $products = array();

	/**
	 * Initializes variables and sets up WordPress hooks/actions.
	 *
	 * @return void
	 */

	protected function __construct( )
	{

		$this->configure();

		libxml_use_internal_errors(true);

		add_action('wp_ajax_cat_rental_import', array($this, 'import'));
		add_action('wp_ajax_nopriv_cat_rental_import', array($this, 'import'));
		add_action('cat_rental_cron_import', array($this, 'import'));

        // Purge
		add_action('wp_ajax_cat_rental_purge', array($this, 'purge'));
		add_action('wp_ajax_nopriv_cat_rental_purge', array($this, 'purge'));

	}



	/* Static Singleton Factory Method */
	public static function instance()
	{
		if (!isset(self::$instance)) {
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}


	/**
	 * Grabs config options from settings page
	 *
	 * @return void
	 */

	protected function configure()
	{
        $this->api_environment = get_option('cat_rental_environment', '');
        if (!empty($this->api_environment))
        {
            $default_cpc_url = empty(CAT()->default_urls['rental_cpc'][$this->api_environment]) ? "" : CAT()->default_urls['rental_cpc'][$this->api_environment];
            $this->api_cpc_url = trailingslashit($default_cpc_url);
            $this->api_user = get_option('cat_rental_' . $this->api_environment . '_user', '');
            $this->api_password = get_option('cat_rental_' . $this->api_environment . '_password', '');
        }
	}

    /**
     * Get instance of soap client
     *  - Create if none exists
     * @return SoapClient
     */
    protected function client()
    {
        if (is_null($this->_client))
        {
            try {

                // Initializing namespaces
                $ns_wsse = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
                $ns_wsu = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd';
                $password_type = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText';

                // Creating WSS identification header using SimpleXML
                $root = new SimpleXMLElement('<root/>');

                $security = $root->addChild('wsse:Security', null, $ns_wsse);

                $usernameToken = $security->addChild('wsse:UsernameToken');
                $usernameToken->addChild('wsse:Username', $this->api_user, $ns_wsse);
                $usernameToken->addChild('wsse:Password', $this->api_password, $ns_wsse)->addAttribute('Type', $password_type);

                // Recovering XML value from that object
                $root->registerXPathNamespace('wsse', $ns_wsse);
                $full = $root->xpath('/root/wsse:Security');
                $auth = $full[0]->asXML();

                $header = new SoapHeader($ns_wsse, 'Security', new SoapVar($auth, XSD_ANYXML), true);

                $this->_client = new SoapClient(
                    CAT()->plugin_path . 'assets/wsdl/catrental_'.$this->api_environment.'.wsdl',
                    array(
                        'login' => $this->api_user,
                        'password' => $this->api_password,
                        'trace' => 1
                    )
                );
                $this->_client->__setSoapHeaders($header);
            } catch (Exception $e) {
                echo "Error\n";
                die($e->getMessage());
            }

        }

        return $this->_client;
    }

	/**
	 * Starts the import process
	 *
	 * @hook   wp_ajax_cnf_import_class
	 * @param  string $url the url to get the xml from
	 * @return simple_xml_element xml tree from the url
	 */

	public function import()
	{
		ini_set('memory_limit', '512M');
		ini_set('max_execution_time', 1800);

		Progress::set(0, 1, 'Fetching list of product updates');

		if(empty($this->api_environment))
			return new WP_Error('Rental Import Failed', __('Rental equipment feed is disabled in settings'));

		if(empty($this->api_user) or empty($this->api_password))
			return new WP_Error('Rental Import Failed', __('Insufficient connection information - check URL,User,Password'));

		// set transient to use for deleting
		set_transient( 'current_rental_import_time', gmdate("Y-m-d H:i:s"), 3600);

        $updates = false;

        if ($this->cache_api())
            $updates = get_transient('cat_rental_updates');

        if (empty($updates))
        {
            // Get updates since last check
            $updates = $this->client()->getRentalUpdates(array('input' => array('lang' => $this->lang, 'region' => $this->region)));
        }

        if ($this->cache_api())
            set_transient('cat_rental_updates', $updates, DAY_IN_SECONDS);

        $this->parseUpdates($updates);

        // Parsing updates will have given us an array of products to be processed:
        $this->updateProducts();

        foreach (CAT()->available_classes as $class_id => $class)
            $this->remove_old_items($class_id);

		global $wpseo_sitemaps;
		try {
			$wpseo_sitemaps->cache->clear(); //Clear sitemap cache
			$wpseo_sitemaps->build_root_map(); //Rebuild sitemap
		}
		catch(Exception $exc) {
			//do nothing
		}
			
		exit;
	}

    /**
     * Import a product from CPC if needed
     *  - namely for products not already in dealer version of CPC
     */
    public function import_cpc_product($product_id)
    {
        $cpc_product_map = $this->get_cpc_product_map();
        $cpc_family_map = $this->get_cpc_family_map();
        $product = empty($cpc_product_map[$product_id]) ? false : $cpc_product_map[$product_id];

        if(empty($product))
            return false;

        $new_feed = $this->get_new_feed();

        // Import Family and Subfamily if not already done
        foreach (array('family_id', 'subfamily_id') as $key)
        {
            $family_id = empty($product[$key]) ? false : $product[$key];
            if (
                !empty($family_id)
                and empty($new_feed->family_map[$family_id])
                and !empty($cpc_family_map[$family_id])
            ){
                $family = $cpc_family_map[$family_id];
                $new_feed->family($family, $family['class_id'], $family['post_type'], true);
            }
        }

        // Import Product
        $post_id = $new_feed->product($product, $product['post_type'], true);

        return $post_id;
    }

    /**
     * Get cpc family map - id => data
     *  - from class trees
     */
    public function get_cpc_family_map()
    {
        if (is_null($this->cpc_family_map))
        {
            $this->get_cpc_product_map();
        }

        return $this->cpc_family_map;
    }

    /**
     * Get a product map - id => data
     *  - from class trees
     */
    protected function get_cpc_product_map()
    {
        if (is_null($this->cpc_product_map))
        {
            $this->cpc_family_map = array();
            $this->cpc_product_map = array();

            $class_ids = array_keys(CAT()->available_classes);
            $new_feed = $this->get_new_feed();

            foreach ($class_ids as $class_id)
            {
                $tree = $new_feed->class_tree($class_id, false);
                foreach ($tree->families as $family)
                {
                    $id = $family['id'];
                    $family['class_id'] = $tree->ID;
                    $family['post_type'] = $tree->post_type;
                    $this->cpc_family_map[$id] = $family;
                }
                foreach ($tree->products as $product)
                {
                    $id = $product['id'];
                    $product['class_id'] = $tree->ID;
                    $product['post_type'] = $tree->post_type;
                    $this->cpc_product_map[$id] = $product;
                }
            }
        }

        return $this->cpc_product_map;
    }

    /**
     * Get instance of new feed based on config
     */
    protected function get_new_feed()
    {
        if (is_null($this->new_feed))
        {
            $class_ids = array_keys(CAT()->available_classes);
            $this->new_feed = New_Feed::_new($this->api_cpc_url, $this->region, $class_ids);
        }
        return $this->new_feed;
    }

    /**
     * Get the data we are interested in and populate class parameters
     *
     */
    protected function parseUpdates($updates)
    {
        $output = empty($updates->output) ? array() : $updates->output;
        $this->update_num_products = empty($output->update_count) ? 0 : $output->update_count;
        $this->delete_num_products = empty($output->delete_count) ? 0 : $output->delete_count;
        $this->total_num_products = empty($output->total) ? 0 : $output->total;
        $this->more_available = !empty($output->more);
        $this->last_update_date = empty($output->last_update_date) ? "" : $output->last_update_date;
        $this->products = (empty($output->rentalList) or empty($output->rentalList->rentalUpdate)) ? array() : $output->rentalList->rentalUpdate;
    }

    /**
     * Loop through all products
     *  - If "A" - Active, locate new product, update rental terms & metas
     *  - If "D" - Deleted, locate new product, remove rental terms & metas
     */
	public function updateProducts()
	{
		$counter = 0;
		Progress::set(0, $this->total_num_products, 'Processing Products');

        $successes = array();
        $failurs = array();

		foreach($this->products as $product_data)
		{

            $dealer_code = $product_data->dealer_code;
            $product_id = $product_data->product_id;
            $action = strtolower($product_data->action);

            if ($action == 'a')
            {
                $result = false;
                if ($this->cache_api())
                    $result = get_transient('cat_rental_detail_'.$product_id);

                if (empty($result))
                {
                    $result = $this->client()->getRentalDetails(array('input' => array(
                        'lang' => $this->lang,
                        'region' => $this->region,
                        'dealer_code' => $dealer_code,
                        'product_id' => $product_id
                    )));
                }

                if ($this->cache_api())
                    set_transient('cat_rental_detail_'.$product_id, $result, DAY_IN_SECONDS);

                $output = empty($result->output) ? array() : $result->output;

                $product = new Rental_Product($product_id, $output, $this);
                $result = $product->process()
                          ->save();

                if (empty($result))
                {
                    $failures[]= $product_id;
                }
                else
                {
                    $post_id = $product->get_post_id();
                    $successes[$product_id]= $post_id;
                }
            }
            /*
            elseif ($action == 'd')
            {
                CAT_Log(' - Marked as Deleted');
                $product = new Rental_Product($product_id);
                $product->removeRentalData();
            }
             */

			$counter++;
			Progress::update('index', $counter);
		}

        CAT_Log('Rental Products Updated:');
        CAT_Log($successes);
        CAT_Log('Failed:');
        CAT_Log($failures);

		do_action( 'cat_products_processed', $this);
	}

    /**
     * Purge all rental data
     */
    public function purge()
    {
        // set transient to use for deleting
        set_transient( 'current_rental_import_time', gmdate("Y-m-d H:i:s"), 3600);
        $available_classes = CAT()->available_classes;
        foreach ($available_classes as $class_id => $name)
            $this->remove_old_items($class_id);

        // Delete all rental families
        $post_types = CAT()->get_class_post_type_relation();
        foreach ($post_types as $class_id => $post_type)
        {
            $taxonomy = $post_type . "_rental_family";
            $terms = get_terms($taxonomy, array(
                'hide_empty' => false
                ,'fields' => 'ids'
            ));
			Progress::set(0, count($terms), 'Deleting Rental Families - Class ' . $class_id);
            $index = 1;
            foreach ($terms as $term)
            {
                $success = wp_delete_term($term, $taxonomy);
                CAT_Log(($success ? "Deleted" : "Failed to Delete") . " Term - " . $term);
				// Update progress
				Progress::update('index', $index);
				$index++;
            }
        }

		Progress::set(0, 0, 'Finished');
    }

	public function remove_old_items($class_id)
	{
		global $wpdb;
		$post_type = CAT()->get_class_post_type_relation($class_id);
		$last = get_transient('current_rental_import_time');
        $sql = "
            SELECT p.ID,
                f.meta_value as is_new,
                r.meta_value as is_rental
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} f ON (p.ID = f.post_id AND f.meta_key = 'is_feed')
            LEFT JOIN {$wpdb->postmeta} r ON (p.ID = r.post_id AND r.meta_key = 'is_rental_feed')
            WHERE post_type = '$post_type'
                AND post_modified_gmt < '$last'
                AND r.meta_value = '1'
        ";

		$imported = $wpdb->get_results($sql);

		if($imported)
		{
			Progress::set(0, count($imported), 'Deleting Old Rental Equipment - Class ' . $class_id);

			$index = 1;
			// loop through and delete those items
			foreach($imported as $i)
			{
                CAT_Log('Removing post ID ' . $i->ID);

				// delete the post if only rental
                if ($i->is_rental and !$i->is_new)
                {
                    CAT_Log(' - deleting post entire');
                    wp_delete_post( $i->ID, 1 );
                    Indexer::delete_all($i->ID);
                }
                else
                {// remove the post_meta and terms
                    CAT_Log(' - removing rental data from post');

                    delete_post_meta($i->ID, 'is_rental_feed');
                    delete_post_meta($i->ID, 'rental_rates');

                    wp_set_object_terms($i->ID, array(), $post_type . "_rental_family");
                }

				// Update progress
				Progress::update('index', $index);
				$index++;
			}
		}

		Progress::set(0, 0, 'Finished');
	}

}

Rental_Feed::instance();
