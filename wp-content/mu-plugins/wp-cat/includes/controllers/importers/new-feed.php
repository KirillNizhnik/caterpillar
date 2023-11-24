<?php

namespace Cat\Controllers\Importers;

use \WP_Error;
use \Cat\Controllers\Progress;
use \Cat\Controllers\Indexer;
use \Cat\Models\CPC_Class;
use \Cat\Models\CPC_Family;
use \Cat\Models\CPC_Product;
use \Cat\Controllers\Importers\Mappers\NewEquipmentFamilyMapper;

/**
 * CNF_Importer
 * handles the start of the import process delimiting
 * tasks to proper import
 *
 * @Package CAT New Feed
 * @category Core
 * @author WebpageFX
 */

if (!defined('ABSPATH'))
    exit;


class New_Feed extends \Cat\Core\Abstracts\Importer
{
    protected static $instance;

    private $xml_base_url = 'https://cpc.cat.com/api/v2/xml/';
    private $lang_code = 'en';
    private $class_ids = array();
    private $family_exclusion = array();
    private $total_num_families = 0;
    private $total_num_products = 0;

    public $dealer_code = '';
    public $classes = array();
    public $families = array();
    public $family_map = array();
    public $product_map = array();
    private $group_id;


    /**
     * Initializes variables and sets up WordPress hooks/actions.
     *
     * @return void
     */

    protected function __construct($xml_base_url = null, $dealer_code = null, $class_ids = null)
    {
        if (!is_null($xml_base_url))
            $this->xml_base_url = $xml_base_url;
        if (!is_null($dealer_code))
            $this->dealer_code = $dealer_code;
        if (!is_null($class_ids))
            $this->class_ids = $class_ids;

        libxml_use_internal_errors(true);

        add_action('wp_ajax_cat_new_import_class', array($this, 'import'));
        add_action('wp_ajax_nopriv_cat_new_import_class', array($this, 'import'));
        add_action('cat_new_cron_import', array($this, 'import'));
    }

