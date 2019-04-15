<?php
class HBInput{
	//data of request
	public $data = array();
	public $post = array();
	public $get = array();
	public $plaintext = array();//exactly input from $_REQUEST
	//server info of request
	public $server;
	
	public function __construct(){
		foreach($_GET as $key=>$val){
			$key = str_replace('amp;', '', $key);
			$this->plaintext[$key] = $val;
			$this->data[$key] = $this->filter_text($val);
			$this->get[$key] = $this->data[$key];
		}
		foreach($_POST as $key=>$val){
			$this->plaintext[$key] = $val;
			$this->data[$key] = $this->filter_text($val);
			$this->post[$key] = $this->data[$key];
		}
		return true;
	}
	
	private function filter_text($input){
		if(is_array($input)){
			$result = array();
			foreach ($input as $k=>$v){
				$result[$k] = $this->filter_text($v);
			}
			return $result;
		}else{
			return sanitize_text_field($input);
		}
		
	}
	
	public function getPlainText($key,$default = null){
		return isset($this->plaintext[$key]) ? ($this->plaintext[$key]) : $default;
	}
	
	public function get($key,$default = null,$type=null){
		if(!$key){
			return false;
		}
		return isset($this->data[$key]) ? ($this->data[$key]) : $default;
	}
	
	public function getInt($key,$default = null){
		if(!$key){
			return false;
		}
		return isset($this->data[$key]) ? (int)$this->data[$key] : $default;
	}
	
	public function getString($key,$default = null){
		if(!$key){
			return false;
		}
		return isset($this->data[$key]) ? ($this->data[$key]) : $default;
	}
	
	public function set($key,$val){
		$this->data[$key] = $val;
		return true;
	}
	
	public function getPost(){
		return $this->post;
	}
}