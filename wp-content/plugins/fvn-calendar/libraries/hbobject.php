<?php

/**
 * @package 	FVN-extension
 * @author 		Joombooking
 * @link 		http://http://woafun.com/
 * @copyright 	Copyright (C) 2011 - 2012 Vuong Anh Duong
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
defined('ABSPATH') or die('Restricted access');

class HBObject
{	

	public function __construct($array = null){
		if($array){
			foreach ($array as $key=>$val){
				$this->set($key,$val);
			}
		}
	}
	
	public function get($key,$default=null){
		if(isset($this->$key)){
			return $this->$key;
		}
		return $default;
	}
	
	public function set($key,$value){
		$this->$key = $value;
		return true;
	}
	
	public function load($data){
		if(!is_object($data)){
			return false;
		}else{
			foreach ($data as $key=>$val){
				$this->$key = $val;
			}
		}
	}
}
?>