    /**
     * Get a non-singleton instance
     */
    public static function _new($xml_base_url = null, $dealer_code = null, $class_ids = null)
    {
        return new self($xml_base_url, $dealer_code, $class_ids);
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

    private function configure()
    {
        if ($dealer_code = get_option('cat_new_sales_channel_code')) {
            $this->dealer_code = $dealer_code;
        }

        if ($classes = get_option('cat_new_class_limitation')) {
            $this->class_ids = $classes;
        }
    }


    /**
     * Starts the import process
     *
     * @hook   wp_ajax_cnf_import_class
     * @param string $url the url to get the xml from
     * @return simple_xml_element xml tree from the url
     */

    public function import($class_id = "")
    {

        ini_set('memory_limit', '500M');
        ini_set('max_execution_time', 2400);

        $this->configure();

        if (empty($this->dealer_code))
            return new WP_Error('Import Failed', __('Dealer sales channel is not configured.'));

        if (isset($_POST['class_id']))
            $class_id = $_POST['class_id'];
        $this->group_id = $class_id;
        // set transient to use for deleting
        set_transient('current_new_import_time', gmdate("Y-m-d H:i:s"), 3600);

        do_action('cat_before_new_class_import', $class_id);


        $class = $this->class_tree($class_id);
        $this->products($class);

        $class = $this->class_tree($class_id, true, true);
        $this->products($class, true, true);


        $this->remove_old_items($class_id);

        $this->email_admins($class_id);

        do_action('cat_after_new_class_import', $class_id);
        if (!CAT()->is_cli()) {
            exit; //prevent exit on cli imports for class iteration.
        }
    }


    /**
     *  Gets the xml tree for single class
     *
     * @param int $class_id the id for the class we are getting xml tree for
     * @return CPCClass $class the processed class object
     */

    public function class_tree($class_id, $progress = true, $noncurrent = false)
    {
        // Setup temporary cache data
        if ($progress)
            Progress::set(0, 1, 'Processing ' . $class_id . ' class tree');

        $nc = $noncurrent ? '_nc' : '';
        $url = $this->xml_base_url . $this->dealer_code . '/' . $class_id . 'tree_' . $this->lang_code . $nc . '.xml';
        $xml = $this->get_xml($url);

        if (is_wp_error($xml))
            die("<pre>" . print_r($xml, true) . "</pre>");

        $class = new CPC_Class($xml->product_group);

        $class->ID = $class_id;
        $class->post_type = CAT()->get_class_post_type_relation($class_id);

        // Lets log these puppies for easy debugging
        //if ( constant('CAT_PLUGIN_LOG') == true ) {
        //$log_text = $url;
        //error_log( $log_text . PHP_EOL, 3, plugin_dir_path( CAT_PLUGIN_FILE ) . 'all-urls.log' );
        // error_log( $log_text . PHP_EOL, 3, plugin_dir_path( CAT_PLUGIN_FILE ) . 'classtree.log' );
        //}

        do_action('cat_class_' . $class_id . '_processed', $class);
        return $class;
    }


    public function families($class)
    {
        //        TODO: remove this method after
//        $last_tax = '';
//        $counter = 1;
//        Progress::set(0, count($class->families), 'Processing Families');
//
//        foreach ($class->families as $f) {
//            $this->family_map[$f['id']] = $this->family($f, $class->ID);
//
//            Progress::update('index', $counter);
//            $counter++;
//        }
//
//        // Large imports freak out WP's children option on the taxonomies
//        // so after all the taxes are imported we delete the current option
//        // that is saved so the listing will display properly
//        delete_option($class->post_type . '_family_children');
//
//        // fire action letting the wordpress system know that families have been processed
//        do_action('cat_families_processed', $this);
    }

    public function family($f, $class_id)
    {
//        TODO: remove this method after
//        $url = $this->xml_base_url.$this->lang_code.'/'.$f['id'].'/'.$f['id'].'_'.$this->lang_code.'.xml';
//        $xml = $this->get_xml($url);
//       if(is_wp_error( $xml ))
//            die("<pre>".print_r($xml,true)."</pre>");

//        $family = new CPC_Family();
//        $family->is_rental = $rental;
//        $prefix = $rental ? "_rental" : "";
//        $family->xml      = $xml;
//        $family->name = $f['name'];
//        $family->class = $class_id;
//        $family->taxonomy = $post_type.$prefix.'_family';
//        $family->parent = ($f['parent']) ? $f['parent']['name'] : false;
//        return $family;
//        $family->create_term()
//               ->process()
//               ->save();


        // Lets log these puppies for easy debugging
        // if ( constant('CAT_PLUGIN_LOG') == true ) {
        // $log_text = $family->name . ' | ' . $url;
        // error_log( $log_text . PHP_EOL, 3, plugin_dir_path( CAT_PLUGIN_FILE ) . 'families.log' );
        // error_log( $log_text . PHP_EOL, 3, plugin_dir_path( CAT_PLUGIN_FILE ) . 'all-urls.log' );
        //}

//        $this->family_map[$f['id']] = $family;
    }


    public function products($class, $noncurrent = false)
    {
        $counter = 1;
        Progress::set(0, count($class->products), 'Processing Products');

        foreach ($class->products as $p) {

            $this->product($p, false, $noncurrent);
            Progress::update('index', $counter);

            $counter++;
        }
        do_action('cat_products_processed', $this);
    }

    public function product($p, $rental = false, $noncurrent = false)
    {
        $url = $this->xml_base_url . $this->lang_code . '/' . $p['family_id'] . '/' . $p['id'] . '_' . $this->lang_code . '.xml';
        $xml = $this->get_xml($url);
        if (is_wp_error($xml))
            die("<pre>" . print_r($xml, true) . "</pre>");

        $product = new CPC_Product();

        $product->is_rental = $rental;
        $product->is_noncurrent = $noncurrent;
        $product->xml = $xml;
        $product->post_type = 'equipment';

        $product->family = $p['sub_family_name'] ?? '';
        $product->family_id = $p['sub_family_id'] ?? '';
        $product->subfamily = $p['family_name'];
        $product->subfamily_id = $p['family_id'];
        $product->group_id = $this->group_id;
        $product->process()
            ->save();

        return $product->post_id;
    }


    public function remove_old_items($class_id)
    {
        global $wpdb;
        $post_type = CAT()->get_class_post_type_relation($class_id);
        $last = get_transient('current_new_import_time');
        $imported = $wpdb->get_results(
            "SELECT p.ID
           FROM {$wpdb->posts} p
           LEFT JOIN {$wpdb->postmeta} m1 ON p.ID = m1.post_id
           LEFT JOIN {$wpdb->postmeta} m2 ON p.ID = m2.post_id
           LEFT JOIN {$wpdb->postmeta} m3 ON p.ID = m3.post_id
           WHERE post_type = 'equipment'
               AND post_modified_gmt < '$last'
               AND m1.meta_key = 'status'
                AND m1.meta_value = 'available'
                AND m2.meta_key = 'group_id'
                AND m2.meta_value = $this->group_id
                AND m3.meta_key = 'is_feed'
                AND m3.meta_value = '1'
                
        ");


        if ($imported) {
            Progress::set(0, count($imported), 'Deleting Old Equipment');

            $index = 1;
            // loop through and delete those items
            foreach ($imported as $i) {


//                $post = get_post($i->ID, 'equipment');
//                $meta_value = get_post_meta($i->ID, 'status', true);
                $fam = wp_get_object_terms($i->ID, 'family');
                $fam_id = [];
                foreach ($fam as $item) {
                    $fam_id[] = $item->term_id;
                }
                update_post_meta($i->ID, 'array_fam', $fam_id);
                wp_set_post_terms($i->ID, [], 'family');
                update_post_meta($i->ID, 'status', 'hidden');


//                $prod = CAT()->product($i->ID);
//                $rentable = $prod->is_rentable();
//
//                // This ensures products marked as rental aren't removed if they
//                // disappear from the CAT feed temporarily
//                if ($rentable) {
//                    continue;
//                }
//                // delete the post
//                wp_delete_post($i->ID, 1);

                // Remove indexes for this post id
//                Indexer::delete_all($i->ID);

                // Update progress
                Progress::update('index', $index);
                $index++;
            }
        }

        Progress::set(null, null, 'Finished');
    }


    private function email_admins($class)
    {
        //if(defined('DOING_CRON') AND get_option('cat_new_email_update', false ))
        //	{
        $message = sprintf(__('Updated new feed class %s:'), $class) . "\r\n\r\n";

        @wp_mail(
            get_option('admin_email'),
            'Cat New Feed Updated',
            $message
        );
        //}
    }
}

New_Feed::instance();
