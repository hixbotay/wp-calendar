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

class HBOrderCustom extends HBOrder{
	
	public function __construct($array=null){
		return parent::__construct($array);
	}	
	
	
	public function save(){
		try{
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
					'type' 		=> 'CUSTOMIZE',
					'user_id' 	=> $customer_id,
					'pay_method'=> '',
					'promo_code'=>$this->customer['promo_code'],
					'order_status' => $order_status,
					'pay_status'=> 'PENDING',
					'notes' 	=> $this->customer['notes']
			);
			if(!$this->store()){
				$this->writeLog('Save order failed');
				return false;
			}
			if(!$this->saveOrderInfo()){
				$this->writeLog('Save orderinfo failed');
				return false;
			}
			
			$this->table->order_number = 'CS'.$this->table->order_number;
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
		AImporter::table('orderinfo');
		AImporter::helper('date');
		$this->orderinfo['params']['start'] = DateHelper::createFromFormatYmd($this->orderinfo['params']['start']);
		$this->orderinfo['params']['end'] = DateHelper::createFromFormatYmd($this->orderinfo['params']['end']);
		$orderinfo = $this->orderinfo;
		$orderinfo['order_id'] = $this->table->id;
		$orderinfo['params'] = json_encode($this->orderinfo['params']);
		$orderinfo['start'] = $this->orderinfo['params']['start'];
		$TableOrderinfo = new TableOrderInfo ( $this->_db );
		return $TableOrderinfo->save ( $orderinfo );
	}
}