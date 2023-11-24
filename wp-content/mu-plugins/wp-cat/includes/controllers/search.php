<?php
namespace Cat\Controllers;

class Search
{
    protected static $instance;

    public $output;
    public $params = array();
    public $values = array();
    public $filtered_post_ids = array();

    /**
     * Initializes plugin variables and sets up WordPress hooks/actions.
     *
     * @return void
     */

    protected function __construct()
    {
        add_action('wp_ajax_equipment_search_fields', array($this, 'refresh') );
        add_action('wp_ajax_nopriv_equipment_search_fields', array($this, 'refresh') );

        add_action('wp_ajax_equipment_search_refresh', array($this, 'refresh') );
        add_action('wp_ajax_nopriv_equipment_search_refresh', array($this, 'refresh') );

        add_filter('equipment_search_dependent_fields', array($this, 'reset_dependent_fields'), 10, 2 );
        add_filter('wpseo_title', array($this, 'fix_search_title'), 99);

        $this->output = new \stdClass();
        $this->field_types = array(
            'family' => '\Cat\Models\Search_Select',
            'price' => '\Cat\Models\Search_Select_Range',
            'hours' => '\Cat\Models\Search_Select_Range',
            'year' => '\Cat\Models\Search_Select',
            'model' => '\Cat\Models\Search_Select',
            'manufacturer' => '\Cat\Models\Search_Select',
        );

        $this->options = array(
            'price' => array(
                array(
                    'min' => 0
                    ,'max' => 49999
                    ,'display' => '$49,999 and below'
                ),
                array(
                    'min' => 50000
                    ,'max' => 99999
                    ,'display' => '$50,000 - $100,000'
                ),
                array(
                    'min' => 100000
                    ,'max' => 149999
                    ,'display' => '$100,000 - $150,000'
                ),
                array(
                    'min' => 150000
                    ,'max' => 199999
                    ,'display' => '$150,000 - $200,000'
                ),
                array(
                    'min' => 200000
                    ,'max' => 249999
                    ,'display' => '$200,000 - $250,000'
                ),
                array(
                    'min' => 250000
                    ,'max' => 299999
                    ,'display' => '$250,000 - $200,000'
                ),
                array(
                    'min' => 300000
                    ,'max' => 3499999
                    ,'display' => '$300,000 - $350,000'
                ),
                array(
                    'min' => 350000
                    ,'max' => 399999
                    ,'display' => '$350,000 - $400,000'
                ),
                array(
                    'min' => 400000
                    ,'max' => 449999
                    ,'display' => '$400,000 - $450,000'
                ),
                array(
                    'min' => 450000
                    ,'max' => 499999
                    ,'display' => '$450,000 - $500,000'
                ),
                array(
                    'min' => 500000
                    ,'max' => 549999
                    ,'display' => '$500,000 - $550,000'
                ),
                array(
                    'min' => 550000
                    ,'max' => 599999
                    ,'display' => '$550,000 - $600,000'
                ),
                array(
                    'min' => 600000
                    ,'max' => 649999
                    ,'display' => '$600,000 - $650,000'
                ),
                array(
                    'min' => 650000
                    ,'max' => 699999
                    ,'display' => '$650,000 - $700,000'
                ),
                array(
                    'min' => 700000
                    ,'max' => 749999
                    ,'display' => '$700,000 - $750,000'
                ),
                array(
                    'min' => 750000
                    ,'max' => 9999999
                    ,'display' => '$750,000 and above'
                )
            ),
            'hours' => array(
                array(
                    'min' => 0
                    ,'max' => 500
                    ,'display' => 'Under 500'
                ),
                array(
                    'min' => 0
                    ,'max' => 1000
                    ,'display' => 'Under 1000'
                ),
                array(
                    'min' => 0
                    ,'max' => 2000
                    ,'display' => 'Under 2000'
                ),
                array(
                    'min' => 0
                    ,'max' => 3000
                    ,'display' => 'Under 3000'
                ),
                array(
                    'min' => 0
                    ,'max' => 5000
                    ,'display' => 'Under 5000'
                ),
                array(
                    'min' => 5000
                    ,'max' => 999999
                    ,'display' => '5000 +'
                ),
            )

        );

        $action = isset( $_POST['action'] ) ? $_POST['action'] : '';

        if ( 'equipment_search_post' === $action ) {
            $this->params = $this->process_post_data();
            add_action( 'pre_get_posts', array( $this, 'update_query_vars' ), 999 );
            //add_action( 'wp_footer', array( $this, 'inject_template' ), 0 );
        }
    }


