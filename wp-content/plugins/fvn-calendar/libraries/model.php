<?php
/**
 * @package 	FVN-extension
 * @author 		Vuong Anh Duong
 * @link 		http://freelancerviet.net
 * @copyright 	Copyright (C) 2011 - 2012 Vuong Anh Duong
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id$
 **/
require_once 'model/query.php';
require_once 'model/state.php';
require_once 'model/pagination.php';

class FvnModel
{
	public $jbcache = null;
	static $connection;
	public $_tbl;
	public $_key;
	public $state;
	public $name;

	function __construct($table_name, $primary_key)
	{
		global $wpdb;
		$this->_tbl = str_replace('#__',$wpdb->prefix,$table_name);
		if(is_array($primary_key)){
			$this->_key = $primary_key;
		}else{
			$this->_key = array($primary_key);
		}
		$this->name = __CLASS__;
		$this->state= new FvnModelState($this->name);
		
		
	}
	
	function get_primary_key(){
		return $this->_key;
	}
	
	function populate_state(){
		$input= HBFactory::getInput();
		$data = $input->data;
		foreach($data as $key=>$val){
			if(substr($key, 0,7)=='filter_'){
				$this->state->set($key,$val,true);
			}
		}
		$this->state->set('limit',$input->get('limit',20));
		$this->state->set('offset',$input->getInt('offset',0));
	}
	
	protected function getQueries(){
		$query = HBFactory::getQuery();
		$query->select('*')
		->from($this->get_table_name());		
		return $query;
	}
	
	protected function getQueryList(){	
		global $wpdb;
		$query = $this->getQueries()->__toString();
// 		debug($query);die;
		return $query;
	}
	
	public function getItems(){
		global $wpdb;
		$input= HBFactory::getInput();
		
		$this->populate_state();
		$query = $this->getQueryList();
		
		if($this->state->get('limit')){
			$query .= " limit ".(int)$this->state->get('offset')*$this->state->get('limit').",{$this->state->get('limit')}";
		}
// 		debug($query);
		$result= $wpdb->get_results($query,OBJECT_K);
		if($wpdb->last_error){
			debug($wpdb->last_error);
		}
		return $result;
	}
	
	public function getList(){
		global $wpdb;
		$query = $this->getQueryList();
		
		return $wpdb->get_results($query,OBJECT_K);
	}
	
	public function getTotal(){
		//@TODO luu vao session de giam thoi gian query
		global $wpdb;
		$query = $this->getQueries();
		$query->clear('select');
		$query->select('count(1)');
		$query = $query->__toString();
		return $wpdb->get_var($query);
	}
	
	
	public function getPagination(){
		
		return (new FVNPagination(array(
				'total' => $this->getTotal(),
				'limit' => $this->state->get('limit'),
				'offset' => $this->state->get('offset'),
				'model_state' => $this->state
		)));
	}
	
	
	public function getItem($pk=null){
		global $wpdb;
		$input= HBFactory::getInput();
		$private_key = $this->get_primary_key();
		if(empty($pk)){			
			$pk = array();
			
// 			debug($private_key);
			foreach($private_key as $key){
				$pk[$key] = $input->getString($key);
				if(is_numeric($pk[$key])){
					$pk[$key] = "{$key} = {$pk[$key]}";
				}else{
					$pk[$key] = "{$key} = '{$pk[$key]}'";
				}
			}
			
		}else{
			if(!is_array($pk)){
				$key = reset($private_key);
				if(is_numeric($pk)){
					$pk = array("{$key} = {$pk}");
				}else{
					$pk = array("{$key} = '{$pk}'");
				}
			}
		}
		
		if(!empty($pk)){
			$query = "Select * from ".$this->get_table_name()." where ".implode(' AND ', $pk);
// 			debug($query);
			$result =  $wpdb->get_row($query);
			if(isset($result->params)){
				$result->params = json_decode($result->params,true);
			}
			return $result;
		}
	
		return false;
	
	}
	
	function bind($data){
		//debug($data);die;
		foreach($data as $key=>$val){
			$this->$key = $val;
		}
	}
	
	function store(){
		global $wpdb;
		
		$insert = false;
		foreach ($this->_key as $pr_key){
			if(!isset($this->$pr_key) || empty($this->$pr_key)){
				$insert = true;
			}
		}
		//insert
		$table_fields = $this->get_fields();
		$table_fields = array_map(function($a){return $a->Field;}, $table_fields);
		$data = $this->getProperties();
		foreach($table_fields as $i=>$k){
			if(is_null($data[$k])){
				unset($table_fields[$i]);
			}
		}
		$sql = 'INSERT INTO '.($this->_tbl).' ('.implode(',',$table_fields).') VALUES ';
		$sql .= "(".$this->render_values($data,$table_fields)."),";
		$sql = trim($sql,',')." ON DUPLICATE KEY UPDATE ";
		foreach($table_fields as $field){
			$sql .= "$field = VALUES($field),";
		}
		$sql = trim($sql,',').';';
		// debug($sql);
// 			echo $sql;die;
		$this->run_query($sql);
		$check = true;
		if($wpdb->last_error){
			$check = false;
			throw new Exception('Database save error '.$wpdb->last_error,404);
			$this->setError($wpdb->last_error);
		}
// 			debug($check);die;
		foreach($this->_key as $pr_key){
			if($data[$pr_key]){
				$this->$pr_key = $data[$pr_key];
			}else{
				$this->$pr_key = $wpdb->insert_id;
			}
		}			
		
		return $check;
	}
	
