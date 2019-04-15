<?php
class FvnModelCountry extends FvnModel{
	public function __construct($table_name='#__fvn_countries', $primary_key = 'id'){
		return parent::__construct($table_name, $primary_key);
	}
	
	public function check(){
		
		return true;
	}
	
	protected function getQueries(){
		$query = HBFactory::getQuery();
		$query->select('*')
		->from('#__fvn_countries');
		if($this->getState('filter_title')){
			$query->where('country_name LIKE '.$query->quote('%'.$this->getState('filter_title').'%'));
		}
		$query->order('country_name ASC');
		return $query;		
	}
	
	function getDataByCode($code){
		global $wpdb;
		return $wpdb->get_row("select * from {$wpdb->prefix}fvn_countries where country_code='{$code}'");
	}
	
}