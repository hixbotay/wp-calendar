<?php

class FvnActionTransaction extends FvnAction{	
	function getInputData(){
		$data = parent::getInputData();
		$data[description]=$_POST[data][description];
		$data['params'] = json_encode($data['params']);		
		//debug($data);die;
		return $data;
	}
}