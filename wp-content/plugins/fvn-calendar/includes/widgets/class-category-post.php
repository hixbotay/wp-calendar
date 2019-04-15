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
class HB_Widget_Posts_Category extends WP_Widget {

	/**
	 * Sets up a new Recent Posts widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'widget_category_posts_entries',
			'description' => __( 'Posts on a category' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'hb-category-posts', __( 'HBPRO Category Posts' ), $widget_ops );
	}

	public function widget( $args, $instance ) {
		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}
		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 10;
		$r = new WP_Query( array( 
			'posts_per_page' => $number, 
			'post_type' => 'post',
			//'category' => (int)$instance['category'],
			'category_name' => $instance['category'],
			'post_status'  => 'publish',
			'orderby' => 'ID', 
			//'offset' => 1,
			'order' => 'DESC' 
		) );
		//debug($r);
		if ($r->have_posts()) :
		
		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : '';
		
		$i = 2;
		?>
		<?php echo $args['before_widget']; ?>
		<?php if ( $title ) {
			$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
			echo $args['before_title'] . $title . $args['after_title'];
		} ?>
		<section id="slider">
		<div class=" ">
		<div class="slide-banner" id="slider-menu">
		
		<?php switch ($instance['style']){
			case 'slideshow':
				wp_enqueue_script( 'owl_carousel', FVN_URL.'assets/js/owl.carousel.min.js',array('jquery'));
				wp_enqueue_style( 'owl_carousel',  FVN_URL.'assets/css/owl.carousel.min.css',array());
				//debug($r->the_post());
				echo '<div class="hbpro-widget-post-galery">';
				$i = 0;
				while ( $r->have_posts() ) : $r->the_post();
				$post_thumbnail_id = get_post_thumbnail_id( get_the_id() );
				$image = wp_get_attachment_image_url( $post_thumbnail_id, 'thumbnail', false );
				$image_max = wp_get_attachment_image_url( $post_thumbnail_id, 'largest', false );
				$thumbnail[] = '<li onmousemove="jQuery(\'.hbpro-widget-post-galery\').trigger(\'to.owl.carousel\', '.$i.');">
									<div class="slider-image-thumb"><img src="'.$image.'"/></div>
									<a href="'.get_the_permalink().'"><div class="slider-detail">
											<span class="title-image">'.get_the_title().'</span>
											<span class="description-image">'.get_the_excerpt().'</span>
										</div></a>
								</li>';
				?>
					<div class="item insScroll">
						<a href="<?php the_permalink()?>">
							<img class="insImageload" src="<?php echo $image_max?>"/>						
						</a>
					</div>
				<?php $i++;
				endwhile; 
				echo '</div>';
				echo '<div class="slider-thumb hidden-sm hidden-xs"><ul id="slider-thumb">'.implode('',$thumbnail).'</ul></div>';
				?>
				
				<script>
						jQuery(document).ready(function($){
							$('.hbpro-widget-post-galery').owlCarousel({
								slideSpeed : 300,
								autoPlay: 3000,
								lazyLoad: true,
								loop: true,
								paginationSpeed : 400,
								transitionStyle:'fade',
								autoHeight : '500',
								autoWidth: false,
								nav: false,
								pagination: true,
								items: 1,
								itemsCustom : false,
								itemsDesktop : [1400,1],
								itemsDesktopSmall : [980,1],
								itemsTablet: [768,1],
								itemsTabletSmall: false,
								itemsMobile : [479,1],
								afterAction: function(el){
								   this.$owlItems.removeClass('active');	
								   this.$owlItems.eq(this.currentItem).addClass('active');    
								} 
							});
						});
						
					</script>
				<?php 
				break;
			case 'list_slideshow':
				?>
				<script>
					jQuery(document).ready(function($){
						$('#floor_slideshow').wfslideshow({
							url: siteURL+'/index.php?fvnaction=ajax&task=get_post_floor', 
							classItem: 'item-opacity item-news',
							classAvatar: 'avatar-item-news',
							classTitle: 'title-item-news',
							classMore: '',
							itemPhone: 2,
							itemDesktop: 4,
							itemTablet: 3
						});
						
					});
				</script>
				<?php 
				break;
			default:
				while ( $r->have_posts() ) : $r->the_post();
				$post_thumbnail_id = get_post_thumbnail_id( get_the_id() );
				$image = wp_get_attachment_image_url( $post_thumbnail_id, 'thumbnail', false );
				?>
					<div class="item">
						<a href="<?php the_permalink(); ?>">
							<div class="item-img"><img class="avatar" src="<?php echo $image?>"/></div>
							<?php the_title() ?>
						</a>
					</div>
				<?php $i++;
				endwhile; 
				break;
		}?>
		</div>
		</div>
		</section>
		<?php echo $args['after_widget']; ?>
		<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		endif;
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
		foreach($new_instance as $key => $val){
			$instance[$key] = $val;
		}		
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
		$category     = isset( $instance['category'] ) ? ( $instance['category'] ) : '';
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$style    = isset( $instance['style'] ) ? $instance['style'] : 0;
		$wp_cats = get_categories();
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
		
		<p><label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e( 'category:' ); ?></label>		
		<select class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'category' ); ?>">
		<?php foreach($wp_cats as $cat){?>
			<option value="<?php echo $cat->cat_name?>" <?php echo $cat->cat_name==$category ? 'selected="selected"' : ''?>><?php echo $cat->name?></option>
		<?php }?>
		</select>
		</p>
		
		<p><label for="<?php echo $this->get_field_id( 'style' ); ?>"><?php _e( 'style' ); ?></label>		
		<select class="widefat" id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>">
		<option value="1" <?php echo $style==0 ? 'selected="selected"' : ''?>>Normal</option>
		<option value="slideshow" <?php echo $style=='slideshow' ? 'selected="selected"' : ''?>>Slideshow</option>
		<option value="list_slideshow" <?php echo $style=='list_slideshow' ? 'selected="selected"' : ''?>>List slideshow</option>
		<option value="list_slideshow2" <?php echo $style=='list_slideshow2' ? 'selected="selected"' : ''?>>List slideshow2</option>
		</select>
		</p>
		
		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
		<input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" /></p>

<?php
	}
	
	
}
