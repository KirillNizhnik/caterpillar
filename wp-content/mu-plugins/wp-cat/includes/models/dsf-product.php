<?php

namespace Cat\Models;

use Cat\Controllers\Importers\Mappers\FamilyAssignerByRule;
use Cat\Controllers\Importers\Mappers\UsedEquipmentFamilyMapper;
use \Cat\Controllers\Indexer;


class DSF_Product
{
    public $equipment_id;
    public $post_id;
    public $created;
    public $updated;
    public $families;
    public $manufacturer;
    public $family;
    public $contact = array();
    public $features = array();
    public $comments = '';
    public $condition = array();
    public $images = array();
    public string $modelForMapper;
    public string $familyForMapper;
    public string $taxonomy = 'used-family';

    public function __construct($xml = '')
    {
        if (!empty($xml))
            $this->process_from_xml($xml);
    }


    /**
     * returns all the properties of our model
     *
     * @return array
     */

    public function properties()
    {
        return get_object_vars($this);
    }


    public static function find($id)
    {
        global $wpdb;

        $item = new self();
        $item->id = $id;
        $exists = $wpdb->get_row("SELECT post_id FROM {$wpdb->postmeta} WHERE equipment_id='$id'");

        if ($exists) {
            $item->post_id = $exists->post_id;
        }

        return $item;
    }


    public function get_post_id($eid)
    {
        global $wpdb;

        $exists = $wpdb->get_row("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='equipment_id' AND meta_value = $eid");

        if ($exists) {
            return $exists->post_id;
        }

        return false;
    }


    public function process_from_xml($xml)
    {
        // grab attributes
        $attrs = $xml->attributes();
        $skip_fields = array('product-family', 'product-family-code', 'manufacturer-code', 'manufacturer', 'dealer-id', 'dealer-name');

        // loop through attributes and set our model values
        foreach ($attrs as $k => $v) {
            $key = trim($k);

            // skip some things that are unneed or setup with taxonomies
            if (in_array($key, $skip_fields)) continue;

            switch ($key) {
                case 'id':
                    $this->equipment_id = trim((string)$v);
                    break;
                default:
                    $this->{str_replace('-', '_', $k)} = trim((string)$v);
                    break;
            }
        }
        if (isset($xml->price))
            $this->price = trim($xml->price);

        if (isset($xml->contact))
            $this->contact = $this->process_contact($xml->contact);

        if (isset($xml->features))
            $this->features = $this->process_features($xml->features);

        if (isset($xml->condition))
            $this->condition = $this->process_conditions($xml->condition);

        if (isset($xml->comments))
            $this->comments = wpautop((string)$xml->comments);

        if (isset($xml->photos))
            $this->images = $this->process_images($xml->photos);

        // gets related post_id
        if (empty($this->post_id)) {
            $this->post_id = $this->get_post_id($this->equipment_id);
        }
    }


    /**
     * Save the post to the database
     * handles new & update
     *
     * @return $id INT, the ID of the saved post
     */

    public function save()
    {
        global $wpdb;

        $post = array();
        $current_user_id = 1;
        $current_user = defined('DOING_CRON') && DOING_CRON ? get_user_by('email', get_option('admin_email')) : wp_get_current_user();
        $update = false;

        if (false !== $current_user && $current_user->ID > 0) {
            $current_user_id = $current_user->ID;
        }

        if (defined('DOING_CRON') && DOING_CRON) {
            wp_set_current_user($current_user_id);
        }

        // if the product was previously in the database
        // then we want to update the post not create a new one.
        if ($this->post_id) {
            $post['ID'] = $this->post_id;
            $update = true;
        }

        // Setup the post information for inserting
        $year = $this->year ?? '';
        $post['post_title'] = ucwords(strtolower(trim($year . ' ' . $this->manufacturer->name)) . ' ' . $this->model);
        $post['post_type'] = 'used-equipment';
        $post['post_status'] = 'publish';
        $post['post_author'] = $current_user_id;
        $post['tax_input'] = array();

//        // setup taxonomies
//        foreach($this->families as $family) {
//            $post['tax_input'][$family->taxonomy][] = $family->term_id;
//        }

//        if(isset($this->manufacturer)){
//            $post['tax_input'][$this->manufacturer->taxonomy] = array($this->manufacturer->term_id);
//        }

        // save


        $assigner = null;
        if ($this->manufacturer->manufacturer_code === 'CAT' || $this->manufacturer->manufacturer_code === 'CATERPILLAR') {
            $assigner = new FamilyAssignerByRule($this->modelForMapper, 'used_family_custom_textarea', 'used-family', false, $this->familyForMapper);
        }

        $equipmentFamilyMapper = new UsedEquipmentFamilyMapper($this->familyForMapper, $assigner, 'used-family');
        $families = $equipmentFamilyMapper->applyFamily();
        if ($families === false) {
            $post['post_status'] = 'draft';
        }
        if ($post_id = wp_insert_post($post)) {


//            $family_ids = array();

//            foreach($post['tax_input'][$family->taxonomy] as $family) {
//                $family_ids[] = intval($family);
//            }


            if (is_array($families)) {
                $this->families = get_terms([
                    'taxonomy' => 'used-family',
                    'hide_empty' => false,
                    'term_taxonomy_id' => $families
                ]);
                $this->family = $this->families[count($this->families) - 1];
                $this->insert_families($post_id, $families);
            }

            $this->setStatusAvailable($post_id);

            $this->insert_post_meta($post_id, $update);
            $this->index_search_meta($post_id, $update);

            wp_set_object_terms($post_id, (int)$this->manufacturer->term_id, 'used-manufacturer');
//            wp_set_object_terms($post_id, $family_ids, 'cat_used_machine_family');
        }

        return $this;
    }


