<?php
HBImporter::helper('date','invest');
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
		$query->select('o.*,p.name as invest_name,u.display_name as user_display_name,um.meta_value as user_phone')
		->from('#__fvn_orders as o')
		->leftJoin('#__users as u ON u.ID = o.user_id')
		->leftJoin('#__usermeta as um ON u.ID = um.user_id AND um.meta_key="phone"')
		->leftJoin('#__fvn_invest_package as p ON o.invest_package_id = p.id');
		if($this->getState('filter_title')){
			$search = '%'.$this->getState('filter_title').'%';
			$query->where('u.display_name LIKE '.$query->quote($search).' OR order_number LIKE '.$query->quote($search).' OR mobile LIKE '.$query->quote($search).' 
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
		if($this->getState('filter_private_later')!=''&&$this->getState('filter_private_later')!=null){
			$query->where('o.private_later = '.(int)$this->getState('private_later'));
		}
		if($this->getState('filter_user_id')){
			$query->where('o.user_id = '.(int)$this->getState('filter_user_id'));
		}
		
		$query->order('id DESC');
		return $query;		
	}
	
	function getComplexItem($id){
		$order = $this->getItem($id);
		$result = new stdClass();
		if($order->id){
			global $wpdb;
			// $order->day = HBDateHelper::getDate($order->start)->diff(HBDateHelper::getDate($order->end))->days;
			$result->order = $order;
			HBImporter::helper('math','date','currency','price');
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