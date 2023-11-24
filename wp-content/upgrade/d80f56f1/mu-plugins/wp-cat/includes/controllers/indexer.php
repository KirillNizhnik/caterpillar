<?php
namespace Cat\Controllers;

class Indexer
{
    protected static $instance;

    /**
     * Static Singleton Factory Method
     * @return Indexer single Indexer class
     */
    public static function instance()
    {
        if (!isset(self::$instance)) {
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }


    /**
     * Setup hooks to allow managing values through actions.
     * @return void
     */
    protected function __construct( )
    {
        add_action('cat_index_new_value', array( __CLASS__, 'save' ), 10, 5 );
        add_action('cat_index_update_value', array( __CLASS__, 'update' ), 10, 5 );
        add_action('cat_index_delete_value', array( __CLASS__, 'delete' ), 10, 2 );
        add_action('cat_index_post_delete_all', array( __CLASS__, 'delete_all' ), 10, 1 );
    }



    /**
     * Save a value to the index
     * @param  int    $post_id       id of the post
     * @param  string $param         param/property to compare
     * @param  string $value         values that is compared
     * @param  string $display_value what to visually show
     * @param  string $source        new or used
     * @return bool                  true/false saved to index
     */
    public static function save($post_id, $param, $value, $display_value, $source)
    {
        global $wpdb;
        $values = apply_filters( 'cat_index_before_save', array('value' => $value, 'display_value' => $display_value) );
        $values = apply_filters( 'cat_index_'.$param.'_before_save', $values );

        return $wpdb->insert(
            $wpdb->prefix . 'cat_search_index',
            array(
                'post_id' => $post_id
                ,'param_name' => $param
                ,'param_value' => $values['value']
                ,'param_display_value' => $values['display_value']
                ,'param_source' => $source
            ),
            array(
                '%d'
                ,'%s'
                ,'%s'
                ,'%s'
                ,'%s'
            )
        );
    }

    /**
     * Update a value in the index
     * @param  int    $post_id       id of the post
     * @param  string $param         param/property to compare
     * @param  string $value         values that is compared
     * @param  string $display_value what to visually show
     * @param  string $source        not used, here for easy integration
     * @return bool                  true/false updated value in index
     */
    public static function update($post_id, $param, $value, $display_value, $source='')
    {
        global $wpdb;
        $values = apply_filters( 'cat_index_before_update', array('value' => $value, 'display_value' => $display_value) );
        $values = apply_filters( 'cat_index_'.$param.'_before_update', $values );

        $updated = $wpdb->update(
            $wpdb->prefix . 'cat_search_index',
            array(
                 'param_value' => $values['value']
                ,'param_display_value' => $values['display_value']
            ),
            array(
                 'post_id' => $post_id
                ,'param_name' =>  $param
            ),
            array(
                 '%s'
                ,'%s'
            ),
            array( '%d', '%s' )
        );

        return $updated === 1;
    }

    /**
     * Remove a specific property from the index
     * @param  int    $post_id post id
     * @param  string $param   property to remove
     * @return bool            true/false deleted value from index
     */
    public static function delete($post_id, $param)
    {
        global $wpdb;

        return $wpdb->delete(
            $wpdb->prefix . 'cat_search_index',
            array(
                 'post_id' => $post_id
                ,'param_name' =>  $param
            ),
            array( '%d', '%s' )
        );
    }

    /**
     * Remove all properties for a post
     * @param  int    $post_id post id
     * @return bool            true/false deleted values from index
     */
    public static function delete_all( $post_id )
    {
        global $wpdb;

        return $wpdb->delete(
            $wpdb->prefix . 'cat_search_index',
            array( 'post_id' => $post_id ),
            array( '%d' )
        );
    }
}