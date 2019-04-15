<?php

/**
 * @package 	FVN-extension
 * @author 		Joombooking
 * @link 		http://http://woafun.com/
 * @copyright 	Copyright (C) 2011 - 2012 Vuong Anh Duong
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
defined('ABSPATH') or die('Restricted access');

class HBCustomer extends WP_User{
	
	
	public function save($data){
		if(!is_string($data['params'])){
			$data['params'] = json_encode($data['params']);
		}
		return $this->save($data);
	}
	
	public function store(){
		if(!is_string($this->params)){
			$this->params = json_encode($this->params);
		}
		return $this->store();
	}
	
	public function setSunpassNumber(){
		$this->sunpass = FvnHelper::randomString(5);
		$this->_db->setQuery('select count(*) from #__bookpro_customer where sunpass LIKE '.$this->_db->quote($this->sunpass));
		if($this->_db->loadResult()){
			//sunpass point dupplicate
			return $this->setSunpassNumber();
		}
		$id_encode = md5($this->id);
		$this->sunpass .= strtoupper($id_encode[0].substr($id_encode,-1,1));
		return $this->store();
	}
	
	public function create($data){
		$this->save($data);
		$this->id = $this->id;
		if($this->id){
			return $this->setSunpassNumber();
		}
		return false;
	}
	
	public function delete(){
		//delete user
		if($this->user){
			$user = JFactory::getUser($this->user);
			if($user->id){
				$user->delete();
			}
		}	
		return $this->delete();
		
	}
	
	public function saveSunpassPoint($order){
		if($order->id && $order->pay_status == 'SUCCESS'){
			$point_per_price = JComponentHelper::getParams('com_bookpro')->get('sunpass_point_per_price',10);				
			$point = ceil($order->total/$point_per_price);
			$sunpass_number  = $this->processSunpassOfOrder($order);
			if($sunpass_number){				
				if($this->id){
					$this->sunpass_point += $point;
					return $this->store();						
				}
								
			}
			
		}
		return false;
		
	}
	
	private function processSunpassOfOrder($order){
		$order_params = json_decode($order->params);
		$sunpass_number = $order_params->sunpass->number;
		if(!empty($sunpass_number)){
			//remove sunpass number for order
			if(empty($order_params->sunpass->log)){
				$order_params->sunpass->log = $order_params->sunpass->number;		
			}else{
				$order_params->sunpass->log .= ','.$order_params->sunpass->number;		
			}						
			$order_params->sunpass->number = '';
			$order->params = json_encode($order_params);
			$order->store();
			return $sunpass_number;
		}
		return false;
		
	}
}
?>