<?php
class CM_Widget_Closest_Location extends WP_Widget {

    public function __construct() {
        $widget_ops = array(
            'classname' => 'wpcm_closest_location',
            'description' => 'Shows the user their closest location.'
        );
        parent::__construct( 'wpcm_closest_location', 'Closest Location', $widget_ops );
    }

    /**
     * Admin Form
     */
    public function form( $instance ) {

       $title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
       $prompt = isset( $instance['prompt'] ) ? esc_attr( $instance['prompt'] ) : '';
?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
            <small><em>This will show at the top of the widget.</em></small>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'prompt' ); ?>">Prompt Text</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'prompt' ); ?>" name="<?php echo $this->get_field_name( 'prompt' ); ?>" type="text" value="<?php echo $prompt; ?>" />
            <small><em>The text the user will click on to request their nearest location. Will default to "Show Closest Location" if left blank.</em></small>
        </p>
<?php
    }

    /**
     * Admin Form Update
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['prompt'] = strip_tags( $new_instance['prompt'] );
        return $instance;
    }

    /**
     * Widget Template Require
     */
    public function widget( $args, $instance ) {
        extract( $args );
        $before_widget = substr( $before_widget, 0, -1 ) .'">';
        CM_Template::$add_scripts = true;
        if ( empty( $instance['prompt'] ) ) {
            $instance['prompt'] = 'Show Closest Location';
        }
        if ( empty( $instance['title'] ) ) {
            $instance['title'] = 'Find a Location';
        }
        //wpcm_template( 'widget-closest-location', array( 'title' => $instance['title'], 'prompt' => $instance['prompt'], 'before_widget' => $before_widget, 'after_widget' => $after_widget ) );
        wpcm_template( 'widget-closest-location', array( 'title' => $instance['title'], 'prompt' => $instance['prompt'], 'before_widget' => $before_widget, 'after_widget' => $after_widget ) );
    }
}
