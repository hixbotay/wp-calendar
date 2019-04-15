<?php
//Store state of model
class FvnModelState extends HBObject{
	public $data;
	public $name;
	public $session;
	public function __construct($name){
		$this->name = $name;
		$this->data = new stdClass();
	}
	public function set($key,$val,$session=false){
		$this->data->$key = $val;
		if($session){
			$_SESSION['fvn_filter'][$this->name][$key] = $val;
		}
		
	}
	public function get($key,$default=''){
		if(isset($this->data->$key)){
			return $this->data->$key;
		}
		if(isset($_SESSION['fvn_filter'][$this->name][$key])){
			return $_SESSION['fvn_filter'][$this->name][$key];
		}
		return $default;
	}
	//count number of state
	function getCount(){
		return count((array)$this->data);
	}
	
	function getData(){
		return (array)$this->data;
	}
}