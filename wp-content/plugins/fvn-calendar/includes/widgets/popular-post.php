<?php
/**
 * Widget API: HB_Widget_Popular_Posts class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 1.0.0
 */

/**
 * Core class used to implement a Recent Posts widget.
 *
 * @since 2.8.0
 *
 * @see WP_Widget
 */
class HB_Widget_Popular_Posts extends WP_Widget {

	/**
	 * Sets up a new Recent Posts widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'widget_popular_entries',
			'description' => __( 'Your site&#8217;s most popular Posts.' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'popular-posts', __( 'Popular Posts' ), $widget_ops );
	}

	public function widget( $args, $instance ) {
		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		
		if($instance['condition'] == 'featured'){
			$r = new WP_Query( array( 'posts_per_page' => $number, 'meta_key' => 'post_views_count', 'orderby' => 'meta_value_num', 'order' => 'DESC'  ) );
		}else{
			$r = new WP_Query( array( 'posts_per_page' => $number, 'meta_key' => 'post_views_count', 'orderby' => 'meta_value_num', 'order' => 'DESC'  ) );
			if(is_single()){
			?>
			<script>jQuery(document).ready(function($){
				$.ajax({
					url: '<?php echo site_url()?>/index.php?fvnaction=ajax&task=count_post&post_id=<?php echo get_the_ID()?>',
					dataType: "html",
					async: !1
				 }).responseText;
			})</script>
			<?php }
		}
		
		
		
		if ($r->have_posts()){
		
		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Popular Posts' );
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		$i = 2;
		?>
		<?php echo $args['before_widget']; ?>
		<?php if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		} ?>
		
		<?php while ( $r->have_posts() ) : $r->the_post();
			 $post_thumbnail_id = get_post_thumbnail_id( get_the_id() );
			 $image = wp_get_attachment_image_url( $post_thumbnail_id, 'thumbnail', false );
			 ?>
			 <div class="item row">
				<a href="<?php the_permalink(); ?>"><div class="item-img col-md-3"><img class="avatar" src="<?php echo $image?>"/></div>
				<div class="item-title col-md-9"><?php the_title() ?></div></a>
			</div>
		<?php $i++;
			endwhile; ?>
	
		<?php echo $args['after_widget']; ?>
		<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		}
	}

	/**
	 * Handles updating the settings for the current Recent Posts widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['number'] = (int) $new_instance['number'];
		$instance['condition'] = $new_instance['condition'];
		return $instance;
	}

	/**
	 * Outputs the settings form for the Recent Posts widget.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'condition' ); ?>"><?php _e( 'Condition:' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'condition' ); ?>" name="<?php echo $this->get_field_name( 'condition' ); ?>">
				
				<option value="view" <?php echo $instance['condition']=='view' ?'selected="selected"' :''; ?>>View</option>
				<option value="featured" <?php echo $instance['condition']=='featured' ?'selected="selected"' :''; ?>>Featured</option>
			</select>
		</p>

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
		<input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" /></p>

<?php
	}
}
