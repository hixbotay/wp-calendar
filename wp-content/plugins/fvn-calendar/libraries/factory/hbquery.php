<?php
class JBQuery{
	var $query;
	var $data;
	
	public function __construct(){
		$this->data = array(
		'select'	=> '',
		'from'		=> '',
		'join'		=> array(),
		'where'		=> '');
		$this->query = '';
	}
	
	public function select($string){
		$this->data['select'] .= $string;
		return $this->data['select'];
	}
	
	public function from($string){
		$this->data['from'] .= $string;
		return $this->data['from'];
	}
	/**
	 * Join query
	 * @param unknown_type $type type of join
	 * @param unknown_type $sql
	 */
	public function join($type, $sql){
		$this->data['join'][$type] .= $sql;
	}
	
	public function getQuery(){
		return $this->toString();
	}
	
	private function toString(){
		
	}
}