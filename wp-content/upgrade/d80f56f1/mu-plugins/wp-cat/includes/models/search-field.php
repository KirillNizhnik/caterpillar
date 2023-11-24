<?php
namespace Cat\Models;

class Search_Field
{
    /**
     * Return Search results for fields with Equals opperator
     * @param  string $field     name of field to search
     * @param  string $value     value of field to search
     * @param  string $and_where additional where parameters
     * @return array             results of the field
     */
    protected function field_equals($field, $value, $and_where)
    {

        $sql = "SELECT * FROM {$this->table} WHERE param_name = '{$field}'";

        if( ! empty($value) )
            $sql .= " AND param_value = '{$value}'";

        $sql .= $and_where;


        $query = $this->wpdb->get_results($sql);

        // set up variables for filtering
        $results = $this->process_query_results($query);

        return array(
            'ids' => array_merge($results['new'], $results['used'])
            ,'new'     => $results['new']
            ,'used'    => $results['used']
            ,'values'  => $results['values']
            ,'query'   => $this->wpdb->last_query
        );
    }


    protected function field_range($field, $min="", $max=999999, $and_where)
    {
        $sql = "SELECT * FROM {$this->table} WHERE param_name = '{$field}'";

        if( ! empty($min) )
            $sql .= " AND param_value >= '{$min}'";

        if($max != 999999 AND $max != '' )
            $sql .= " AND param_value <= '{$max}'";

        $sql .= $and_where;

        $query = $this->wpdb->get_results($sql);

        // set up variables for filtering
        $results = $this->process_query_results($query);

        return array(
            'ids' => array_merge($results['new'], $results['used'])
            ,'new'     => $results['new']
            ,'used'    => $results['used']
            ,'values'  => $results['values']
            ,'query'   => $this->wpdb->last_query
        );
    }

    /**
     * Sets up results data of a query
     * @param  array $results rows of wpdb query
     * @return [array          the results
     */
    protected function process_query_results($results)
    {
        $ids = array(
            'new'   => array()
            ,'used' => array()
        );

        $values = array();

        // push values from results
        if( ! empty($results) ) {
            foreach($results as $result) {
                $values[] = array(
                    'post_id' => $result->post_id,
                    'value'   => $result->param_value,
                    'display' => $result->param_display_value
                );
            }
        }

        return array(
            'new'     => array_unique($ids['new'])
            ,'used'   => array_unique($ids['used'])
            ,'values' => $values
        );
    }


    /**
     * Returns the matching range options for a list
     * @param  array $values            single item values
     * @param  array $available_options the range options to test
     * @return array                    resulting array of range values
     */
    protected function values_for_range($values, $available_options)
    {
        $return = array();

        foreach($values as $result)
        {
            foreach($available_options as $option)
            {
                if( $result['value'] >= $option['min']
                    AND $result['value'] <= $option['max']
                    //AND ! isset($return[$option['min'].'-'.$option['max']])
                ){
                    $return[$option['min'].'-'.$option['max']] = array(
                        'post_id'  => $result['post_id']
                        ,'value'   => $option['min'].'-'.$option['max']
                        ,'display' => $option['display']
                    );
                    continue;
                }
            }
        }


        return array_values($return);
    }
}