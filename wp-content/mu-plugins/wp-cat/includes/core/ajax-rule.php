<?php


class AjaxRule
{


    public function __construct()
    {
        add_action('wp_ajax_get_new_family_action', array($this, 'getNewEquipmentFamily'));
        add_action('wp_ajax_nopriv_get_new_family_action', array($this, 'getNewEquipmentFamily'));
        add_action('wp_ajax_load_new_equipment_meta_field', array($this, 'loadNewEquipmentRuleMetaField'));
        add_action('wp_ajax_nopriv_load_new_equipment_meta_field', array($this, 'loadNewEquipmentRuleMetaField'));
        add_action('wp_ajax_save_new_equipment_meta_field', array($this, 'saveNewEquipmentRuleMetaField'));
        add_action('wp_ajax_nopriv_save_new_equipment_meta_field', array($this, 'saveNewEquipmentRuleMetaField'));

        add_action('wp_ajax_get_used_family_action', array($this, 'getUsedEquipmentFamily'));
        add_action('wp_ajax_nopriv_get_used_family_action', array($this, 'getUsedEquipmentFamily'));
        add_action('wp_ajax_load_used_equipment_meta_field', array($this, 'loadUsedEquipmentRuleMetaField'));
        add_action('wp_ajax_nopriv_load_used_equipment_meta_field', array($this, 'loadUsedEquipmentRuleMetaField'));
        add_action('wp_ajax_save_used_equipment_meta_field', array($this, 'saveUsedEquipmentRuleMetaField'));
        add_action('wp_ajax_nopriv_save_used_equipment_meta_field', array($this, 'saveUsedEquipmentRuleMetaField'));
    }

    public function getNewEquipmentFamily(): void
    {
        $categories = $this->getFamily('family', 'family_custom_textarea');
        wp_send_json_success($categories);
        wp_die();
    }

    public function loadNewEquipmentRuleMetaField(): void
    {
        $category_id = $_POST['category_id'];
        $meta_value = get_term_meta($category_id, 'family_custom_textarea', true);
        if ($meta_value) {
            $data = json_decode($meta_value, true);
            wp_send_json_success($data);
        }
        if (!$meta_value) {
            wp_send_json_success('{}');
        }
        wp_die();
    }

    public function saveNewEquipmentRuleMetaField(): void
    {
        $json = $_POST['JSON'];
        $categoryId = $_POST['category_id'];
        $this->saveRule($json, $categoryId, 'family_custom_textarea');

        wp_die();

    }

    public function getUsedEquipmentFamily(): void
    {
        $categories = $this->getFamily('used-family', 'used_family_custom_textarea');
        wp_send_json_success($categories);
        wp_die();
    }

    public function loadUsedEquipmentRuleMetaField(): void
    {
        $category_id = $_POST['category_id'];
        $meta_value = get_term_meta($category_id, 'used_family_custom_textarea', true);
        if ($meta_value) {
            $data = json_decode($meta_value, true);
            wp_send_json_success($data);
        }
        wp_die();
    }

    public function saveUsedEquipmentRuleMetaField(): void
    {

        $json = $_POST['JSON'];
        $categoryId = $_POST['category_id'];
        $this->saveRule($json, $categoryId, 'used_family_custom_textarea');
        wp_die();

    }


    public function saveRule($json, $familyId, $metaKey): void
    {
        if (empty($json)) {
            delete_term_meta($familyId, $metaKey);
        } else {
            $encodedJson = json_encode($json);
            update_term_meta($familyId, $metaKey, $encodedJson);
        }

        wp_send_json_success();
    }

    public function getFamily(string $taxonomy, $meta_key): array
    {
        $categories = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
            'hierarchical' => true,
            'number' => 0,
        ));
        return $this->buildCategoryTree($categories, $meta_key);
    }

    private function buildCategoryTree($categories, $meta_key, $parentId = 0, $level = 0): array
    {
        $result = array();

        foreach ($categories as $category) {
            if ($category->parent == $parentId) {
                $category->level = $level;
                $meta_value = get_term_meta($category->term_id, $meta_key, true);
                $category->has_meta = !empty($meta_value);
                $category->children = $this->buildCategoryTree($categories, $meta_key, $category->term_id, $level + 1);
                $result[] = $category;
            }
        }

        return $result;
    }


}

new AjaxRule();