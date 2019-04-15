<?php

/*
 * parent class for view of post type as list
 */
defined ( 'ABSPATH' ) or die ();
class HBPostTypeViewList{
	public $post_type;
	public function __construct($config = array('custom_column'=>1,'custom_filter'=>1)) {
		if(empty($this->post_type)){
			$this->post_type = 'HB_'.strtolower(substr(get_class($this), 23));
		}
		//add custom column
		if($config['custom_column']){
			add_filter( 'manage_edit-'.$this->post_type.'_columns', array( $this, 'add_columns' ) );
			add_action('manage_'.$this->post_type.'_posts_custom_column', array( $this, 'fill_columns' ), 10, 2);
		}
			
		//add custom filter
		if($config['custom_filter']){
			add_action( 'restrict_manage_posts', array( $this, 'restrict_manage_posts' ) );
			add_filter( 'parse_query', array( $this, 'filters_query' ) );
		}
		$this->hook();
	}
	
	//override hook function before display
	public function hook(){
		
	}
		
	//abtract function to add more column to list
	public function add_columns($columns){
		return $columns;
	}
	
	//abtract function to fill in custom column
	public function fill_columns($column_name, $id) {
		
	}
	
	/**
	 * Filters for post types.
	 */
	public function restrict_manage_posts() {
		return;
	}
	
	public function filters_query($query){
		return;
	}
	
	
	
	public function debugQuery(){
		add_filter( 'posts_request', array( $this, 'dump_request' ) );
	}
	//debug query
	public function dump_request( $input ) {
		global $wp_query;
		debug($wp_query->request);
		//var_dump($input);
	
		//return $input;
	}
	
}