    /**
     * Singleton factory Method
     * Forces that only on instance of the class exists
     *
     * @return $instance Object, Returns the current instance or a new instance of the class
     */

    public static function instance()
    {
        if (!isset(self::$instance)) {
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }


    public function update_query_vars( $query )
    {
        // Only run once
        if ( isset( $this->query_vars ) ) {
            return;
        }

        if ( ! $query->is_main_query() ) {
            return;
        }


        // Store the default WP query vars
        $this->query_vars = $query->query_vars;

        $params = $this->process_post_data();

        $params = $this->get_filtered_post_ids( $params );

        $this->params = $params;

        $query->set('post_type', 'cat_used_machine');
        $query->set('post__in', $this->filtered_post_ids);
        $query->set('posts_per_page', 999999);

        // $this->output->template = $this->render( $params );
        // return $query;
    }


    public function inject_template()
    {
        ?>
        <script id="search-inject" type="text/javascript">
            var CSE =  CSE || {};

            CSE.Posted = JSON.parse('<?php echo json_encode( $_POST ); ?>');
        </script>
        <?php
    }



    public function process_post_data()
    {
        $fields   = stripslashes_deep( $_POST );
        $modified = '';

        $params = array(
            'fields' => array(),
            'selections' => array()
        );

        foreach($fields as $name => $value)
        {
            $skip = array(
                'ga_count' => ''
                ,'ga_id' => ''
                ,'action' => ''
            );

            if( isset($skip[$name]) )
                continue;

            if( isset($skip[$field->name]) )
                continue;

            if( $name === 'source' ) {
                // Process used or new source type
                $source = strtolower($value);
                $params['where_clause'] = (! empty($source) ) ? " AND param_source='{$source}' " : '';
                continue;
            }

            $params['fields'][$name] = new $this->field_types[$name]($name, $value);

            if( $name == 'price' ) {
                $params['fields']['price']->options = $this->options['price'];
            }

            if( $name == 'hours' ) {
                $params['fields']['hours']->options = $this->options['hours'];
            }
        }

        return $params;
    }

    public function process_ajax_post_data()
    {
        $data     = stripslashes_deep( $_POST['data'] );
        $fields   = json_decode( $data['current'] );
        $last     = json_decode( $data['previous'] );

        $params = array(
            'fields' => array(),
        );

        foreach($fields as $field)
        {
            $skip = array(
                'ga_count' => ''
                ,'ga_id' => ''
                ,'action' => ''
            );

            if( isset($skip[$field->name]) )
                continue;

            if( $field->name === 'source' ) {
                // Process used or new source type
                $source = strtolower($field->value);
                $params['where_clause'] = (! empty($source) ) ? " AND param_source='{$source}' " : '';
                continue;
            }

            $params['fields'][$field->name] = new $this->field_types[$field->name]($field->name, $field->value);

            if( $field->name == 'price' ) {
                $params['fields']['price']->options = $this->options['price'];
            }

            if( $field->name == 'hours' ) {
                $params['fields']['hours']->options = $this->options['hours'];
            }
        }


        if(isset($_POST['sort'])) {
            $params['sort'] = $_POST['sort'];
        }

        $params['modified'] = isset($_POST['changed']) ? $_POST['changed'] : '';

        return $params;
    }


    /**
     * The AJAX search refresh handler
     */
    public function refresh()
    {
        $action = isset( $_POST['action'] ) ? $_POST['action'] : '';

        $output = new \stdClass();
        $output->values = array();

        $params = $this->process_ajax_post_data();
        $params = $this->get_filtered_post_ids( $params );

        if( $action === 'equipment_search_refresh' ){
            $output->data = $this->render( $params );
        }

        // grab options for fields
        foreach($params['fields'] as $field_name => $field)
        {
            $output->values[$field_name] = $field->render($params);
        }

        $data = stripslashes_deep( $_POST['data'] );

        wp_send_json(apply_filters( 'equipment_search_ajax_response', $output, array(
            'data' => $data
        )));
    }



    public function get_filtered_post_ids( $params )
    {
        $this->values = array();

        $matches = array();

        foreach($params['fields'] as $field_name => $field)
        {

            $field->ids = $field->filter_posts($params);

            if( empty($field->ids) )
                continue;

            $this->filtered_post_ids = ( empty($this->filtered_post_ids) ) ? $field->ids : array_intersect( $this->filtered_post_ids, $field->ids );
        }

        return $params;
    }



    public function render($params)
    {

        ob_start();
        $args = array(
            'post_type' => 'cat_used_machine',
            'post__in'  => $this->filtered_post_ids,
            'posts_per_page' => -1
        );

        if(isset($params['sort'])) {
            $args['meta_key'] = $params['sort']['field'];
            $args['orderby']  = 'meta_value_num';
            $args['order']    = $params['sort']['order'];
        } else {
            $args['meta_key'] = 'price';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'ASC';
        }

        $used = new \WP_Query($args);
        $models = array();

        foreach($used->posts as $post) {
            $model = CAT()->product($post);
            $data = array();

            $data['url'] = get_permalink($model->id );
            $data['image'] = '<img src="'.$model->images[0]->src.'" alt="'.$model->title.'" class="img-responsive" />';
            $data['title'] = $model->title;
            $data['year'] = $model->year;
            $data['rating'] = $model->rating;
            $data['manufacturer'] = $model->manufacturer->name;
            $data['model'] = $model->model;
            $data['hours'] = $model->hours;
            $data['serialnumber'] = $model->serial_number;
            $data['price'] = '$' . number_format( $model->price, 2 );

            $models[] = $data;
        }


        return $models;
    }





    /**
     * Sets up results data of a query
     * @param  array $results rows of wpdb query
     * @return [array          the results
     */
    private function process_query_results($results)
    {
        $ids = array(
            'new'   => array()
            ,'used' => array()
        );

        $values = array();

        // push values from results
        if( ! empty($results) ) {
            foreach($results as $result) {
                $ids[$result->param_source][] = $result->post_id;
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
    private function values_for_range($values, $available_options)
    {
        $return = array();

        foreach($values as $value => $display)
        {
            foreach($available_options as $option)
            {
                if( $value >= $option['min'] AND $value <= $option['max'])
                {
                    $return[$option['min'].'-'.$option['max']] = $option['display'];
                    continue;
                }
            }
        }

        return $return;
    }



    public function reset_dependent_fields($fields, $field)
    {
        $values = array();

        switch ($field) {
            case 'source':
                $values = array('family', 'price', 'hours');
                break;

            case 'family':
                $values = array('price', 'hours');
                break;

            case 'price':
                $values = array('hours');
                break;
        }

        foreach($values as $value) {
            $fields[$value] = '';
        }

        return $fields;
    }


    private function remove_duplicate_values($input)
    {
        $serialized = array_map('serialize', $input);
        $unique = array_unique($serialized);
        return array_intersect_key($input, $unique);
    }

    public function fix_search_title($title)
    {
        if( is_equipment_search() )
            $title = 'Product Search Results';

        return $title;
    }


}

Search::instance();