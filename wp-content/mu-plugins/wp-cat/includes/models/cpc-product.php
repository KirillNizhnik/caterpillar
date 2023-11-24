<?php

namespace Cat\Models;

use Cat\Controllers\Importers\Mappers\FamilyAssignerByRule;
use Cat\Controllers\Importers\Mappers\NewEquipmentFamilyMapper;
use \Cat\Controllers\Indexer;

/**
 * CPCProduct
 * Imports custom data for products.
 *
 * @Package CAT New Feed/Importer
 * @category importer
 * @author WebFX
 */

if (!defined('ABSPATH'))
    exit;


class CPC_Product
{
    public $xml;
    public $equipment_id;
    public $post_id;
    public $sort;
    public $nondisplayname;
    public $name;
    public $longname;
    public $brand;
    public $class;
    public $short_description;
    public $long_description;
    public $images = array();
    public $videos = array();
    public $vpts = array();
    public $spinsets = array();
    public $documents = array();
    public $links = array();
    public $features = array();
    public $specs = array();
    public $related = array();
    public $standard_equipment = array();
    public $optional_equipment = array();
    public $family;
    public $subfamily;
    public string $group_id;


    public $is_rental = false;

    public function __construct($xml = '')
    {
        if (!empty($xml)) {
            $this->xml = $xml;
            $this->process();
        }
    }


    public function get_post_id($eid)
    {
        global $wpdb;

        $exists = $wpdb->get_row("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='equipment_id' AND meta_value ='$eid'");

        if ($exists) {
            return $exists->post_id;
        }
        return false;
    }


    public function save()
    {
        global $wpdb;

        $post = array();

        // fix bug with cron
        // https://core.trac.wordpress.org/ticket/19373
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
        // makes wordpress update rather then create
        if ($this->post_id) {
            $post['ID'] = $this->post_id;
        }


        // Setup the post information for inserting
        $post['post_title'] = (!empty($this->longname)) ? trim($this->longname) : $this->nondisplayname;
        $post['post_type'] = $this->post_type;
        $post['post_status'] = 'publish';
        $post['post_author'] = $current_user_id;


        $post['post_content'] = $this->isContentOverwriteable()
            ? wpautop($this->long_description)
            : get_post_field('post_content', $this->post_id);


        if (isset($this->short_description))
            $post['post_excerpt'] = $this->short_description;

        $families = $this->getFamiliesToInsert();
        if($families === false){
            $post['post_status'] = 'draft';
        }

        // insert/update our post and then update our custom table
        $this->post_id = wp_insert_post($post);

        if(is_array($families)){
            $this->insert_families($this->post_id, $families);
        }
        $this->insert_specs($this->post_id);
        $this->insert_post_meta($this->post_id);
        $this->index_search_meta($this->post_id, $update);
        // add hook to save meta, specs and index
        // add hook to save meta, specs and index
//        add_action('save_post_' . $this->post_type, array($this, 'after_save'), 10, 3);
        return $this;
    }


    private function isContentOverwriteable(): bool
    {
        return !get_post_meta($this->post_id, '_do_not_overwrite', true) && isset($this->long_description);
    }

    /**
     * Saves the custom post data
     *
     * @param $post_id int, The current post ID
     */

    public function after_save($post_id, $post, $update)
    {

        // remove our after save hook so we dont keep
        // calling for each product
//        remove_action('save_post_' . $this->post_type, array($this, 'after_save'), 10, 3);
    }

    private function insert_families($post_id , array $familiesTree): void
    {
        if ($this->isRewriteDisallowed($post_id)) {
            return;
        }
            wp_set_object_terms($post_id, $familiesTree, 'family');

    }

    private function getFamiliesToInsert()
    {
        $taxonomy = 'family';
        $familyAssignerByRule = new FamilyAssignerByRule($this->name, 'family_custom_textarea', $taxonomy, $this->group_id);
        $equipmentFamilyMapper = new NewEquipmentFamilyMapper($this->family,  $familyAssignerByRule, $taxonomy, $this->subfamily,);
        return $equipmentFamilyMapper->applyFamily();
    }

    private function isRewriteDisallowed($post_id): bool
    {
        return (bool)get_post_meta($post_id, '_disallow_rewrite', true);
    }


    /**
     * Save the post metadata to the database
     *
     * @param [int] $id The ID of the post we need to save our post meta for
     * @return void
     */

