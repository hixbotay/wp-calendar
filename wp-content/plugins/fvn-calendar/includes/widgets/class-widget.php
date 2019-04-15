<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Display form search route
 *
 */
class HB_Widget extends WP_Widget {

	
	function form($instance){
		
		if(empty($this->form_path)){
			echo __('Form path file is not found!','hb');
			return false;
		}
		
		HBImporter::libraries('form');
		$form = new HBForm($this->id_base);		
		$form->loadFile($this->form_path);
		//bind data to form
		$form->bind($instance);
		echo $form->renderFieldset('params',array(
				'name'	=> $this->get_field_name('%s'),
				'id'	=> $this->get_field_id('%s')
		));
	}
	
	// update widget
	function update($new_instance, $old_instance) {
		$instance = array();
		foreach ($new_instance as $key=>$val){
			$instance[$key] = strip_tags($val) ;
		}
		return $instance;
	}
	
	public function output( $args, $instance ){
	}
	/**
	 * Output widget.
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		$this->output( $args, $instance );
		echo $args['after_widget'];
		return;
	}
}
