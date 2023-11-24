<?php
namespace Cat\Models;

class Search_Select extends Search_Field
{
    public $name           = '';
    public $selected_value = '';
    public $ids            = array();
    public $values         = array();

    public function __construct($field_name, $selected_value='')
    {
        $this->name = $field_name;
        $this->selected_value = $selected_value;
    }


    function load_values( $params )
    {
        global $wpdb;

        $where_clause = $params['where_clause'];
        $post_ids     = array();

        // Preserve the original fields
        $or_values = $params['fields'];

        // drop field we are in
        unset( $or_values[ $this->name ] );

        // only search within post ids that match across all other fields.
        $counter = 0;
        foreach ( $or_values as $name => $field ) {
            if( empty($field->ids) )
                continue;

            $post_ids = ( 0 === $counter ) ? $field->ids : array_intersect( $post_ids, $field->ids );
            $counter++;
        }

        if( ! empty( $post_ids ) )
            $where_clause = ' AND post_id IN (' . implode( ',', $post_ids ) . ')';

        $results = $wpdb->get_results(
            "SELECT post_id, param_value as value, param_display_value as display
            FROM {$wpdb->prefix}cat_search_index
            WHERE param_name = '{$this->name}' $where_clause
            GROUP BY param_value", ARRAY_A
        );

        // set up variables for filtering
        //$results = $this->process_query_results($query);

        // sort the values alphbetically
        usort($results, function($a, $b) {
            return strcmp($a['value'], $b['value']);
        });

        $this->values  = $results;
        $this->query   = $wpdb->last_query;

        return $this;
    }


    public function render($params)
    {
        $output = '';
        $output .= '<option value="">All '.ucwords(pluralize($this->name)).'</option>';

        if(empty($this->values))
            $this->load_values($params);

        foreach($this->values as $row)
        {
            $selected = $row['value'] === $this->selected_value ? ' selected' : '';
            $output .= '<option value="'.$row['value'].'" '.$selected.' >' . esc_attr( $row['display'] ) . '</option>';
        }

        return $output;
    }


    /**
     * Filter the query based on selected values
     */
    public function filter_posts( $params )
    {
        global $wpdb;

        $where_clause = $params['where_clause'];

        if( ! empty($this->selected_value) )
            $where_clause .= " AND param_value = '$this->selected_value'";

        $sql = "
        SELECT DISTINCT post_id FROM {$wpdb->prefix}cat_search_index
        WHERE param_name = '{$this->name}' $where_clause";
        $this->ids = $wpdb->get_col( $sql );
        return $wpdb->get_col( $sql );
    }


}