<?php
class FX_Popular_Posts_Widget extends WP_Widget {

	public function __construct() {
		$widget_ops = array(
			'classname'   => 'widget-popular-posts',
			'description' => 'A list of most-viewed posts.',
		);
		parent::__construct( 'fx_popular_posts_widget', 'Popular Posts', $widget_ops );
	}

	public function form( $instance ) {
		$title      = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number     = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$show_views = isset( $instance['show_views'] ) ? (bool) $instance['show_views'] : false;
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>">Number of posts to show:</label>
		<input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" /></p>
		<p><input class="checkbox" type="checkbox"<?php checked( $show_views ); ?> id="<?php echo $this->get_field_id( 'show_views' ); ?>" name="<?php echo $this->get_field_name( 'show_views' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_views' ); ?>">Display post views count?</label></p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance               = $old_instance;
		$instance['title']      = sanitize_text_field( $new_instance['title'] );
		$instance['number']     = (int) $new_instance['number'];
		$instance['show_views'] = isset( $new_instance['show_views'] ) ? (bool) $new_instance['show_views'] : false;
		return $instance;
	}

	public function widget( $args, $instance ) {
		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}
		$meta_key = FX_Popular_Posts()::$post_meta_key;
		$title      = ( ! empty( $instance['title'] ) ) ? $instance['title'] : '';
		$number     = ( ! empty( $instance['number'] ) && is_numeric( $instance['number'] ) && $instance['number'] > 0 ) ? $instance['number'] : 5;
		$show_views = isset( $instance['show_views'] ) ? $instance['show_views'] : false;
		$pp_query   = new WP_Query(
			array(
				'posts_per_page'      => $number,
				'no_found_rows'       => true,
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'meta_key'            => $meta_key,
				'orderby'             => 'meta_value',
			)
		);
		if ( ! $pp_query->have_posts() ) {
			return;
		}
		?>
		<?php echo $args['before_widget']; ?>
		<?php
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		?>
		<ul>
			<?php foreach ( $pp_query->posts as $p ) : ?>
				<?php
					$post_title = get_the_title( $p->ID );
					$title      = ( ! empty( $post_title ) ) ? $post_title : '(no title)';
					$view_count = get_post_meta( $p->ID, $meta_key, true );
				?>
				<li>
					<a href="<?php the_permalink( $p->ID ); ?>"><?php echo $title; ?></a>
					<?php if ( $show_views ) : ?>
						<span>(<?php echo $view_count; ?> View<?php echo ( $view_count > 1 ) ? 's' : ''; ?>)</span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
		echo $args['after_widget'];
	}
}

add_action( 'widgets_init', 'fx_register_popular_posts_widget' );
function fx_register_popular_posts_widget() {
	register_widget( 'FX_Popular_Posts_Widget' );
}
