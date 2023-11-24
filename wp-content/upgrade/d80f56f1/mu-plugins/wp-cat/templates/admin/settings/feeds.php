
<h3 id='cat-new'>New Equipment</h3>
<table class="form-table">
    <tr valign="top">
        <th scope="row">
            <label for="cat_new_sales_channel_code">
                Sales Channel Code
            </label>
        </th>
        <td>
            <input type="text"
                   name="cat_new_sales_channel_code"
                   id="cat_new_sales_channel_code"
                   value="<?php echo get_option('cat_new_sales_channel_code'); ?>"
                   style="width: 100%; max-width: 250px">
        </td>
        <th scope="row">
            <label for="cat_new_sales_channel_code">
                Secret API Code
            </label>
        </th>
        <td>
            <input type="text"
                   name="cat_new_secret_api_code"
                   id="cat_new_secret_api_code"
                   value="<?php echo get_option('cat_new_secret_api_code'); ?>"
                   style="width: 100%; max-width: 250px">
        </td>
    </tr>
    <tr valign="top">
        <?php $email_admin = get_option('cat_new_email_update', 0); ?>

        <th scope="row"><label for="cat_new_email_update">Email Admin on update</label></th>
        <td>
            <label><input name="cat_new_email_update" type="radio" value="1" <?php checked(1, $email_admin);?>> Yes</label>
            <label><input name="cat_new_email_update" type="radio" value="0" <?php checked(0, $email_admin);?>> No</label>
        </td>
    </tr>

</table>


<!--<h4>Include Classes</h4> -->

<table class="form-table">
    <tr valign="top">
        <th scope="row">
            <label for="<?php echo 'cat_new_class_limitation'; ?>">
                Available Classes
            </label>
        </th>
        <td>
            <?php foreach($available_classes as $id => $class): ?>
            <label>
              <input type="checkbox"
                     name="cat_new_class_limitation[]"
                     id="cat_new_class_limitation"
                     value="<?php echo $id; ?>"
                     <?php echo in_array($id, $classes) ? 'checked' : '';?>> <?php echo $class; ?>
            </label><br>
            <?php endforeach; ?>
        </td>
    </tr>
</table>


<!--<h4>Permalinks / URL Structure</h4>-->
<table class="form-table">
<?php if(!empty($classes)): ?>
<?php foreach($available_classes as $id => $class): ?>
<?php if (!in_array($id, $classes)) continue; ?>

    <tr valign="top">
        <th scope="row">
            <label for="<?php echo 'cat_new_base_slug'; ?>">
                <?php echo $class; ?> Base Slug
            </label>
        </th>
        <td>
            <?php $base = get_option($class_post_type_relation[$id].'_slug'); ?>
            <input type="text"
                   name="<?php echo $class_post_type_relation[$id].'_slug'; ?>"
                   id="<?php echo $class_post_type_relation[$id].'_slug'; ?>"
                   value="<?php echo $base; ?>"
                   placeholder="<?php echo \Cat\Core\Post_types::get_class_default_slug($id); ?>"
                   style="width: 250px">
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <label for="<?php echo 'cat_new_base_slug'; ?>">
                Rental <?php echo $class; ?> Base Slug
            </label>
        </th>
        <td>
            <?php $base = get_option($class_post_type_relation[$id].'_rental_slug'); ?>
            <input type="text"
                   name="<?php echo $class_post_type_relation[$id].'_rental_slug'; ?>"
                   id="<?php echo $class_post_type_relation[$id].'_rental_slug'; ?>"
                   value="<?php echo $base; ?>"
                   placeholder="<?php echo \Cat\Core\Post_types::get_class_default_slug($id, 'rental'); ?>"
                   style="width: 250px">
        </td>
    </tr>
    <?php endforeach; ?>

    <?php if( get_option('cat_use_industries') ): ?>
    <tr valign="top">
        <th scope="row">
            <label for="<?php echo 'cat_new_base_slug'; ?>">
                Industries Base Slug
            </label>
        </th>
        <td>
            <?php $base = get_option('cat_industry_slug'); ?>
            <input type="text"
                   name="cat_industry_slug"
                   id="cat_industry_slug"
                   value="<?php echo $base; ?>"
                   placeholder="industries"
                   style="width: 250px">
        </td>
    </tr>
    <?php endif; ?>

