<?php
/*
 * List of some type of data in database
 */
class HBSalonHelper{
	static $timeframe;
	static function get_salons($id=0){
		global $wpdb;
		$querystr = "
		SELECT * from {$wpdb->prefix}hairsalon_salons 
		";
		if(!empty($id)){
			if(is_array($id)){
				$querystr .= 'WHERE id IN ('.implode(',',$id).')';
			}else{
				$querystr .= "WHERE id={$id}";
			}
		}
		$result = $wpdb->get_results($querystr, OBJECT_K);
		
		return $result;
	}
	
	static function get_timeframes($id=0){
		if(!isset(static::$timeframe)){
			global $wpdb;
			$querystr = "
			SELECT * from {$wpdb->prefix}hairsalon_timeframes 
			";
			static::$timeframe = $wpdb->get_results($querystr,OBJECT_K);
		}
		if($id){
			return static::$timeframe[$id];
		}
		return static::$timeframe;
	}
	
	static function get_staff_by_salon($salon_id,$type=''){
		global $wpdb;
		$salon_id = (int)$salon_id;
		$querystr = "
		SELECT * from {$wpdb->prefix}hairsalon_staffs
		";
		$querystr .= "WHERE salon_id={$salon_id}";
		$result = $wpdb->get_results($querystr);
		
		return $result;
	}
	
}