<?php
FvnImporter::helper('date');
class FvnModelOrders extends FvnModel{
	public function __construct($table_name='#__fvn_orders', $primary_key = 'id'){
		return parent::__construct($table_name, $primary_key);
	}
	private function getUniqueOrderNumber(){
		$order_number = current_time('Y').strtoupper(FvnHelper::random_string(5));
		$query = HBFactory::getQuery();
		$query->select('id')->from('#__fvn_orders')->where('order_number LIKE '.$query->quote($order_number));
		global $wpdb;
		if($wpdb->get_row($query->__toString())){
			return $this->getUniqueOrderNumber();
		}
		return $order_number;
	}

	public function check(){
		if(!$this->id){
			$this->created = current_time( 'mysql' );
			$this->order_number = $this->getUniqueOrderNumber();			
		}
		return true;
	}
	
	protected function getQueries(){
		$query = HBFactory::getQuery();
		$query->select('o.*')
		->from('#__fvn_orders as o');
		if($this->getState('filter_title')){
			$search = '%'.$this->getState('filter_title').'%';
			$query->where('order_number LIKE '.$query->quote($search).' OR phone LIKE '.$query->quote($search).' 
					OR email LIKE '.$query->quote($search).')');
		}
		if($this->getState('filter_date')){
			$query->where('o.start = '.$this->quote($this->getState('filter_date')));
		}
		if($this->getState('filter_order_status')){
			$query->where('o.order_status = '.$this->quote($this->getState('filter_order_status')));
		}
		if($this->getState('filter_pay_status')){
			$query->where('o.pay_status = '.$this->quote($this->getState('filter_pay_status')));
		}
		
		// if($this->getState('filter_user_id')){
		// 	$query->where('o.user_id = '.(int)$this->getState('filter_user_id'));
		// }
		
		$query->order('id DESC');
		return $query;		
	}

	function getBooked(){
		$query = HBFactory::getQuery();
		global $wpdb;

		$query->select('o.*')
		->from('#__fvn_orders as o');
		$query->where('o.start >= '.date('Y-m-d'));
		$query->where('o.order_status = '.FvnParamOrderStatus::CONFIRMED['value']);
		$query->order('o.start ASC');
		$query->order('o.start_time ASC');
		
		return $wpdb->get_results($query->__toString());
	}	
	
	function getComplexItem($id){
		$order = $this->getItem($id);
		$result = new stdClass();
		if($order->id){
			global $wpdb;
			// $order->day = FvnDateHelper::getDate($order->start)->diff(FvnDateHelper::getDate($order->end))->days;
			$result->order = $order;
			FvnImporter::helper('math','date','currency','price');
			$config = HBFactory::getConfig();
			// $result->user = HBFactory::getUser($order->user_id);

			return $result;
		}else{
			return false;
		}		
	}

	function getOrderByUser($user_id,$params=array()){
		$this->setState('filter_user_id',$user_id);
		$this->setState('filter_pay_status',FvnParamPayStatus::SUCCESS['value']);
		// $query = $this->getQueries();
		return $this->getItems();
	}

	

	public function caculateDrawAble(){
		$data = $this->getProperties();
		return FvnInvestHelper::caculateDrawAble($data);
	}
}