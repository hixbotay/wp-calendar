<?php
/**
 * @package 	FVN-extension
 * @author 		Vuong Anh Duong
 * @link 		http://freelancerviet.net
 * @copyright 	Copyright (C) 2011 - 2012 Vuong Anh Duong
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id$
 **/
defined('ABSPATH') or die('Restricted access');

class HbTable
{
	public $jbcache = null;
	static $connection;
	protected $_tbl;
	protected $_tbl_keys;
	public $_db;

	function __construct($table_name, $primary_key)
	{
		global $wpdb;
		if(is_string($primary_key)){
			$primary_key = array($primary_key);
		}
		$this->_db = $wpdb;
		$this->_tbl = str_replace('#__', $wpdb->prefix, $table_name);
		$this->_tbl_keys = $primary_key;
		
	}
	
	public function bind($data){		
		$key = $this->getFields();
// 		debug($key);die;
		foreach($key as $k){
			$this->$k = $data[$k];
		}
	}
	
	public function getProperties(){
		$key = $this->getFields();
		$result = array();
		foreach($key as $k){
			$result[$k] = $this->$k;
		}
		return $result;
	}
	
	function save($data, $orderingFilter = '', $ignore = ''){
		$data = (array)$data;
		$this->bind($data, null);
		
		$query = "SELECT COUNT(1) FROM ".$this->_tbl." WHERE ";
		foreach($this->_tbl_keys as $key){
			if(isset($data[$key]) && $data[$key]){
				$key_value = $this->_db->esc_like($data[$key]);
			}else{
				$key_value = 0;
			}
			$query .= "$key = ". $key_value;
		}		
// 		echo $query;
		$count = $this->_db->get_var($query);
// 		debug($count);die;
		$data = $this->getProperties();
		if($count){
			$where = array();
			foreach($this->_tbl_keys as $key){
				$where[$key] = $data[$key];
			}
			$check = $this->_db->update($this->_tbl, $data, $where);		
		}else{
			debug($data);
			foreach($this->_tbl_keys as $key){
				if(empty($data[$key])){
					unset($data[$key]);	
				}					
			}
			debug($data);
			$check = $this->_db->insert($this->_tbl, $data);
			//@TODO thay id bang primary key
			$this->id = $this->_db->insert_id;
			debug($check);
		}
		return $check;
	}
	
	function batch_save($datas){
		if(!is_array($datas) || count($datas) < 1){
			return true;
		}
		$table_fields = $this->getFields();
		$table_fields = array_map(function($a){return $a->Field;}, $table_fields);
		$fields = array();
		foreach($datas[0] as $key=>$value){
			if(in_array($key, $table_fields)){
				$fields[]=$key;
			}
		}
		
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
// 		return $this->run_query($sql);
		$this->_db->query($sql);
		// echo $sql;die;
		$sql=null;unset($sql);
		
		return $this->_db->execute();
		
	}
	
	private function run_query($query){
// 		echo $query;
		$con = $this->get_connection();
		$check = mysqli_query($query);
		mysqli_close($con);
		return $check;
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
					$sql .=  ','.$this->_db->quote(json_encode($data[$v]));
				}else{
					$sql .=  ','.$this->_db->quote($data[$v]);
				}
				
			}else{
				$sql .=  ',""';
			}
		}
		return trim($sql,',');
	}
	
	
	public function getFields()
	{	
		if ($this->jbcache === null)
		{
			// Lookup the fields for this table only once.
			$name   = $this->_tbl;
			$fields = $this->_db->get_col("SELECT `COLUMN_NAME` 
				FROM `INFORMATION_SCHEMA`.`COLUMNS` 
				WHERE `TABLE_NAME`='{$this->_tbl}'");
	
			if (empty($fields))
			{
				throw new UnexpectedValueException(sprintf('No columns found for %s table', $name));
			}
	
			$this->jbcache = $fields;
		}
	
		return $this->jbcache;
	}
	
	
	function insert($data){
		$this->bind($data, '');
		return $this->_db->insert($this->_tbl, $this->getProperties());
	}
}