    private function insert_specs($post_id)
    {
        if (empty($this->specs))
            return;

        global $wpdb;
        $specs_table = $wpdb->prefix . 'cat_product_specs';

        $values = array();

        $sql = "INSERT INTO $specs_table
                (family_id,
                 product_id,
                 spec_group_id,
                 spec_id,
                 name,
                 group_name,
                 value_english,
                 value_metric,
                 unit_english,
                 unit_metric,
                 type,
                 sort,
                 group_sort,
                 priority,
                 updated) VALUES ";

        // build the values
        foreach ($this->specs as $spec) {
            // sql for prepare
            $sql .= "(%d, %d, %d, %d, %s, %s, %s, %s, %s, %s, %s, %d, %d, %d, %s),";

            // values that get inserted for the above
            // prepare just takes a single level array and places
            // then in the values in order.

            $values[] = $spec['family_id'];
            $values[] = $spec['product_id'];
            $values[] = $spec['spec_group_id'];
            $values[] = $spec['spec_id'];
            $values[] = $spec['name'];
            $values[] = $spec['group_name'];
            $values[] = $spec['value_english'];
            $values[] = $spec['value_metric'];
            $values[] = $spec['unit_english'];
            $values[] = $spec['unit_metric'];
            $values[] = $spec['type'];
            $values[] = $spec['sort'];
            $values[] = $spec['group_sort'];
            $values[] = $spec['priority'];
            $values[] = get_transient('current_new_import_time');
        }

        // remove the last comma after all the values
        $sql = substr($sql, 0, -1);

        // setup if key duplicate
        $sql .= " ON DUPLICATE KEY UPDATE
                    family_id = VALUES(family_id),
                    product_id = VALUES(product_id),
                    spec_group_id = VALUES(spec_group_id),
                    spec_id = VALUES(spec_id),
                    name = VALUES(name),
                    group_name = VALUES(group_name),
                    value_english = VALUES(value_english),
                    value_metric = VALUES(value_metric),
                    unit_english = VALUES(unit_english),
                    unit_metric = VALUES(unit_metric),
                    type = VALUES(type),
                    sort = VALUES(sort),
                    group_sort = VALUES(group_sort),
                    priority = VALUES(priority),
                    updated = VALUES(updated)";

        // insert into DB
        $wpdb->query($wpdb->prepare($sql, $values));

        // remove old specs for this product(in case CAT changed the spec ids on us)
        $import_start = get_transient('current_new_import_time');
        $remove_sql = "DELETE FROM $specs_table
                       WHERE product_id = '%d'
                       AND updated < '$import_start'";

        $wpdb->query($wpdb->prepare($remove_sql, $spec['product_id']));
    }


    /**
     * Save the post metadata to the database
     *
     * @param [int] $id The ID of the post we need to save our post meta for
     * @return void
     */

    private function insert_post_meta($post_id)
    {
        $vars = get_object_vars($this);
        $vars['model'] = $vars['name'];

        // Mark as new feed - rental marked separately (is_rental_feed)
        if (!$this->is_rental)
            $vars['is_feed'] = true;

        // remove the keys we dont need from our vars object.
        unset($vars['xml']);
        unset($vars['name']);
        unset($vars['long_description']);
        unset($vars['short_description']);
        unset($vars['post_id']);
        unset($vars['family']);
        unset($vars['subfamily']);
        unset($vars['post_type']);
        unset($vars['specs']);
        unset($vars['is_rental']);

        foreach ($vars as $k => $v) {
            add_post_meta($post_id, $k, $v, true) || update_post_meta($post_id, $k, $v);
        }
        update_post_meta($post_id, 'status', 'available');
        update_post_meta($post_id, 'group_id', $this->group_id);
    }

    /**
     * Save the post metadata to the database
     *
     * @param [int] $id The ID of the post we need to save our post meta for
     * @return void
     */

    private function index_search_meta($post_id, $update)
    {
        //        TODO:  refactor this method
//        $year = $this->get_year();
//        $industries = array_filter($this->get_industries());

//        $method = ($update) ? 'update' : 'save';
//
//        Indexer::$method($post_id, 'model', $this->name, $this->name, 'new');
//        Indexer::$method($post_id, 'family', $this->family->name, $this->family->name, 'new');
//
//        if ($year)
//            Indexer::$method($post_id, 'year', $year, $year, 'new');
//
//        // update industries
//        Indexer::delete($post_id, 'industry');
//
//        foreach ($industries as $industry) {
//            Indexer::save($post_id, 'industry', $industry, $industry, 'new');
//        }
    }


