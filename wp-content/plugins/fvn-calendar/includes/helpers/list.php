<?php
/*
 * List of some type of data in database
 */
class HBList{
	static $countries;
	static $destinations;
	static $vehicles;
	static $suppliers;
	static $routes;
	static $addons;
	
	static function getDestinations(){
		if (!isset(self::$destinations)){
			global $wpdb;
			$querystr = "
			SELECT $wpdb->posts.*, {$wpdb->posts}.post_excerpt as code, meta2.meta_value as country_id
			FROM $wpdb->posts			
			LEFT JOIN $wpdb->postmeta as meta2 ON ($wpdb->posts.ID = meta2.post_id AND meta2.meta_key = '_country_id')
			WHERE $wpdb->posts.post_status = 'publish'
			AND $wpdb->posts.post_type = 'HB_dest'
			GROUP BY $wpdb->posts.ID
			ORDER BY $wpdb->posts.post_title ASC
			";
			self::$destinations = $wpdb->get_results($querystr, OBJECT_K);
		}
		
		return self::$destinations;
	}
	
	static function getcountries(){
		if (!isset(self::$countries)){
			$data = file_get_contents(FVN_PATH.'includes/data/countries.json');
			self::$countries = json_decode($data);			
			foreach(self::$countries as $i=>&$value){
				$value->id=$i;
			}
		}
		
		return self::$countries;
	}
	
	static function getSuppliers(){
		if (!isset(self::$suppliers)){
			global $wpdb;
			$querystr = "
			SELECT $wpdb->posts.*
			FROM $wpdb->posts
			WHERE $wpdb->posts.post_status = 'publish'
			AND $wpdb->posts.post_type = 'HB_supplier'
			GROUP BY $wpdb->posts.ID
			ORDER BY $wpdb->posts.post_title ASC
			";
			self::$suppliers = $wpdb->get_results($querystr, OBJECT_K);
		}
	
		return self::$suppliers;
	}
	
	static function getVehicles(){
		if (!isset(self::$vehicles)){
			global $wpdb;
			$querystr = "
			SELECT $wpdb->posts.*
			FROM $wpdb->posts
			WHERE $wpdb->posts.post_status = 'publish'
			AND $wpdb->posts.post_type = 'HB_vehicle'
			GROUP BY $wpdb->posts.ID
			ORDER BY $wpdb->posts.post_title ASC
			";
			self::$vehicles = $wpdb->get_results($querystr, OBJECT_K);
		}
	
		return self::$vehicles;
	}
	
	static function getRoutes($id=null){
		if (!isset(self::$routes)){
			global $wpdb;
			$querystr = "
			SELECT $wpdb->posts.*
			FROM $wpdb->posts
			WHERE $wpdb->posts.post_status = 'publish'
			AND $wpdb->posts.post_type = 'HB_route'
			GROUP BY $wpdb->posts.ID
			ORDER BY $wpdb->posts.post_title ASC
			";
			self::$routes = $wpdb->get_results($querystr, OBJECT_K);
		}
		if($id){
			return self::$routes[$id];
		}
		return self::$routes;
	}
	
	static function getAddons($id=null){
		if (!isset(self::$addons)){
			global $wpdb;
			$querystr = "
			SELECT $wpdb->posts.*
			FROM $wpdb->posts
			WHERE $wpdb->posts.post_status = 'publish'
			AND $wpdb->posts.post_type = 'HB_addon'
			GROUP BY $wpdb->posts.ID
			ORDER BY $wpdb->posts.post_title ASC
			";
			self::$addons = $wpdb->get_results($querystr, OBJECT_K);
			foreach (self::$addons as &$addon){
				$addon->post_content = json_decode($addon->post_content);
			}
		}
		if($id){
			return self::$addons[$id];
		}
		return self::$addons;
	}
	
	
	static function getPaymentPlugin() {
		$options = get_option ( 'active_plugins' );
		$result = array();
		foreach ($options as $plugin){
			if(preg_match('/^hbpayment_\w*/', $plugin)){
				$name = explode('/', $plugin);
				$result[] = (object)array('name'=>$name[0],'title'=>substr($name[0], 10),'file'=>ABSPATH."wp-content/plugins/$name[0]/");
			}
		}
		return $result;
	}
	
	/**
	 * 
	 * @return array of core payment method
	 */
	static function getCorePaymentMethod($params=[]){
		$result = array();
		$files = scandir (FVN_PATH.'includes/gateways');
		foreach ($files as $plugin){
			if(preg_match('/^hbpayment_\w*/', $plugin)){
				$name = explode('/', $plugin);
				$result[] = (object)array('name'=>$name[0],'title'=>substr($name[0], 10),'file'=>FVN_PATH."includes/gateways/$plugin/");
			}
		}
		return $result;
	}
	
	/**
	 * Get all core payment method and installed payment plugin
	 */
	static function getPaymentAvailPlugin($enable = true,$check_permission = false){
		$result = array();
		$core = self::getCorePaymentMethod();
// 		debug($core);
		//check core method that is it turn on
		if($enable){
			foreach ($core as $i=>$plugin){
				$params= get_option($plugin->name,'{}');
				$params = json_decode($params);
				if (!$params->enabled){
					unset($core[$i]);
				}
			}
		}
		$additionals = self::getPaymentPlugin();
		$result = array_merge($core,$additionals);
		//check permission of user to use the plugin
		if ($check_permission){
			$current_user = wp_get_current_user();
			foreach ($result as $i=>$plugin){
				$params= get_option($plugin->name,'{}');
				$params = json_decode($params);				
				if(in_array('publish', $params->user_role)){
					//publish for all user					
				}elseif (in_array('guest', $params->user_role)){
					//only allow guest
					if($current_user->ID){
						unset($result[$i]);
					}					
				}elseif (is_array($params->user_role)){
					foreach ($current_user->roles as $role){
						if (!in_array($role, $params->user_role)){
							unset($result[$i]);
						}
					}
					
				}
			}
		}
		foreach($result as &$plugin){
			$params= get_option($plugin->name,'{}');
			$params = json_decode($params);
			$plugin->params = $params;
		}
		return $result;
	}
}