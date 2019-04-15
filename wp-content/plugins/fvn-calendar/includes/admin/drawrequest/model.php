<?php
class FvnModelDrawRequest extends FvnModel{
	public function __construct($table_name='#__fvn_draw_request', $primary_key = 'id'){
		return parent::__construct($table_name, $primary_key);
	}
	
	public function check(){
		if(!$this->id){
			$this->created = current_time( 'mysql' );
		}
		return true;
	}
	
	protected function getQueries(){
		$query = HBFactory::getQuery();
		$query->select('r.*,p.name as invest_name,u.display_name as user_name,u.user_email,o.order_number,o.total,`o`.`start`,r.created as end_date')
		->from('#__fvn_draw_request as r');
		$query->leftJoin('#__users as u ON u.ID = r.user_id');
		$query->leftJoin('#__fvn_invest_package as p ON p.id = r.invest_package_id');
		$query->leftJoin('#__fvn_orders as o ON o.id = r.order_id');
		// $query->leftJoin('#__usermeta as um ON um.user_id = r.user_id');
		if($this->getState('filter_title')){
			$search = $query->quote('%'.$this->getState('filter_title').'%');
			$query->where('u.email LIKE '.$search.' OR u.display_name LIKE '.$search);
		}
		if($this->getState('filter_user_id')){
			$query->where('r.user_id = '.$this->getState('filter_user_id'));
		}
		if($this->getState('filter_status')){
			$query->where('r.status = '.$query->quote($this->getState('filter_status')));
		}
		if($this->getState('filter_order_id')){
			$query->where('r.order_id = '.$query->quote($this->getState('filter_order_id')));
		}
		$query->group('r.id');
		$query->order('r.id DESC');
		return $query;		
	}

	public function getRequestByOrder($order_id){
		if(!$order_id){
			return [];
		}
		$this->setState('filter_order_id',$order_id);
		return $this->getItems();
	}
	
}