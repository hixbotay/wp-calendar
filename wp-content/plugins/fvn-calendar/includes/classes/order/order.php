<?php
/**
 * @package 	FVN-extension
 * @author 		Joombooking
 * @link 		http://http://woafun.com/
 * @copyright 	Copyright (C) 2011 - 2012 Vuong Anh Duong
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
defined('ABSPATH') or die('Restricted access');

require_once FVN_PATH.'includes/classes/class-customer.php';

class HBOrder extends HBObject
{
	public $id;
	public $data;
	public $customer;
	public $passengers;
	public $addon;
	public $promo_code;
	public $price;
	public $table;
	public $user;
	public $error;
	
	
	public function __construct($array = null){	
		$this->error = 0;
		$this->data = array();
		if($array){
			foreach ($array as $key=>$val){
				$this->set($key,$val);
			}
		}
	}
	
	public function writeLog($content){
		
		return;
	}
	
	//abstract public function setPrice();
	
	public function processCoupon(){
		
		require_once FVN_PATH.'includes/classes/coupon.php';
		$this->promo_code = new HBCoupon($this->data['promo_code']);
		if($this->promo_code->id){
			if($this->promo_code->data->subtract_type==1){
				$discount= ($this->data['total'] * $this->promo_code->data->amount)/100;
				$this->data['total'] -= $discount;
				$this->data['discount'] =$discount;				
			}else {
				$this->data['total'] -= $this->promo_code->data->amount;
				$this->data['discount'] = $this->promo_code->data->amount;
			}
			$this->data['params']->promo_code = $this->data['promo_code'];
			return true;		
		}else {			
			return false;
		}
	}
	
	public function addCoupon(){
		$add = $this->processCoupon();
		if($add){
			$this->promo_code->data->remain -= 1;
			$this->promo_code->update();
		}
		$this->error_code = 21;
		return false;
		
	}
	
	protected function saveCustomer(){
		// save customer
		$user = HBFactory::getUser();
		
		if ($user->user->id) {
			if($user->is_agent){
				//return fasle if there are no customer record is assign to the user
				if(!$user->customer->table->id){
					HB_enqueue_message('You must update your information before booking','warning');
					return false;
				}
				if($this->customer['sunpass']){
					$customer->load(array(
							'sunpass'=>$this->customer['sunpass']
					));
				}				
			}
			else{
				$customer = $user->customer->table;			
			}
		}else{
			$customer->load(array(
					'sunpass'=>$this->customer['sunpass']
			));
		}
		
		if($customer->save ( $this->customer )){
			return $customer->id;			
		}
		return false;
	}
	
	protected function saveOrderInfo(){
		//save order info
		AImporter::table('orderinfo');
		$orderinfo = array (
				'type'			=>'TOUR',
				'route_id'		=>$this->tour->id,
				'order_id' 		=> $this->table->id,
				'start' 		=> $this->rate->date,
				'pickup' => $this->cart->tour->start_time,
				'return_pickup' => $this->tour->return_time,
				'adult' => $this->cart->filter['adult'],
				'child'	=> $this->cart->filter['child'],
				'infant'=> $this->cart->filter['infant']						
		);							
		$TableOrderinfo = new TableOrderInfo ( $this->_db );
		return $TableOrderinfo->save ( $orderinfo );
	}
	
	protected function savePassenger(){
		AImporter::table('passenger');
		//TODO
		foreach ( $this->passengers as $passenger){
			$passenger['order_id'] = $this->table->id;
			$table = new TablePassenger($this->_db);
			$table->save($passenger);
		}
		return true;
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
	}
	
		
	protected function store(){
		$user = JBFactory::getAccount();
		if(empty($this->data)){
			$this->error = new JObject();
			$this->error->code = 1;
			$this->error->msg = 'Empty data';
			return false;
		}	
		if($user->is_agent){
			$this->data['agent_id'] = $user->id;
		}
		if(!is_object($this->data['params'])){
			$this->data['params'] = json_decode($this->data['params']);
		}
		
		$sunpass = new JbObject();	
		$sunpass->number = trim($this->customer['sunpass']);
		$sunpass->log = '';
		$this->data['params']->sunpass = $sunpass;
		
		$this->data['params'] = json_encode($this->data['params']);		
		return $this->table->save($this->data);
	}
	
	function applycoupon($order_id,$code){
		if(empty($code)){
			return false;
		}
		AImporter::model('coupon');
		$couponModel=new BookProModelCoupon();
		$coupon=$couponModel->getObjectByCode($code);
		$check=true;
		if($coupon){
			if((int)$coupon->total==0){
				$check=false;
				$msg=JText::_('COM_BOOKPRO_COUPON_INVALID');
			}else{
				JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/tables');
				$order = JTable::getInstance('orders', 'table');
				$order->load($order_id);
				if($order->discount>0){
					$check=false;
					$msg=JText::_('COM_BOOKPRO_COUPON_APPLY_ERROR');
				}else {	
					if($coupon->subtract_type==1){
						$discount= ($order->total*$coupon->amount)/100;
						$order->total=$order->total-$discount;
						$order->discount=$discount;
					}else {
						$order->total=$order->total-$coupon->amount;
						$order->discount=$coupon->amount;
					}
					$order->coupon_id=$coupon->id;
					$coupon->remain -= 1;
					$coupon->store();
					$order->store();
					$msg=JText::_('COM_BOOKPRO_COUPON_VALID');
				}
			}
		}else {
			$check=false;
			$msg=JText::_('COM_BOOKPRO_COUPON_INVALID');
		}
		JFactory::getApplication()->enqueueMessage($msg);
		return $check;
	
	}
}
?>
