<?php
class FvnModelTransaction extends FvnModel{
	public function __construct($table_name='#__fvn_transaction', $primary_key = 'id'){
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
		$query->select('t.*,u.display_name as user_display_name, u.user_email as user_email')
		->from('#__fvn_transaction as t')
		->leftJoin('#__users as u ON u.ID = t.user_id');
		
		if($this->getState('filter_user_id')){
			$query->where('t.user_id = '.(int)$this->getState('filter_user_id'));
		}
		if($this->getState('filter_title')){
			$query->where('t.content LIKE '.$query->quote('%'.$this->getState('filter_title').'%'));
		}
		return $query;		
	}
	
	public function get_Transaction_by_cat($cat_id){
		if(empty($cat_id)){
			return [];
		}
		global $wpdb;
		
		$query = "Select p.* from ".$this->get_table_name()." as p 
				left Join ".$wpdb->prefix."Transaction_categories as c ON c.id=p.category_id				
				where p.category_id= {$cat_id} OR c.parent_id={$cat_id} group by p.id ";
		
		$query .= ' order by created DESC ';
		if($this->get_state('list.limit')){
			$query .= 'LIMIT 0,'.$this->get_state('list.limit');
		}
		
		
				
		return $wpdb->get_results($query,OBJECT_K);
	}
}