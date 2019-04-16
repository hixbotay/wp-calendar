<?php
class FvnActionOrders extends FvnAction{
	function getInputData(){
		$data = parent::getInputData();
		if($data['id']){
			$this->model->load($data['id']);
			$params = json_decode($this->model->params,true);
			$params['image_result'] = $data['params']['image_result'];
			$data['params'] =json_encode($params);
		}
		return $data;
	}
	public function save(){
		FvnImporter::helper('date');
		$passengers = $this->input->get('passenger');
		foreach($passengers as &$p){
			$p['birthday'] = FvnDateHelper::createFromFormatYmd($p['birthday']);
		}
		//debug($passengers);die;
		$model = new FvnModel('#__fvn_passengers','id');
		if(!$model->batch_save($passengers)){
			hb_enqueue_message(__('Save passengers error'),'error');
		}
		parent::save();
		return;
	}
	
	function exportCsv(){
		FvnImporter::helper('csvfilehelper');
		$this->loadModel();
		$items = $this->model->getItems();
		$loc=array('order_number','total','pay_status','notes','order_status','firstname','lastname','email','mobile');
		$result=[];
		global $wpdb;
		$query = HBFactory::getQuery();
		foreach($items as $i=>$item){
			
			
			foreach($item as $k=>$v){				
				if(in_array($k,$loc)){
					$result[$i][$k]=$v;
				}
			}
			$query->clear();
			$passports = $wpdb->get_results($query->select('passport')->from('#__fvn_passengers')
					->where('order_id='.$item->id)->__toString());
				
			$result[$i]['passport'] = '';
			foreach($passports as $p){
				$result[$i]['passport'] .= ' '.$p->passport;
			}
			
		}
		CsvFileHelper::download($result, 'order.csv');
		exit;
	}
	
	
}