	function save($data, $orderingFilter = '', $ignore = ''){
 		//bind data to this
 		$this->bind($data); 	
//  		debug($this);die;
 		if(!$this->check()){
 			return false;
 		}
 		return $this->store();		
	}
	
	function delete($pk = null){
		if(!$pk){
			foreach($this->_key as $pr_k){
				if(empty($this->$pr_k)){
					$this->set_error(_('Please choose primary key'));
					return false;
				}
			}
		}else{
			$this->load($pk);
		}
		
		global $wpdb;
		$where = [];
		foreach($this->_key as $pr_k){
			$where[] = "{$pr_k} = ".$wpdb->_escape($this->$pr_k);
		}
		$where = implode(' AND ', $where);		
		try{
			$check = $wpdb->query("DELETE FROM ".$this->get_table_name()." WHERE ".$where);
		}catch(Exception $e){
			$this->setError($e->getMessage());
			return false;
		}

		if($wpdb->last_error){
			$this->setError($wpdb->last_error);
			return false;
		}
		return true;
		
	}
	
	function setError($error,$code=''){
		$this->error_msg = $error;
		$this->error_code = $code;
	}
	
	function getError(){
		return $this->error_msg;
	}
	
	
	function load($array){
		if(empty($array)){
			return false;
		}
		global $wpdb;
		$sql = 'SELECT * from '.$this->_tbl.' WHERE ';
		
		if(is_array($array) || is_object($array)){
			$where = array();
			foreach($array as $key=>$value){
				$where[] = "{$key} = ".$this->quote($value);
			}
			$sql .= implode(' AND ', $where);
		}else{
			$sql .= reset($this->_key).' = '.$array;
		}
		$result = $wpdb->get_row($sql);
		$this->bind($result);
		return !empty($result);
	}
	
	
	
	function batch_save($datas){
		if(!is_array($datas) || count($datas) == 0){
			return true;
		}
		$table_fields = $this->get_fields();		
		$table_fields = array_map(function($a){return $a->Field;}, $table_fields);
		
		$fields = array();
		foreach(reset($datas) as $key=>$value){
			if(in_array($key, $table_fields)){
				$fields[]=$key;
			}
		}
		//debug($fields);die;
		$sql = 'INSERT INTO '.($this->_tbl).' ('.implode(',',$fields).') VALUES ';
		foreach($datas as $data){
			$data = (array)$data;
			$sql .= "(".$this->render_values($data,$fields)."),";
		}
		
		$sql = trim($sql,',')." ON DUPLICATE KEY UPDATE ";
		foreach($fields as $field){
			$sql .= "$field = VALUES($field),";
		}
		$sql = trim($sql,',').';';
		$datas=null;unset($datas);
 		return $this->run_query($sql);
		//$this->setQuery($sql);
// 		echo $sql;die;
		//$sql=null;unset($sql);
		
		//return $this->execute();
		
	}
	
	private function run_query($query){
// 		echo $query;
		global $wpdb;
		return $wpdb->query($query);
		// $con = $this->get_connection();
		// $check = mysqli_query($query);
		// mysqli_close($con);
		// return $check;
	}
	
	private function get_connection(){
		if(!self::$connection){
			$conf = JFactory::getConfig();
			self::$connection = mysqli_connect($conf->get('host'), $conf->get('user') , $conf->get('password'), $conf->get('db'));
			if (!self::$connection) {
				die('Not connected : ' . mysql_error());
			}
		}
		return self::$connection;
		$db =JFactory::getDbo();
	}
	
	private function render_values($data,$key){
		$sql = '';
		foreach($key as $v){
			if(isset($data[$v])){
				if(is_array($data[$v]) || is_object($data[$v])){
					$sql .=  ','.$this->quote(json_encode($data[$v]));
				}else{
					$sql .=  ','.$this->quote($data[$v]);
				}
				
			}else{
				$sql .=  ',""';
			}
		}
		return trim($sql,',');
	}
	
	public function quote($value){
		$value = str_replace("'","\'","{$value}");
		return "'{$value}'";
	}
	
	public function get_table_name(){
		global $wpdb;
		return str_replace('#__', $wpdb->prefix, $this->_tbl);
	}
	
	public function get_fields()
	{	
		if ($this->jbcache === null)
		{
			// Lookup the fields for this table only once.
			$name   = $this->_tbl;
// 			debug($name);die;
			global $wpdb;
			$fields = $wpdb->get_results("SHOW COLUMNS FROM {$name};");
			if (empty($fields))
			{
				throw new UnexpectedValueException(sprintf('No columns found for %s table', $name));
			}
	
			$this->jbcache = $fields;
		}
	
		return $this->jbcache;
	}
	
	function getProperties(){
		$table_fields = $this->get_fields();
		$table_fields = array_map(function($a){return $a->Field;}, $table_fields);
		$fields = array();
		foreach($table_fields as $key){
			$fields[$key] = isset($this->$key) ? $this->$key : '';
		}
		return $fields;
	}
	
	function setState($filter,$value){
		$this->state->set($filter,$value);
	}
	
	function getState($filter,$default_value = ''){
		return $this->state->get($filter,$default_value);
	}

	function check(){
		return true;
	}
	
	
}