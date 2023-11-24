<?php
namespace Cat\Models;
use \Cat\Controllers\Indexer;

/**
 * Rental Product
 * Imports custom data for rental products.
 *
 * @Package CAT New Feed/Importer
 * @category importer
 * @author WebpageFX
 */

if ( ! defined('ABSPATH') )
	exit;


class Rental_Product
{
    public $raw_data;
	public $equipment_id;
    public $post_id;

    public $post_meta = array(
        'is_rental_feed' => 1
    );

    protected $term_metas = array("name", "parent", "class", "family_id", "short_description", "long_description", "links", "features", "images", "videos", "spec_priority");

    protected $rental_feed = null;

	public function __construct($equipment_id, $raw_data = null, $rental_feed = null)
    {
        $this->equipment_id = $equipment_id;
        $this->raw_data = $raw_data;
        $this->rental_feed = $rental_feed;
    }

    public function get_post_id()
    {/*{{{*/
        if (empty($this->post_id) and !empty($this->equipment_id))
        {
            global $wpdb;

            $post_id = $wpdb->get_var("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='equipment_id' AND meta_value ='".$this->equipment_id."'");

            if($post_id) {
                $this->post_id = $post_id;
            }
        }

        return $this->post_id;
    }/*}}}*/


    public function save()
    {/*{{{*/

        // Only update vs creating products with missing info
        // - ie. New import needs to populate product first
        $post_id = $this->get_post_id();

        // Import if available
        $from_cpc = false;
        if (empty($post_id) and !is_null($this->rental_feed))
        {
            $from_cpc = true;
            $post_id = $this->rental_feed->import_cpc_product($this->equipment_id);
        }

        if (empty($post_id))
        {
            return false;
        }

        // fix bug with cron
        // https://core.trac.wordpress.org/ticket/19373
        if(defined('DOING_CRON')) {
            wp_set_current_user(1);
        }

        // Just to update the date for removal
        wp_update_post(array('ID' => $post_id));

        $this->propagate_taxonomy();

        $this->insert_post_meta();

        return $this;
    }/*}}}*/

    /**
     * Get post terms from "new" taxonomy
     * and copy to "rental" taxonomy
     */
    private function propagate_taxonomy()
    {
        $post_id = $this->get_post_id();
        $post_type = get_post_type($post_id);

        // Get new family terms
        $terms = wp_get_post_terms($post_id, $post_type."_family");

        if (!is_array($terms))
            return $this;

        // map by parent
        $term_map = array();
        foreach ($terms as $term)
        {
            $parent = $term->parent;

            if (!isset($term_map[$parent]))
            {
                $term_map[$parent] = array();
            }

            $term_map[$parent][] = $term;
        }

        $new_terms = $this->save_term_map($term_map);
        if (!empty($new_terms))
            wp_set_object_terms( $post_id, $new_terms, $post_type."_rental_family");
        
        return $this;
    }

    /**
     * Recursive method to save terms to rental taxonomy
     */
    function save_term_map($term_map, $old_parent=0, $new_parent=0, $new_terms=array())
    {
        $terms = empty($term_map[$old_parent]) ? array() : $term_map[$old_parent];

        foreach ($terms as $term)
        {
            $name = $term->name;
            $old_id = $term->term_id;
            $taxonomy = str_replace("_family", "_rental_family", $term->taxonomy);

            // if term doesn't exist, create it
            // either way, set $new_id
            if ( ! $result = term_exists($name, $taxonomy, $new_parent))
            {
                $result = wp_insert_term($name, $taxonomy, array(
                    'parent' => $new_parent
                ));
            }

            if (is_array($result) and !empty($result['term_id']))
                $new_id = (int) $result['term_id'];

            if (empty($new_id))
            {
                echo "error adding term:\n";
                echo "$name\n";
                echo "$taxonomy\n";
                echo "$new_parent\n";
                die;
            }
            else
            {
                // Save term metadata
                $old_metas = get_cat_term_meta($old_id);
                foreach ($this->term_metas as $key)
                {
                    if (isset($old_metas[$key]))
                    {
                        $value = maybe_unserialize($old_metas[$key][0]);

                        add_cat_term_meta($new_id, $key, $value, true)
                            || update_cat_term_meta($new_id, $key, $value);
                    }
                }

                $new_terms[]= $new_id;
            }

            // Recurse to children (if any)
            $new_child_terms = $this->save_term_map($term_map, $old_id, $new_id);
            $new_terms = array_merge($new_terms, $new_child_terms);
        }

        return $new_terms;
    }

    /**
     * Save the post metadata to the database
     *
     * @param [int] $id The ID of the post we need to save our post meta for
     * @return void
     */

    private function insert_post_meta()
    {/*{{{*/
        $post_id = $this->get_post_id();

        foreach($this->post_meta as $k => $v)
        {
            add_post_meta($post_id, $k, $v, true) || update_post_meta($post_id, $k, $v);
        }

        return $this;
    }/*}}}*/

    /**
     * Base processor of the the xml
     *
     * @return null
     */

    public function process()
    {
        $output = $this->raw_data;
        if (!is_object($output)) return false;

        // Error checking - in case something is badly wrong with feed
        if (empty($output->product_id) or $output->product_id != $this->equipment_id)
            throw new Exception("Product ID mismatch in API data. Update says " . $output->product_id . ", Detail says " . $this->equipment_id);

        if (!empty($output->rateList) and !empty($output->rateList->rate))
        {
            $rates = array();
            foreach ($output->rateList->rate as $rate)
            {
                // Get period to use as key, to prevent duplicate rates
                $period = $rate->period;

                // convert from objects to arrays
                $rates[$period]= (array) $rate;
            }
            $this->post_meta['rental_rates'] = $rates;
        }

        return $this;
    }

}// End of File