<?php else: ?>
    <tr valign="top">
        <th scope="row">Enable Class types to set URL structures</th>
    </tr>
<?php endif;?>

    <tr>
        <td colspan="2" height="20"></td>
    </tr>

</table>

<h3 id='cat-rental'>Rental Equipment</h3>
<?php $rental_environment = get_option('cat_rental_environment', ''); ?>
<table class="form-table">
    <tr valign="top">
        <th scope="row">
            <label for="cat_rental_disabled">Disabled</label>
        </th>
        <td>
            <input type="radio"
                   name="cat_rental_environment"
                   id="cat_rental_disabled"
                    <?php checked(empty($rental_environment)) ?>
                   value="">
        </td>
    </tr>
</table>
<?php foreach ($this->rental_environments as $key => $name): ?>
<hr/>
<table class="form-table">
    <tr valign="top">
        <th scope="row">
            <label for="cat_rental_<?php echo $key ?>"><?php echo $name ?> Enabled</label>
        </th>
        <td>
            <input type="radio"
                   name="cat_rental_environment"
                   id="cat_rental_<?php echo $key ?>"
                    <?php checked($rental_environment, $key) ?>
                   value="<?php echo $key ?>">
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <label for="cat_rental_<?php echo $key ?>_user" style="float:right"><?php echo $name ?> User</label>
        </th>
        <td>
            <input type="text"
                   name="cat_rental_<?php echo $key ?>_user"
                   id="cat_rental_<?php echo $key ?>_user"
                   value="<?php echo get_option('cat_rental_' . $key . '_user'); ?>"
                   style="width: 100%; max-width: 250px">
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <label for="cat_rental_<?php echo $key ?>_password" style="float:right"><?php echo $name ?> Password</label>
        </th>
        <td>
            <input type="text"
                   name="cat_rental_<?php echo $key ?>_password"
                   id="cat_rental_<?php echo $key ?>_password"
                   value="<?php echo get_option('cat_rental_' . $key . '_password'); ?>"
                   style="width: 100%; max-width: 250px">
        </td>
    </tr>
</table>
<?php endforeach ?>
<hr/>

<h3 id='cat-used'>Used Equipment</h3>

<table class="form-table">
    <tr valign="top">
        <th scope="row">
            <label for="cat_used_feed_url">
                DSF-Data URL
            </label>
        </th>
        <td>
            <input type="text"
                   name="cat_used_feed_url"
                   id="cat_used_feed_url"
                   value="<?php echo get_option('cat_used_feed_url'); ?>"
                   style="width: 100%; max-width: 250px">
        </td>
    </tr>
    <tr valign="top">
        <?php $email_admin = get_option('cat_used_email_update', 0); ?>

        <th scope="row"><label for="cat_used_email_update">Email Admin on update</label></th>
        <td>
            <label><input name="cat_used_email_update" type="radio" value="1" <?php checked(1, $email_admin);?>> Yes</label>
            <label><input name="cat_used_email_update" type="radio" value="0" <?php checked(0, $email_admin);?>> No</label>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <label for="<?php echo 'cat_used_machine_slug'; ?>">
                Base Slug
            </label>
        </th>
        <td>
            <?php $base = get_option('cat_used_machine_slug'); ?>

            <input type="text"
                   name="cat_used_machine_slug"
                   id="cat_used_machine_slug"
                   value="<?php echo $base; ?>"
                   placeholder="<?php echo \Cat\Core\Post_types::get_class_default_slug('used'); ?>"
                   style="width: 250px">
        </td>
    </tr>
</table>

<input type="hidden" name="cat_use_industries" value="<?php echo get_option('cat_use_industries', 0); ?>" />
<input type="hidden" name="cat_use_applications" value="<?php echo get_option('cat_use_applications', 0); ?>" />
<input type="hidden" name="tab" value="feeds" />
 



