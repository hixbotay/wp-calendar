<?php
/**
 * @package 	FVN-extension
 * @author 		Vuong Anh Duong
 * @link 		http://freelancerviet.net
 * @copyright 	Copyright (C) 2011 - 2012 Vuong Anh Duong
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id$
 **/
defined('ABSPATH') or die;

class FvnActionAjax extends FvnAction{
	
	private function renderJson($data){
		// Use the correct json mime-type
	    header('Content-Type: application/json');	
	    // Change the suggested filename
	    header('Content-Disposition: attachment;filename="response.json"');
	    header('Access-Control-Allow-Origin: *');
		echo json_encode($data);
		exit;
	}
	function getPost(){
		global $wpdb;
		
		$category = $this->input->getString('category');
		$category = get_term_by( 'slug', 'hoc-sinh', 'category');
		$category = $category->term_id;
		$limit = $this->input->getInt('limit',4);
		$offset = $this->input->getInt('offset',0);
		$args = array (
				'numberposts' => $limit,
				'category_name' => 'hoc-sinh'
		);
		if($offset){
			$args['offset'] = $offset;
		}
// 		$post = get_posts($args);
		
		$query = "SELECT p.*,meta.meta_value as thumbnail from {$wpdb->prefix}posts as p 
		LEFT JOIN {$wpdb->prefix}postmeta as meta ON (meta.post_id = p.ID AND meta.meta_key LIKE '_thumbnail_id')
		LEFT JOIN {$wpdb->prefix}term_relationships as term ON (p.ID = term.object_id)  
		WHERE p.post_type='post' 
			AND p.post_status LIKE 'publish' 
			AND term.term_taxonomy_id = {$category} 
		GROUP BY p.ID ORDER BY p.post_date DESC";
		
		$post = $wpdb->get_results($query);
		debug($query);
		debug($post);
		
		echo json_encode($post);
		exit;
	}
	
	function getPostByCategory(){
		HBImporter::helper('post');
		$category_name = $this->input->getString('category_name');
		
		$limit = $this->input->getInt('limit');
		$offset = $this->input->getInt('offset');
		$posts = HBPostHelper::getPostByCategory($category_name,$limit,$offset);
		if(count($posts) == 0){
			$new_offset = $offset-1;
			if($new_offset < 0){
				$new_offset = 0;
			}
			$posts = HBPostHelper::getPostByCategory($category_name, 1, $new_offset);
		}
		
		$this->renderJson($posts);
	}
	
	function get_post_floor(){
		HBImporter::helper('post');
		$category_name = 'mat-bang';
		
		$limit = $this->input->getInt('limit');
		$offset = $this->input->getInt('offset');
		$posts = HBPostHelper::getPostByCategory($category_name,$limit,$offset);
		if(count($posts) == 0){
			$new_offset = $offset-1;
			if($new_offset < 0){
				$new_offset = 0;
			}
			$posts = HBPostHelper::getPostByCategory($category_name, 1, $new_offset);
		}
		foreach($posts as &$post){
			$post->link .= '?raw=1';
		}
		$this->renderJson($posts);
	}
	
	function count_post(){
		$post_id = $this->input->getInt('post_id');
		if($post_id)
			hb_set_post_view($post_id);
		exit;
	}
}
