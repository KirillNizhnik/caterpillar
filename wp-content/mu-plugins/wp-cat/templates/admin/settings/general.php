<!--<h3>Addons</h3>-->
<!--<table class="form-table">-->
<!--    <tr valign="top">-->
<!--        <th scope="row">-->
<!--            <label for="cat_use_industries">-->
<!--                Use Industries-->
<!--            </label>-->
<!--        </th>-->
<!--        <td>-->
<!--            --><?php //$current = get_option('cat_use_industries', 0); ?>
<!---->
<!--            <label>-->
<!--                <input type="radio"-->
<!--                       name="cat_use_industries"-->
<!--                       id="cat_use_industries"-->
<!--                       value="1"-->
<!--                    --><?php //checked(1, $current); ?><!-- > -->
<!--                Yes-->
<!--            </label>-->
<!---->
<!--            <label>-->
<!--                <input type="radio"-->
<!--                       name="cat_use_industries"-->
<!--                       id="cat_use_industries"-->
<!--                       value="0"-->
<!--                    --><?php //checked(0, $current); ?><!-- > -->
<!--                No-->
<!--            </label>-->
<!--        </td>-->
<!--    </tr>-->
<!--    <tr valign="top">-->
<!--        <th scope="row">-->
<!--            <label for="cat_use_industries">-->
<!--                Use Applications-->
<!--            </label>-->
<!--        </th>-->
<!--        <td>-->
<!--            --><?php //$current = get_option('cat_use_applications', 0); ?>
<!---->
<!--            <label>-->
<!--                <input type="radio"-->
<!--                       name="cat_use_applications"-->
<!--                       id="cat_use_applications"-->
<!--                       value="1"-->
<!--                    --><?php //checked(1, $current); ?><!-- > -->
<!--                Yes-->
<!--            </label>-->
<!---->
<!--            <label>-->
<!--                <input type="radio"-->
<!--                       name="cat_use_applications"-->
<!--                       id="cat_use_applications"-->
<!--                       value="0"-->
<!--                    --><?php //checked(0, $current); ?><!-- > -->
<!--                No-->
<!--            </label>-->
<!--        </td>-->
<!--    </tr>-->
<!--</table>-->

<h3>General settings</h3>
<!--<p class="description">Enter relative url to page <b>e.g: /about-us</b></p>-->
<table class="form-table">
<!--    <tr valign="top">-->
<!--        <th scope="row">-->
<!--            <label for="cat_financing_url">-->
<!--                Financing URL-->
<!--            </label>-->
<!--        </th>-->
<!--        <td>-->
<!--            <input type="text"-->
<!--                   name="cat_financing_url"-->
<!--                   id="cat_financing_url"-->
<!--                   value="--><?php //echo esc_attr(get_option('cat_financing_url', '')); ?><!--">-->
<!---->
<!--        </td>-->
<!--    </tr>-->
<!--    <tr valign="top">-->
<!--        <th scope="row">-->
<!--            <label for="cat_demo_url">-->
<!--                Schedule A Demo URL-->
<!--            </label>-->
<!--        </th>-->
<!--        <td>-->
<!--            <input type="text"-->
<!--                   name="cat_demo_url"-->
<!--                   id="cat_demo_url"-->
<!--                   value="--><?php //echo esc_attr(get_option('cat_demo_url', '')); ?><!--">-->
<!---->
<!--        </td>-->
<!--    </tr>-->
<!--    <tr valign="top">-->
<!--        <th scope="row">-->
<!--            <label for="cat_em_solutions_url">-->
<!--                EM Solutions URL-->
<!--            </label>-->
<!--        </th>-->
<!--        <td>-->
<!--            <input type="text"-->
<!--                   name="cat_em_solutions_url"-->
<!--                   id="cat_em_solutions_url"-->
<!--                   value="--><?php //echo esc_attr(get_option('cat_em_solutions_url', '')); ?><!--">-->
<!---->
<!--        </td>-->
<!--    </tr>-->
<!--    <tr valign="top">-->
<!--        <th scope="row">-->
<!--            <label for="cat_rent_url">-->
<!--                Rent URL-->
<!--            </label>-->
<!--        </th>-->
<!--        <td>-->
<!--            <input type="text"-->
<!--                   name="cat_rent_url"-->
<!--                   id="cat_rent_url"-->
<!--                   value="--><?php //echo esc_attr(get_option('cat_rent_url', '')); ?><!--">-->
<!---->
<!--        </td>-->
<!--    </tr>-->
    <tr valign="top">
        <th scope="row">
            <label for="archive_category">
                Archive Family
            </label>
        </th>
        <td>

            <select id="archive_category" name="archive_category">

                <?php
                $cat = get_option('archive_category');
                ?>
                <option disabled <?= $cat ? '':'selected' ?>>Not Selected</option>
                <?php
                $terms = get_terms(array(
                    'taxonomy' => 'family',
                    'hide_empty' => false,
                ));

                if (!empty($terms) && !is_wp_error($terms)) {
                    foreach ($terms as $term) : ?>
                        <option <?= $term->term_id == $cat ? 'selected' : '' ?>
                                value="<?= $term->term_id ?>"><?= $term->name ?></option>
                    <?php endforeach;
                } ?>
            </select>

        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <label for="removeFolderByLink">
                Slug(s) to exclude from redirect search (optional)
            </label>
        </th>
        <td>
            <input type="text"
                   placeholder=" cat/"
                   name="removeFolderByLink"
                   id="removeFolderByLink"
                   value="<?php echo esc_attr(get_option('removeFolderByLink', '')); ?>">

        </td>
    </tr>
</table>