    /**
     * Base processor of the the xml
     *
     * @return null
     */

    public function process()
    {
        $product = $this->xml->product;
        $this->process_attrs($product);
        $this->process_properties($product);

        if (isset($product->standard_equipment))
            $this->process_equipment($product->standard_equipment, 'standard_equipment');

        if (isset($product->optional_equipment))
            $this->process_equipment($product->optional_equipment, 'optional_equipment');

        if (isset($product->listofsalesfeatures))
            $this->process_features($product->listofsalesfeatures);

        if (isset($product->listofimages))
            $this->process_images($product->listofimages);

        if (isset($product->listoflinks))
            $this->process_links($product->listoflinks);

        if (isset($product->marketing_content))
            $this->process_marketing($product->marketing_content);

        if (isset($product->specifications))
            $this->process_specs($product->specifications);

        if (isset($product->listofrelationships))
            $this->process_relationships($product->listofrelationships);

        return $this;
    }


    /**
     * Processes the attributes and basic elements
     *
     * @param [simple xml object] $item  The xml object to process
     * @return null
     */

    private function process_attrs($item)
    {
        $attrs = $item->attributes();

        $this->nondisplayname = trim($item->nondisplayname);
        $this->name = trim($item->name);
        $this->longname = trim($item->longname);
        $this->updated = trim($item->last_modified);
        $this->brand = trim($item->brand);
        $this->equipment_id = trim($attrs['id']);
        $this->sort = trim($attrs['sort']);
        $this->post_id = $this->get_post_id($attrs['id']);
    }


    /**
     * Processes the properties and set them to the model
     *
     * @param [simple xml object] $item  The xml object to process
     * @return null
     */

    private function process_properties($item)
    {
        if (!isset($item->property))
            return;

        foreach ($item->property as $p) {
            $attrs = $p->attributes();

            if ($attrs['type'] == ('short_description' or 'long_description')) {
                $attr_name = trim($attrs['type']);
                $this->$attr_name = wpautop(trim($p));
            } else {
                $this->$attrs['type'] = trim($p);
            }
        }
    }


    /**
     * Processes the images and set them to the model
     *
     * @param [simple xml object] $item  The xml image object to process
     * @return null
     */

    private function process_images($images)
    {
        $temp = array();

        foreach ($images->image as $i) {
            $attrs = $i->attributes();
            $url = explode('?', trim($i->url));

            $image = new \stdClass();
            $image->src = $url[0];
            $image->src = str_replace("http", "https", $image->src);
            $image->type = trim($attrs['type']);

            if ($attrs['type'] === "specalog_cover_jpg") {
                $temp['specalog_cover_jpg'] = $url[0];
            } elseif (in_array($attrs['type'], array('specalog_pdf', 'speclog_pdf', 'tech_spec_graphic'))) {
                // if its the speclog cover then attach.
                if ($attrs['type'] == "speclog_pdf" and isset($temp['specalog_cover_jpg'])) {
                    $image->image = $temp['specalog_cover_jpg'];
                }

                $this->documents[trim($attrs['id'])] = $image;
            } else {
                $this->images[trim($attrs['id'])] = $image;

            }


        }
    }


    /**
     * Processes the marketing content and set them to the model
     *
     * @param [simple xml object] $item  The xml markeeting object to process
     * @return null
     */

