<?php 
class HBPostHelper{
	static function getPostByCategory($category_name,$limit = 4, $offset = 0){
		$posts = get_posts ( array (
				'numberposts' => $limit,
				'category_name' => $category_name,
				'offset'=>$offset
		) );
		
		$result = array();
		foreach($posts as $post){			
			$post_thumbnail_id = get_post_thumbnail_id( $post );
			$result[] = (object)array(
				'ID' => $post->ID,
				'link'=>$post->guid,
				'title'=>$post->post_title,
				'desc' => $post->post_content,
				'excerpt'=>$post->post_excerpt,
				'image'=> wp_get_attachment_image_url( $post_thumbnail_id, 'medium', false ),
				'image_max' => wp_get_attachment_image_url( $post_thumbnail_id, 'large', false ),
				'post_date_gmt' => $post->post_date_gmt
			);
			
		}
		$posts = null;
		unset($posts);
		return $result;
	}
	
}

?>