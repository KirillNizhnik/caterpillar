<?php

function default_search_values($field, $source='')
{
    $search = \Cat\Controllers\Search::instance();

    if( ! empty($search->params) ) {
        $params = $search->params;
    } else {
        $params = array(
            'fields' => array(),
            'selections' => array(),
            'where_clause' => ( ! empty($source) ) ? " AND param_source='{$source}' " : ''
        );
    }

    switch($field)
    {
        case 'source':

            $selected = ( isset($params['selections']['source']) )
                        ? $params['selections']['source']
                        : $source;

            $options = array('' => 'New & Used', 'new' => 'New', 'used' => 'Used');

            foreach($options as $value => $display)
            {
                $is_selected = (strtolower($selected) == $value) ? ' selected' : '';
                echo '<option value="'.$value.'"'.$is_selected.'>'.$display.'</option>';
            }
            break;

        default:
            $value = isset($_POST[$field]) ? sanitize_text_field($_POST[$field]) : '';
            $model = new $search->field_types[$field]($field, $value);

            if( $field == 'price' ) {
                $model->options = $search->options['price'];
            }

            if( $field == 'hours' ) {
                $model->options = $search->options['hours'];
            }

            echo $model->render($params);
            break;
    }
}


function output_search_options($values=array())
{
    $unique = array();
    foreach($values as $value => $display)
    {
        if(is_array($display) AND ! isset($unique[$display['value']]))
        {
            echo '<option value="'.$display['value'].'">'.$display['display'].'</option>';
            $unique[$display['value']] = $display['display'];
        }/*
        else
        {
            echo '<option value="'.$value.'">'.$display.'</option>';
        }*/
    }
}
