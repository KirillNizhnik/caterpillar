<?php

class NewEquipmentMetaFields
{
    public function __construct()
    {
        add_action('edited_family', array($this, 'save_family_custom_textarea_field'), 10, 2);
        add_action('family_edit_form_fields', array($this, 'display_family_custom_textarea_field'), 10, 1);
        add_action('create_family', array($this, 'save_family_custom_textarea_field'), 10, 2);
        add_action('add_meta_boxes', array($this, 'add_equipment_meta_box'));
        add_action('save_post_equipment', array($this, 'save_equipment_meta'));
        add_action('add_meta_boxes_equipment', [$this, 'add_custom_meta_box']);
        add_action('save_post_equipment', [$this, 'save_custom_meta_box'], 11, 3);
        add_action('restrict_manage_posts', [$this, 'add_status_filter']);
        add_filter('parse_query', [$this, 'apply_status_filter']);
        add_filter('manage_equipment_posts_columns', [$this, 'add_status_column']);
        add_action('manage_equipment_posts_custom_column', [$this, 'display_status_column'], 10, 2);
        add_action('add_meta_boxes_equipment', [$this, 'add_group_id_metabox']);
        add_action('add_meta_boxes_equipment', [$this, 'add_array_fam_meta_box']);
        add_action('add_meta_boxes_equipment', array($this, 'add_custom_meta_box_callback'));


//        add_action('acf/include_fields', [$this, 'addSimilarProductsField']);
        add_action('acf/init', [$this, 'addSimilarProductsField']);
        add_filter('acf/prepare_field/name=similar_products', function ($field) {
            if (get_post_meta(get_the_ID(), 'status', true) === 'not_available') {
                return $field;
            }
            return null;
        });
    }


    function save_family_custom_textarea_field($term_id): void
    {
        if (isset($_POST['family_custom_textarea'])) {
            update_term_meta($term_id, 'family_custom_textarea', sanitize_textarea_field($_POST['family_custom_textarea']));
        }
    }

    function display_family_custom_textarea_field($term): void
    {
        $textarea_value = get_term_meta($term->term_id, 'family_custom_textarea', true);
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="family_custom_textarea">Rule</label></th>
            <td>
                <textarea name="family_custom_textarea" id="family_custom_textarea"
                          rows="5"><?php echo esc_textarea($textarea_value); ?></textarea>
                <p class="description">Readonly, edit category on CAT Settings -> New Equipment Rule.</p>
            </td>
        </tr>
        <?php
    }

    public function add_equipment_meta_box(): void
    {
        add_meta_box('equipment_meta_box', 'Equipment Settings', array($this, 'display_equipment_meta_box'), 'equipment', 'normal', 'default');
    }

    public function display_equipment_meta_box($post): void
    {
        wp_nonce_field(basename(__FILE__), 'equipment_nonce');

        $disallow_rewrite = get_post_meta($post->ID, '_disallow_rewrite', true);
        ?>

        <label for="disallow_rewrite">
            <input type="checkbox" name="disallow_rewrite" id="disallow_rewrite"
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
        $disallow_rewrite = isset($_POST['disallow_rewrite']) ? 1 : 0;
        update_post_meta($post_id, '_disallow_rewrite', $disallow_rewrite);

        $do_not_overwrite = isset($_POST['do_not_overwrite']) ? 1 : 0;
        update_post_meta($post_id, '_do_not_overwrite', $do_not_overwrite);
    }


    public function add_custom_meta_box()
    {
        add_meta_box('status_meta_box', 'Status', array($this, 'render_status_meta_box'), 'equipment', 'side', 'default');
    }

    public function render_status_meta_box($post)
    {

        $meta = get_post_meta($post->ID, 'status', true); ?>
        <label for="status">Status:</label><br>
        <input type="radio" name="status" value="available" <?php checked($meta, 'available'); ?>> Available<br>
        <input type="radio" name="status" value="not_available" <?php checked($meta, 'not_available'); ?>> Not Available
        <br>
        <input type="radio" name="status" value="hidden" <?php checked($meta, 'hidden'); ?>> Hidden
        <?php
    }

    public function save_custom_meta_box($post_id, WP_Post $post, bool $update)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return $post_id;
        if(!$update){
            update_post_meta($post_id, 'status', 'available');
            return $post_id;
        }
        $currentStatus = get_post_meta($post_id, 'status', true);

