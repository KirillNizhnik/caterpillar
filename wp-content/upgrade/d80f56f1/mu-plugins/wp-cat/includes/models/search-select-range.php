<?php
namespace Cat\Models;

class Search_Select_Range extends Search_Field
{
    public $name           = '';
    public $selected_value = '';
    public $ids            = array();
    public $values         = array();
    public $options        = array();

    public function __construct($field_name, $selected_value='', $options=array())
    {
        $this->name = $field_name;
        $this->selected_value = $selected_value;
        $this->options = $options;
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

        usort($results, function( $a, $b ) {
            return $a['value'] == $b['value'] ? 0 : (( $a['value'] > $b['value'] ) ? 1 : -1);
        });


        $this->values  = $this->values_for_range( $results, $this->options);
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

        $selected_values = explode('-', $this->selected_value);
        $where_clause    = $params['where_clause'];

        if( ! empty($selected_values[0]) )
        $where_clause .= " AND param_value >= {$selected_values[0]}";

        if( ! empty($selected_values[1]) AND $selected_values[1] != 999999 )
            $where_clause .= " AND param_value <= {$selected_values[1]}";

        $sql = "
        SELECT DISTINCT post_id FROM {$wpdb->prefix}cat_search_index
        WHERE param_name = '{$this->name}' $where_clause";
        return $wpdb->get_col( $sql );

    }

}