    private function process_marketing($marketing)
    {
        $importer = \Cat\Controllers\Importers\New_Feed::instance();

        foreach ($marketing->content as $c) {
            $meta = $this->process_marketing_meta($c);

            $attrs = $c->attributes();

            switch (trim($attrs['type'])) {
                case 'vpt-exterior':
                case 'vpt-interior':

                    $vpt = new \stdClass();
                    $vpt->title = trim($c->title);
                    $vpt->type = trim($attrs['type']);
                    $view = ($vpt->type == 'vpt-exterior') ? 'e' : 'i';
                    $vpt->src = 'https://h-cpc.cat.com/cmms/vpt-widget?groupid=' . $this->subfamily->family_id . '&prodid=' . $this->equipment_id . '&langid=en&view=' . $view . '&media=f&sc=' . $importer->dealer_code . '&hotspots=Y';

                    $this->vpts[] = $vpt;

                    break;

                case 'spinset-exterior':
                case 'spinset-interior':

                    $spinsets = new \stdClass();
                    $spinsets->title = trim($c->title);
                    $spinsets->caption = trim($c->media->caption);
                    $spinsets->type = trim($attrs['type']);
                    $spinsets->src = trim($c->media->source);

                    $this->spinsets[] = $spinsets;

                    break;

                case 'doc_library':

                    $d = 0;

                    if (isset($c->content)) {
                        foreach ($c->content as $content) {
                            $doc = new \stdClass();
                            $doc->title = trim($content->title);

                            if (isset($content->text))
                                $doc->text = trim($content->text);

                            $doc->type = trim($attrs['type']);

                            $url = explode('?', trim($content->media->source));
                            $doc->src = $url[0];

                            $this->documents[$d] = $doc;
                            $d++;
                        }
                    } else {

                        $dc = 0;

                        $doc = new \stdClass();
                        $doc->title = trim($c->title);
                        $doc->type = trim($attrs['type']);
                        $doc->src = trim($c->media->source);

                        $this->documents[$dc] = $doc;
                        $dc++;
                    }
                    break;

                case 'gallery-image':
                    $images = array();

                    /**
                     * Cat Feed issue for https://cpc.cat.com/ws/xml/en/305/18111626_en.xml
                     *
                     * 1. the second content type="gallery-image" should actually be a "gallery-video"
                     *    the wrong type attribute is causing items to be pushed into wrong arrays
                     *    as a quick fix we do a test here for the content id and route it the
                     *    proper video array
                     *
                     * CAT issue 4/18 - CAT removed attribute ids for videos/images/spinsets
                     * so instead of using the attr id to make item unique in array,
                     * we use a counter variable
                     */

                    if (trim($c->attributes()->id) != '18508980') { /* [1] */

                        $i = 0;

                        foreach ($c->content as $content) {

                            $image = new \stdClass();
                            $image->title = trim($content->title);
                            $image->type = trim($attrs['type']);

                            $url = explode('?', trim($content->media->source));
                            $image->src = $url[0];
                            $images[$i] = $image;
                            $i++;
                        }

                        $this->images = $images + $this->images;

                    } else { /* [1] */

                        $v = 0;

                        foreach ($c->content as $content) {

                            $video = new \stdClass();
                            $video->title = trim($content->title);

                            if (isset($content->text))
                                $video->text = trim($content->text);

                            $video->type = trim($content->media->attributes()->type);
                            $video->src = trim($content->media->source);

                            $this->videos[$v] = $video;
                            $v++;
                        }
                    } /* [1] */

                    break;

                case 'gallery-video':

                    $i = 0;

                    foreach ($c->content as $content) {

                        $video = new \stdClass();
                        $video->title = trim($content->title);

                        if (isset($content->text))
                            $video->text = trim($content->text);

                        $video->type = trim($content->media->attributes()->type);
                        $video->src = trim($content->media->source);

                        $this->videos[$i] = $video;
                        $i++;
                    }
                    break;
            }

        } // each content
    }


    /**
     * process marketing item, meta tags, to reference for country
     *
     * @param [simple xml object] $item  The xml markeeting object to process
     * @return [array] Single level array of tags;
     */

    private function process_marketing_meta($item)
    {
        $return = array();
        foreach ($item->metadata as $meta) {
            foreach ($meta->tag as $tag) {
                $return[] = trim($tag);
            }
        }

        return $return;
    }


    /**
     * Processes the link content and sets them to the model
     *
     * @param [simple xml object] $item  The xml link object to process
     * @return null
     */

    private function process_links($links)
    {
        foreach ($links->link as $l) {
            $link = new \stdClass();

            $link->label = trim($l->link_label);
            $link->url = trim($l->link_url);
            $link->type = trim($l->type);

            $this->links[] = $link;
        }
    }


    /**
     * Processes the sales features content and sets them to the model
     *
     * @param [simple xml object] $item  The xml feautres object to process
     * @return null
     */

    private function process_features($features, $return = false)
    {
        $featurelist = array();

        foreach ($features->salesfeature as $salesfeature) {
            $feature = new \stdClass();

            $feature->name = trim($salesfeature->name);
            $feature->content = wpautop(trim($salesfeature->paragraph));

            if (isset($salesfeature->listofimages)) {
                $feature->images = array();

                foreach ($salesfeature->listofimages->image as $image) {
                    $url = explode('?', trim($image->url));
                    $feature->images[trim($image->attributes()->id)] = $url[0];
                }
            }

            if (isset($salesfeature->listofsalesfeatures)) {
                $feature->children = $this->process_features($salesfeature->listofsalesfeatures, true);
            }

            $featurelist[] = $feature;
        }

        if ($return)
            return $featurelist;

        $this->features = array_merge($this->features, $featurelist);
    }


