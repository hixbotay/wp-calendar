<?php



class FvnParams
{
	static function get_customer_group(){
		$config = AFactory::getConfig();
		return array(
				$config->admin_usergroup=>'admin',
				$config->manager_usergroup=>'manager',
				$config->advisor_usergroup=>'advisor',
// 				$config->host_usergroup=>'host',
				$config->ambassador_usergroup=>'ambassador',
		);
	}
	
	static function get_customer_group_role(){
		return array(
				'manage'=>'manager',
				'advisor'=>'advisor',
				'ambassador' => 'ambassador'
		);
	}
	
	static function get_product_type(){
		return array(
				'text'=>'Text',
				'video'=>'video',
				'website'=>'website',
		);
	}
	
	static function get_car_seat(){
		return [4=>'4 seats',
				7=>'7 seats',
				16=>'16 seats',24=>'24 seats'];
	}
	
	
	static function get($key_val,$type=false){
		$function = 'get_'.$key_val;
		$data = self::$function();
		if($type){
			switch ($type) {
				case 'arrayObject':
					$result = array();
					foreach ($data as $key=>$val){
						$result[] = (object)array('value'=>$key,'text'=>_($val));
					}
					return $result;
					break;		
				case 'array':
					$result = array();
					foreach ($data as $key=>$val){
						$result[$val] = $key;
					}
					return $result;
					break;		
				default:
					break;
			}
		}
		return $data;
	}
	/**
	 * get langugage setting
	 * require xmlhelper
	 */
    static function getLanguageConfig(){
    	$config = array();
    	$file_filter = JPATH_ADMINISTRATOR.'/components/com_jbtracking/data/language_filter.xml';
		$filter = JFactory::getXML($file_filter);
		$config['main_lang'] = 'en-GB';		
		$config['folder_site']		= JPATH_SITE .DS."language".DS;
		$config['folder_admin']	= JPATH_ADMINISTRATOR .DS."language".DS;
		//admin language file
		$config['file_admin'] = XmlHelper::getAttribute($filter->admin->file, 'name');
		//site language file
		$config['file_site']	= XmlHelper::getAttribute($filter->site->file, 'name');
		return $config;
    }
}

?>