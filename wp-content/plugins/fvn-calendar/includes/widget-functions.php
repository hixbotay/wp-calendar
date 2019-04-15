<?php
/**
 * Widget functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Register Widgets.
 */
function HB_register_widgets() {
	// Include widget classes.	
	include_once( 'widgets/class-widget.php' );
	include_once( 'widgets/class-widget-search.php' );
	include_once( 'widgets/class-register-form.php' );
	include_once( 'widgets/class-category-post.php' );
	include_once( 'widgets/popular-post.php' );
	register_widget( 'HB_Widget_Search' );
	register_widget( 'HbRegisterForm_Widget' );
	register_widget( 'HB_Widget_Posts_Category' );
	register_widget( 'HB_Widget_Popular_Posts' );
}
add_action( 'widgets_init', 'HB_register_widgets' );