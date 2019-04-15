<?php

/**
 * @package 	FVN-extension
 * @author 		Joombooking
 * @link 		http://http://woafun.com/
 * @copyright 	Copyright (C) 2011 - 2012 Vuong Anh Duong
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
defined('ABSPATH') or die('Restricted access');

require_once FVN_PATH.'includes/classes/order/order.php';

class HBOrderRoute extends HBOrder{
	public $rate;
	public $rate_id;
	public $price;
	public $cart;
	public $passengers;
	
	public function __construct($array=null){
		$this->price = new HBObject();
		$this->data = array();
		return parent::__construct($array);
	}	
	
	public function setPrice(){
		if(!empty($this->rate)){
			$this->price->vat 	= HBFactory::getConfig()->vat;
			$this->price->adult	= $this->cart->adult;
			$this->price->child = $this->cart->children;
			$this->price->infant = $this->cart->infant;
			$this->price->rate 	= $this->rate;
			$this->price->addons = $this->addons;
			$this->data['total'] = $this->getTotal();
			$this->data['subtotal'] = $this->data['total'];
			return $this->data['total'];
		}else{
			$this->writeLog('Empty rate');
			return false;
		}
		
	}
	
	public function setTourRate(){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')->from('#__bookpro_tourrate')->where('id='.(int)$this->rate_id);
		$db->setQuery($query);
		$this->rate = $db->loadObject();
		return $this->rate ? true : false;
	}
	
	public function setTour(){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		if($this->tour_id){
			$query->select('*')->from('#__bookpro_tour')->where('id='.(int)$this->tour_id);
		}else{
			if($this->rate->tour_id){
				$query->select('*')->from('#__bookpro_tour')->where('id='.(int)$this->rate->tour_id);
			}else{
				return false;
			}
		}
		$db->setQuery($query);
		$this->tour = $db->loadObject();
		return $this->tour ? true : false;
	}
	
	public function create(){
		try{
			$this->_db->transactionStart();
			//save customer
			$customer_id = $this->saveCustomer();
			if(!$customer_id){
				$this->writeLog('Save customer failed');
				return false;
			}
			//save order
			AImporter::helper('orderstatus');
			OrderStatus::init();
			$order_status = OrderStatus::$PENDING->getValue ();
				
			$this->data = array (
					'type' 		=> 'TOUR',
					'user_id' 	=> $customer_id,
					'pay_method'=> '',
					'promo_code'=>$this->customer['coupon'],
					'order_status' => $order_status,
					'pay_status'=> 'PENDING',
					'notes' 	=> $this->customer['notes'],
					'tax' 		=> $this->cart->tax,
					'service_fee' => $this->cart->service_fee
			);
			$this->setPrice();
			$this->addCoupon();
			if(!$this->store()){
				$this->writeLog('Save order failed');
				return false;
			}
			if(!$this->saveOrderInfo()){
				$this->writeLog('Save orderinfo failed');
				return false;
			}
			if(!$this->saveAddon()){
				$this->writeLog('Save addon failed');
				return false;
			}
			if(!$this->savePassenger()){
				$this->writeLog('Save passenger failed');
				return false;
			}
			$this->table->order_number = 'TR'.$this->table->order_number;
			$this->table->store();
			$this->_db->transactionCommit();
		}
		catch ( Exception $e ) {
			$this->writeLog ( $e->getMessage () );
			$this->_db->transactionRollback ();
			return false;
		}
		
		return true;
	}
	
	protected function saveOrderInfo(){
		//save order info
		if(empty($this->tour->id) || empty($this->rate->id)){
			$this->writeLog('Empty tour or rate');
			return false;
		}
		AImporter::table('orderinfo');
		$orderinfo = array (
				'type'			=>'TOUR',
				'route_id'		=>$this->tour->id,
				'order_id' 		=> $this->table->id,
				'start' 		=> $this->rate->date,
				'adult' => $this->cart->filter['adult'],
				'child'	=> $this->cart->filter['child'],
				'infant'=> $this->cart->filter['infant']						
		);							
		$TableOrderinfo = new TableOrderInfo ( $this->_db );
		return $TableOrderinfo->save ( $orderinfo );
	}
	
	protected function saveAddon(){
		AImporter::table('ordersaddon');
		
		if($this->addons)
			foreach ($this->addons as $addon){			
			$addon_array = array(
					'order_id'	=> $this->table->id,
					'addon_id'	=> $ad,
					'return'	=> 0,
					'params'	=> json_encode($addon)
			);					
			$addon_table = new TableOrdersAddon($this->_db);
			$addon_table->save($addon_array);
		}
		return true;
	}
	
	
	
	
	
}