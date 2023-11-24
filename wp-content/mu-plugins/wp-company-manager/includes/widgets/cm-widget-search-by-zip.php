<?php

class CM_Widget_Search_By_Zip extends WP_Widget
{

    public function __construct()
    {
        $widget_ops = array(
            'classname' => 'wpcm_search_by_zip'
            ,'description' => __( 'Give the user the option to search for locations by zip.')
        );

        parent::__construct('wpcm_search_by_zip', __('Location Search by Zip'), $widget_ops);
    }



    /**
     * Form
     * Displays the form for our widget in the admin
     * @param [array] $instance Previously saved values from database.
     */

    public function form($instance)
    {
       $title = isset($instance['title'])  ? esc_attr($instance['title']) : '';
       $page  = isset($instance['result_page'])  ? esc_attr($instance['result_page']) : '';
       ?>

        <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?></label>
          <input class="widefat"
                 id="<?php echo $this->get_field_id('title'); ?>"
                 name="<?php echo $this->get_field_name('title'); ?>"
                 type="text"
                 value="<?php echo $title; ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('result_page'); ?>"><?php _e('Results Page'); ?></label>
            <?php
                wp_dropdown_pages( array(
                     'name'     => $this->get_field_name('result_page')
                    ,'id'       => $this->get_field_id('result_page')
                    ,'selected' => $page
                ));
            ?>
        </p>

        <?php
    }



    /**
     * Update
     * Function that updates the wdiget html on the frontend
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     * @return array Updated safe values to be saved.
     */

    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['result_page'] = $new_instance['result_page'];

        return $instance;
    }



    /**
     * Widget
     * Function that shows the widget html on the frontend
     * @param [array] $args     Widget arguments.
     * @param [array] $instance Saved values from database.
     */

    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);
        $page  = isset( $instance['result_page'] ) ? $instance['result_page'] : 21;
        extract( $args );

        CM_Template::$add_scripts = true;
        wpcm_template('widget-search-by-zip', array('title' => $title, 'action' => get_permalink( $page ), 'before_widget' => $before_widget, 'after_widget' => $after_widget));
    }
}