        if (isset($_POST['status']) && $_POST['status'] !== $currentStatus) {
            switch ($_POST['status']) {
                case 'hidden':
                    $this->handleHiddenStatus($post_id, $currentStatus);
                    break;
                case 'available':
                    $this->handleAvailableStatus($post_id);
                    break;
                case 'not_available':
                    $this->handleNotAvailableStatus($post_id, $currentStatus);
                    break;
            }
            update_post_meta($post_id, 'status', $_POST['status']);
        }
    }


    private function handleHiddenStatus(int $post_id, string $currentStatus)
    {
        if ($currentStatus === 'available') {
            $this->setMetaFamily($post_id);
        }
        wp_set_post_terms($post_id, [], 'family');
    }

    private function handleAvailableStatus(int $post_id)
    {
        $fam = get_post_meta($post_id, 'array_fam', true);
        wp_set_post_terms($post_id, $fam, 'family');
        update_post_meta($post_id, 'array_fam', null);
    }

    private function handleNotAvailableStatus(int $post_id, string $currentStatus)
    {
        if ($currentStatus === 'available') {
            $this->setMetaFamily($post_id);
            wp_set_post_terms($post_id, [], 'family');
        }
        wp_set_post_terms($post_id, $this->getArchiveFamily(), 'family');
    }

    private function setMetaFamily($postId): void
    {
        $fam = wp_get_object_terms($postId, 'family');
        $fam_id = [];
        foreach ($fam as $item) {
            $fam_id[] = $item->term_id;
        }
        update_post_meta($postId, 'array_fam', $fam_id);
    }

    private function getArchiveFamily()
    {
        return get_option('archive_category');
    }


    function add_status_filter()
    {
        global $typenow;
        if ($typenow == 'equipment') {
            $values = array(
                'available' => 'Available',
                'not_available' => 'Not Available',
                'hidden' => 'Hidden',
            );
            ?>
            <label>
                <select name="status_filter">
                    <option value="">All Status</option>
                    <?php
                    $status = $_GET['status_filter'] ?? '';
                    foreach ($values as $value => $label) {
                            echo '<option value="' . $value . '"' . selected($status, $value, false) . '>' . $label . '</option>';
                    }
                    ?>
                </select>
            </label>
            <?php
        }
    }

    function apply_status_filter($query)
    {
        global $pagenow;
        if ('edit.php' != $pagenow) return;
        $status_filter = $_GET['status_filter'] ?? '';
        if ('equipment' === $query->get('post_type') && $status_filter) {
            $query->set('meta_key', 'status');
            $query->set('meta_value', $status_filter);
        }
    }

    function add_status_column($columns)
    {
        $columns['status'] = 'Status';
        return $columns;
    }


    function display_status_column($column, $post_id)
    {
        if ($column == 'status') {
            $status = get_post_meta($post_id, 'status', true);
            if ($status == 'not_available') {
                echo '<br><span style="color:red; font-size: 18px">Not Available</span>';
            } elseif ($status == 'hidden') {
                echo '<br><span style="color:#FFCC33; font-size: 18px">Hidden</span>';
            } elseif ($status == 'available') {
                echo '<br><span style="color:green; font-size: 18px">Available</span>';
            }
        }
    }

    function add_group_id_metabox()
    {
        add_meta_box(
            'group_id_metabox',
            'Group ID',
            array($this, 'render_group_id_metabox'),
            'equipment',
            'normal',
            'default'
        );
    }

    function render_group_id_metabox($post)
    {
        $group_id = get_post_meta($post->ID, 'group_id', true);
        ?>
        <label for="group_id">Group ID:</label>
        <label type="text" id="group_id" name="group_id"><?php echo esc_attr($group_id); ?> </label>
        <?php
    }

    function display_array_fam_meta_box($post)
    {
        $fam_id = get_post_meta($post->ID, 'array_fam', true);
        // Вывод значения метаполя array_fam
        echo 'Family Categories: ';
        if (!empty($fam_id)) {
            echo implode(', ', $fam_id);
        } else {
            echo 'No categories selected.';
        }
    }

    function add_array_fam_meta_box()
    {
        add_meta_box('array_fam', 'Array Family for status', [$this, 'display_array_fam_meta_box'], 'equipment', 'normal', 'default');
    }

    public function addSimilarProductsField()
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group(array(
            'key' => 'group_6543dac2233a2',
            'title' => 'Similar Products',
            'fields' => array(
                array(
                    'key' => 'field_6543dac2ee318',
                    'label' => 'Similar products',
                    'name' => 'similar_products',
                    'aria-label' => '',
                    'type' => 'relationship',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'post_type' => array(
                        0 => 'equipment',
                    ),
//                    'post_status' => '',
//                    'taxonomy' => '',
                    'filters' => array(
                        0 => 'search',
                        1 => 'post_type',
                        2 => 'taxonomy',
                    ),
                    'return_format' => 'object',
//                    'min' => '',
                    'max' => 3,
//                    'elements' => '',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'equipment',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
            'show_in_rest' => 0,
        ));
    }

    public function add_custom_meta_box_callback() {
        add_meta_box(
            'custom_meta_box',
            'Do not overwrite content',
            array($this, 'display_custom_meta_box_callback'),
            'equipment', // Replace with your actual custom post type
            'normal',
            'high'
        );
    }

    public function display_custom_meta_box_callback($post) {
        wp_nonce_field(basename(__FILE__), 'custom_nonce');

        $do_not_overwrite = get_post_meta($post->ID, '_do_not_overwrite', true);
        ?>

        <label for="do_not_overwrite">
            <input type="checkbox" name="do_not_overwrite" id="do_not_overwrite"
                   value="1" <?php checked($do_not_overwrite, 1); ?> />
            Do not overwrite content
        </label>

        <?php
    }




}

new NewEquipmentMetaFields();

