<?php
class JBDatabase extends wpdb{
	
	function __construct(){
		$this->query = new JBQuery();
		return parent::__construct();
	}
	
}