    /**
     * Processes the specs
     *
     * @param [simple xml object] $specs  The xml feautres object to process
     * @return null
     */

    private function process_specs($specs)
    {
        foreach ($specs->spec as $s) {
            $spec = array();
            $attrs = $s->attributes();
            $group_attrs = $s->title->attributes();

            $spec['family_id'] = $this->subfamily_id;
            $spec['product_id'] = $this->equipment_id;
            $spec['spec_group_id'] = trim($group_attrs['id']);
            $spec['spec_id'] = trim($attrs['id']);
            $spec['name'] = trim($s->name);
            $spec['group_name'] = trim($s->title);
            $spec['type'] = trim($attrs['type']);
            $spec['sort'] = trim($attrs['sort']);
            $spec['group_sort'] = trim($group_attrs['sort']);
            $spec['priority'] = trim($attrs['sort']);

            // setup default values for items
            // that may not exist
            $spec['value_metric'] = '';
            $spec['value_english'] = '';
            $spec['unit_metric'] = '';
            $spec['unit_english'] = '';


            if ($spec['type'] === 'text') {
                $spec['value_english'] = trim($s->{'value-text'});
            }

            // process any values
            if (isset($s->value)) {
                foreach ($s->value as $value) {
                    $value_attrs = $value->attributes();
                    $type = trim($value_attrs['type']);

                    switch ($type) {
                        case 'metric':
                            $spec['value_metric'] = trim($value);
                            break;

                        case 'english':
                        default:
                            $spec['value_english'] = trim($value);
                            break;
                    }
                }
            }

            // process any units
            if (isset($s->unit)) {
                foreach ($s->unit as $unit) {
                    $unit_attrs = $unit->attributes();
                    $type = trim($unit_attrs['type']);

                    switch ($type) {
                        case 'metric':
                            $spec['unit_metric'] = trim($unit);
                            break;

                        case 'english':
                        default:
                            $spec['unit_english'] = trim($unit);
                            break;
                    }
                }
            } // if units

            $this->specs[] = $spec;
        } // foreach spec
    }


    /**
     * Processes products related items
     *
     * @param [simple xml object] $item  The xml object to process
     * @return null
     */

    private function process_relationships($related)
    {
        $relationshps = array();

        foreach ($related->relationship as $r) {
            $attrs = $r->attributes();

            $group = trim($r->related_to_group_or_product);
            $type = trim($attrs['type']);
            $id = trim($r->related_to_id);

            $relationships[$group][$type][] = $id;
        }

        $this->related = $relationships;
    }


    /**
     * Processes products equipment
     *
     * @param [simple xml object] $equipment  The xml object to process
     * @param [string] $type  The equipment object name we are processing
     * @return [mixed] null or array of items
     */

    private function process_equipment($equipment, $type, $return = false)
    {
        $items = array();

        foreach ($equipment->equipment as $e) {
            $description = trim(str_replace(array('•', '®', '™'), array('', '<sup>&reg;</sup>', '<sup>&trade;</sup>'), (string)$e->description));
            $children = array();

            if (isset($e->listofequipment)) {
                $children = $this->process_equipment($e->listofequipment, false, true);
            }

            if (!empty($children))
                $items[] = array('description' => $description, 'children' => $children);
            else
                $items[] = $description;
        }

        if ($return)
            return $items;

        $this->$type = $items;
    }


    private function get_year()
    {
        preg_match('/-\ ?(20[0-9]{2})/', $this->nondisplayname, $matches);

        if (!empty($matches))
            return $matches[1];

        return false;
    }

    private function get_industries()
    {
        global $wpdb;

        $ids = $wpdb->get_col($wpdb->prepare(
            "SELECT DISTINCT industry_id
             FROM   {$wpdb->prefix}cat_term_industries
             WHERE  object_id = %d
            ",
            $this->family->term_id
        ));

        $industries = array();

        if ($ids) {
            foreach ($ids as $post_id) {
                $industries[] = get_the_title($post_id);
            }
        }

        return $industries;
    }


}// End of File
