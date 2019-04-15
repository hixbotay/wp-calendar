<?php
/**
 * Save session
 * @author Admin
 *
 */

class HBCart{
	public $from;
	public $to;
	public $adult;
	public $children;
	public $infant;
	public $start;
	public $end;
	public $type_cart = 'HB_cart';
	
	function __construct(){
	}
	
	
	function saveToSession() {
		
		$_SESSION[$this->type_cart] = serialize($this);
		return true;
	}
	
	//get element from session
	function get($key){
		if(isset($_SESSION[$key])){
			return $_SESSION[$key];
		}else{
			return false;
		}
	}
	
	function load(){
		$objcart = $this->get($this->type_cart);
	
		if (isset($objcart) && $objcart!='') {
			$temp_cart = unserialize($objcart);
			foreach ($temp_cart as $key=>$val){
				$this->$key = $val;
			}
		}
	
	
	}
	function clear(){
		$_SESSION[$this->type_cart] = null;
		return true;
	}
}