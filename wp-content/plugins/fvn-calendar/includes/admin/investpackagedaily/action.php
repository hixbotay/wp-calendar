<?php

class FvnActionInvestPackage extends FvnAction
{
	function getInputData()
	{
		$data = parent::getInputData();
		$data['rate'] = json_encode($data['rate']);
		$data['description'] = $_POST['data']['description'];
		return $data;
	}
	public function hookAfterSave($data){
		if($data['type']==FvnParamVideoCallType::DAY['value']){
			$model = new FvnModel('#__fvn_invest_package_daily',['invest_package_id','date']);
		
			$result=  $model->save([
				'invest_package_id'=>$this->model->id,
				'date'=>date('Y-m-d'),
				'rate'=>reset(json_decode($data['rate']))
			]);
			return $result;
		}
		return true;
	}
}

