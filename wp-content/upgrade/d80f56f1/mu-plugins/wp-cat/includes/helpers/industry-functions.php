<?php

function get_industry_family_list($post_id='')
{
    global $wpdb;
    $post_id = get_cat_active_post_id($post_id);

    $results = $wpdb->get_col(
        " SELECT cat_term_id"
    );
}

function industry_family_list($post_id='')
{
    $post_id = get_cat_active_post_id($post_id);
}

function get_industry_application_list($post_id='')
{
    $post_id = get_cat_active_post_id($post_id);
}