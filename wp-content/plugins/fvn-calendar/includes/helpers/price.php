<?php
class FvnPriceHelper{
	
	static function caculate($params){
		$config = HBFactory::getConfig();
// 		debug($config);die;
		$result = ['total'=>0];
		$period = $params['period'];
		$purpose_of_visit = $params['purpose_of_visit'] ? $params['purpose_of_visit'] : 'tour';
		
		$result['single'] = $params['period']->{'price_'.$purpose_of_visit};
		if($params['country']){
			$country_params = json_decode($params['country']->params);
// 			debug($country_params);
			if($country_params){
				if(isset($country_params->{$period->id}->status) && $country_params->{$period->id}->status){
					$result['single'] = $country_params->{$period->id}->$purpose_of_visit;
				}
			}
		}
		
		$result['main'] = $result['single']*$params['passenger_number'];
		$result['total'] += $result['main'];
		if($params['processing_time']){
			$processing_time = $params['processing_time'];
			$result['processing_time'] = array();
			$result['processing_time']['single'] = $processing_time->{'price_'.$purpose_of_visit};
			$result['processing_time']['total'] = $result['processing_time']['single'] * $params['passenger_number'];
			$result['total'] += $result['processing_time']['total'];
		}
		if($params['private_later']){
			$result['private_later'] = $config->private_later;
			$result['total'] += $result['private_later'];
		}
		if($params['airport_fast_track']){
			$result['airport_fast_track'] = array();
			$result['airport_fast_track']['single'] = $config->get('airport_fast_track',10);
			$result['airport_fast_track']['total'] = $result['airport_fast_track']['single']*$params['passenger_number'];
			$result['total'] += $result['airport_fast_track']['total'];
		}
		if($params['car_service']){
			if($params['airport']->params[$params['car_service']]){
				$result['car_service'] = $params['airport']->params[$params['car_service']];
				$result['total'] += $result['car_service'];
			}
			
		}
		
		
		return $result;		
	}
	
}