<?php

/*
 * parent class for view of post type as list
 */
defined ( 'ABSPATH' ) or die ();
class HBPostTypeViewMetabox{
	/**
	 * Update post data to table post
	 * @param unknown $post
	 */
	static function update_post($post){
		global $wpdb;
		$id= $post['ID'];
		unset($post['ID']);
		return $wpdb->update($wpdb->posts, $post,array("ID"=>$id));
	}
	
}