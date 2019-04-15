<?php
/*
 * List of some type of data in database
 */
class HBEstateHelper{
	static $host;
	
	static function get_host($id=0){
		if (!isset(self::$host)){
			global $wpdb;
			$querystr = "
			SELECT * from {$wpdb->posts}.estate_hosts 
			";
			if(!empty($id)){
				if(is_array($id)){
					$querystr .= 'WHERE id IN ('.implode(',',$id).')';
				}else{
					$querystr .= "WHERE id={$id}";
				}
			}
			self::$host = $wpdb->get_results($querystr, OBJECT_K);
		}
		
		return self::$host;
	}
	
	static function get_estate_images($id=0){
		global $wpdb;
		$querystr = "
		SELECT * from {$wpdb->posts}.estate_image 
		";
		if(!empty($id)){
			if(is_array($id)){
				$querystr .= 'WHERE estate_id IN ('.implode(',',$id).')';
			}else{
				$querystr .= "WHERE estate_id={$id}";
			}
		}
		$result = $wpdb->get_results($querystr, OBJECT_K);
		
		return $result;
	}
	
}