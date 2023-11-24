<?php

class UsedEquipmentMetaFields
{
    public function __construct()
    {
        add_action('edited_used-family', array($this, 'save_used_family_custom_textarea_field'), 10, 2);
        add_action('used-family_edit_form_fields', array($this, 'display_used_family_custom_textarea_field'), 10, 1);
        add_action('create_used-family', array($this, 'save_used_family_custom_textarea_field'), 10, 2);
        add_action('add_meta_boxes_used-equipment', array($this, 'add_equipment_meta_box'));
        add_action('save_post', array($this, 'save_equipment_meta'));
        add_action('add_meta_boxes', [$this, 'add_custom_meta_box']);
        add_action('save_post_used-equipment', [$this, 'save_custom_meta_box'], 11, 3);
        add_filter('manage_used-equipment_posts_columns', [$this, 'add_status_column']);
        add_action('manage_used-equipment_posts_custom_column', [$this, 'display_status_column'], 10, 2);
        add_action('restrict_manage_posts', [$this, 'add_status_filter']);
        add_filter('parse_query', [$this, 'apply_status_filter']);
        add_action('add_meta_boxes_used-equipment', [$this, 'add_array_fam_meta_box']);
    }


    function save_used_family_custom_textarea_field($term_id)
    {
        if (isset($_POST['used_family_custom_textarea'])) {
            update_term_meta($term_id, 'used_family_custom_textarea', sanitize_textarea_field($_POST['used_family_custom_textarea']));
        }
    }

    function display_used_family_custom_textarea_field($term)
    {
        $textarea_value = get_term_meta($term->term_id, 'used_family_custom_textarea', true);
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="used_family_custom_textarea">Rule</label></th>
            <td>
                <textarea readonly name="used_family_custom_textarea" id="used_family_custom_textarea"
                          rows="5"><?php echo esc_textarea($textarea_value); ?></textarea>
                <p class="description">Readonly, edit category on CAT Settings -> Used Equipment Rule.</p>
            </td>
        </tr>
        <?php
    }

    public function add_equipment_meta_box(): void
    {
        add_meta_box('equipment_meta_box', 'Equipment Settings', array($this, 'display_equipment_meta_box'), 'used-equipment', 'normal', 'default');
    }

    public function display_equipment_meta_box($post): void
    {
        wp_nonce_field(basename(__FILE__), 'equipment_nonce');

        $disallow_rewrite = get_post_meta($post->ID, '_disallow_rewrite_used', true);
        ?>

        <label for="disallow_rewrite_used">
            <input type="checkbox" name="disallow_rewrite_used" id="disallow_rewrite_used"
                   value="1" <?php checked($disallow_rewrite, 1); ?> />
            Disallow Rewrite
        </label>

        <?php
    }

    public function save_equipment_meta($post_id)
    {
        if (!isset($_POST['equipment_nonce']) || !wp_verify_nonce($_POST['equipment_nonce'], basename(__FILE__))) {
            return $post_id;
        }

        $disallow_rewrite = isset($_POST['disallow_rewrite_used']) ? 1 : 0;
        update_post_meta($post_id, '_disallow_rewrite_used', $disallow_rewrite);
    }

    public function add_custom_meta_box()
    {
        add_meta_box('status_meta_box', 'Status', array($this, 'render_status_meta_box'), 'used-equipment', 'side', 'default');
    }

    public function render_status_meta_box($post)
    {
        $meta = get_post_meta($post->ID, 'used_status', true);
        ?>

        <label for="status">Status:</label><br>
        <input type="radio" name="status" value="available" <?php checked($meta, 'available'); ?>> Available<br>
        <input type="radio" name="status" value="not_available" <?php checked($meta, 'not_available'); ?>> Not Available
        <?php
    }


    public function save_custom_meta_box(int $post_id, WP_Post $post, bool $update)
    {

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return $post_id;

        if (!$update) {
            update_post_meta($post_id, 'status', 'available');
            return $post_id;
        }
        $currentStatus = get_post_meta($post_id, 'used_status', true);
        if (isset($_POST['status'])) {
            if ($_POST['status'] !== $currentStatus) {
                if ($_POST['status'] === 'available') {
                    $array_fam = get_post_meta($post_id, 'array_fam', true);
                    wp_set_post_terms($post_id, $array_fam, 'used-family');
                    update_post_meta($post_id, 'array_fam', []);
                } else {
                    $fam = wp_get_object_terms($post_id, 'used-family');
                    $fam_id = [];
                    foreach ($fam as $item) {
                        $fam_id[] = $item->term_id;
                    }
                    update_post_meta($post_id, 'array_fam', $fam_id);
                    wp_set_post_terms($post_id, [], 'used-family');
                }
                update_post_meta($post_id, 'used_status', $_POST['status']);
            }
        }
    }

    function add_status_filter()
    {
        global $typenow;
        if ($typenow == 'used-equipment') {
            $values = array(
                'available' => 'Available',
                'not_available' => 'Not Available',
            );
            ?>
            <select name="status_filter">
                <option value="">All Status</option>
                <?php
                $status = $_GET['status_filter'] ?? '';
                foreach ($values as $value => $label) {
                        echo '<option value="' . $value . '"' . selected($status, $value, false) . '>' . $label . '</option>';
                }
                ?>
            </select>
            <?php
        }
    }

    function apply_status_filter($query)
    {
        global $pagenow;
        if ('edit.php' != $pagenow) return;
        $status_filter = $_GET['status_filter'] ?? '';
        if ('used-equipment' == $query->get('post_type') && $status_filter) {
            $query->set('meta_key', 'used_status');
            $query->set('meta_value', $status_filter);
        }
    }

    function display_array_fam_meta_box($post)
    {
        $fam_id = get_post_meta($post->ID, 'array_fam', true);
        echo 'Family Categories: ';
        if (!empty($fam_id)) {
            echo implode(', ', $fam_id);
        } else {
            echo 'No categories selected.';
        }
    }

    function add_array_fam_meta_box()
    {
        add_meta_box('array_fam', 'Array Family for status', [$this, 'display_array_fam_meta_box'], 'used-equipment', 'normal', 'default');
    }

    function add_status_column($columns)
    {
        $columns['status'] = 'Status';
        return $columns;
    }

    function display_status_column($column, $post_id)
    {
        if ($column == 'status') {
            $status = get_post_meta($post_id, 'used_status', true);
            if ($status == 'not_available') {
                echo '<br><span style="color:red; font-size: 18px">Not Available</span>';
            } elseif ($status == 'hidden') {
                echo '<br><span style="color:#FFCC33; font-size: 18px">Hidden</span>';
            } elseif ($status == 'available') {
                echo '<br><span style="color:green; font-size: 18px">Available</span>';
            }
        }
    }


}

new UsedEquipmentMetaFields();
