<?php

class FvnActionCountry extends FvnAction{	
	
	
	function getInputData(){
		$data = parent::getInputData();
		$data['params'] = json_encode($data['params']);
		
		return $data;
	}
	
}