    private function insert_families($post_id, array $familiesTree): void
    {
        if ($this->isRewriteDisallowed($post_id)) {
            return;
        }
        wp_set_object_terms($post_id, $familiesTree, $this->taxonomy);

    }

    private function isRewriteDisallowed($post_id): bool
    {
        return (bool)get_post_meta($post_id, '_disallow_rewrite_used', true);
    }

    /**
     * Inserts posts meta information
     *
     * @param stdclass $machine machine data
     * @param int $post_id machine post id
     * @return void
     */
    private function insert_post_meta($post_id, $update)
    {
        $props = get_object_vars($this);
        $skip = array('post_id', 'families', 'manufacturer');

        $props['is_feed'] = true;

        foreach ($props as $k => $v) {
            if (in_array($k, $skip))
                continue;

            (!$update)
                ? add_post_meta($post_id, $k, $v, true)
                : update_post_meta($post_id, $k, $v);
        }
    }


    /**
     * Save the post metadata to the database
     *
     * @param [int] $id The ID of the post we need to save our post meta for
     * @return void
     */

    private function index_search_meta($post_id, $update)
    {
        $method = ($update) ? 'update' : 'save';

        if (isset($this->model))
            Indexer::$method($post_id, 'model', $this->model, $this->model, 'used');

        if (isset($this->price))
            Indexer::$method($post_id, 'price', $this->price, '$' . number_format($this->price), 'used');

        if (isset($this->city))
            Indexer::$method($post_id, 'city', $this->city, $this->city, 'used');

        if (isset($this->state))
            Indexer::$method($post_id, 'state', $this->state, $this->state, 'used');

        if (isset($this->year))
            Indexer::$method($post_id, 'year', $this->year, $this->year, 'used');

        if (isset($this->hours))
            Indexer::$method($post_id, 'hours', $this->hours, number_format($this->hours), 'used');

        if (isset($this->families[1]->name))
            Indexer::$method($post_id, 'family', $this->families[1]->name, $this->families[1]->name, 'used');

        if (isset($this->families[0]->name))
            Indexer::$method($post_id, 'class', $this->families[0]->name, $this->families[0]->name, 'used');

        if (isset($this->families[2]))
            Indexer::$method($post_id, 'type', $this->families[2]->name, $this->families[2]->name, 'used');

        if (isset($this->manufacturer))
            Indexer::$method($post_id, 'manufacturer', $this->manufacturer->name, $this->manufacturer->name, 'used');
    }


    private function process_contact($xml)
    {
        $attrs = $xml->attributes();
        $contact = new \stdClass();

        foreach ($attrs as $key => $value) {
            $prop = str_replace('-', '_', $key);
            $contact->{$prop} = trim($value);
        }

        return $contact;
    }

    private function process_features($xml)
    {
        $features = array();

        if (count($xml->feature) > 0) {
            foreach ($xml->feature as $feature) {
                $features[] = ucwords(strtolower(trim($feature)));
            }
        }

        return $features;
    }


    /**
     * Processes the coditions and makes a nice clean array
     *
     * @param conditions xml, the unformatted xml
     * @return array
     */

    private function process_conditions($condition)
    {
        $return = array();

        //Process the condition categories
        if (isset($condition->category)) {
            foreach ($condition->category as $category) {
                $cat = array();
                $cat['name'] = ucwords(strtolower(trim((string)$category["name"])));

                //Process the conditions for this category
                if (isset($category->section)) {
                    foreach ($category->section as $section) {
                        foreach ($section->children() as $condition) {
                            $name = trim((string)$condition["name"]);
                            $value = trim((string)$condition['value']);
                            $content = trim((string)$condition);

                            $finalvalue = $value;
                            $remaining_attributes = array();

                            foreach ($condition->children() as $side) {
                                foreach ($side->attributes() as $aname => $avalue) {
                                    $aname = trim((string)$aname);
                                    $avalue = trim((string)$avalue);

                                    if ($aname != "name"
                                        and $aname != "value"
                                        and $aname != ""
                                        and $avalue != ""
                                    ) {
                                        $prefix = ucwords($side->getName() . ': ');
                                        $aname = str_replace("-", " ", $aname);
                                        $aname = ucwords(strtolower($aname));
                                        $remaining_attributes[] = $prefix . $avalue . ' ' . $aname;
                                    }
                                }
                            }

                            if (count($remaining_attributes) > 0) {
                                $finalvalue .= implode(', ', $remaining_attributes);
                                $finalvalue = str_replace(' Percent', '%', $finalvalue);
                            }

                            if ($finalvalue != '' and $name != '') {
                                $cat['conditions'][$name] = $finalvalue;
                            }

                        } // each condition
                    } // each section
                } // if sections

                if (isset($cat['conditions'])) {
                    $return[$cat['name']] = $cat['conditions'];
                }

            } // each category
        }

        return $return;
    }


    /**
     * Processes the photos and makes a nice clean array
     *
     * @param photos xml, the unformatted xml
     * @return array
     */

    private function process_images($photos)
    {
        $images = array();

        if ($photos->children()) {
            foreach ($photos->children() as $photo) {
                $attrs = $photo->attributes();

                $image = new \stdClass();
                $image->updated = trim($attrs->{'updated-date'});
                $image->src = trim($photo);
                $image->src = str_replace("http", "https", $image->src);

                $images[] = $image;
            }
        }

        return $images;
    }

    private function setStatusAvailable($post_id)
    {
        update_post_meta($post_id, 'used_status', 'available');
    }

}
