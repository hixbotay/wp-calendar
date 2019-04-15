<?php
class FvnModelInvestPackage extends FvnModel{
	public function __construct($table_name='#__fvn_invest_package', $primary_key = 'id'){
		return parent::__construct($table_name, $primary_key);
	}
	
	public function check(){
		if(!$this->id){
			// $this->created = current_time( 'mysql' );
		}
		return true;
	}
	
	protected function getQueries(){
		$query = HBFactory::getQuery();
		$query->select('r.*')
		->from('#__fvn_invest_package as r');		
		$query->group('r.id');
		return $query;		
	}
	
	public function getItems(){
		$items = parent::getItems();
		foreach($items as &$item){
			$item->rate = json_decode($item->rate,true);
		}
		return $items;
	}

	public function getItem($pk=null){
		$item = parent::getItem($pk);
		$item->rate = json_decode($item->rate);
		if(!$item->rate){
			$item->rate = [];
		}
		return $item;
	}

}