<?php

/**
 * @package 	FVN-extension
 * @author 		Joombooking
 * @link 		http://http://woafun.com/
 * @copyright 	Copyright (C) 2011 - 2012 Vuong Anh Duong
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
defined('ABSPATH') or die('Restricted access');

class HBCoupon extends HBObject{  
	public $id;
	public $data;
	public $post;
	
	public function __construct($code = null) {
		if($code){
			$this->load($code);
		}
		
	}
	
	public function load($code){
		global $wpdb;
		$post = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE post_title = '" . $code . "'",OBJECT);
		if($post->ID){
			$this->id = $post->ID;
			$this->data = json_decode($this->data->post_content);
			$this->post = $post; 
			return $post;
		}else{
			return false;
		}
	}	
	
	public function update(){
		if(!$this->id){
			return false;
		}
		$this->post->post_content = json_encode($this->data);
		return wp_update_post($this->post);
	}
	
}
?>