<?php
class FvnMailHelper{
	public $order_complex;
	public $from_name;
	public $from_email;
	
	function __construct($order_id){
		FvnImporter::model('orders');
		FvnImporter::helper('currency');
		$model = new FvnModelOrders();
		$this->order_complex = $model->getComplexItem($order_id);		
		$config = HBFactory::getConfig();
// 		debug($config);
		$this->from_name = $config->get('company_name','DiemTuaViet.net');
		$this->from_email = $config->get('company_email','info.decci@gmail.com');
	}
	
	static function getOrderKey(){
		return array('order_status','pay_status','id','email','phone','total','order_number','firstname','lastname');
	}
	function sendCustomer(){
		$template = get_option('fvn_mail_customer');
		$template = json_decode($template);
		$template->description = nl2br($template->description);
		$template->description = $this->fillOrder($template->description);
// 		echo $template->description;die;
		return FvnHelper::sendMail($this->order_complex->user->email,$template->title, $template->description,
				'','',
				$template->from_name? $template->from_name:$this->from_name,
				$template->from_email?$template->from_email:$this->from_email);
	}
	
	function sendPayment(){		
		$template = get_option('fvn_mail_payment');
		$template = json_decode($template);
		$template->description = nl2br($template->description);
		$template->description = $this->fillOrder($template->description);
		// 		echo $template->description;die;
		return FvnHelper::sendMail($this->order_complex->user->email,$template->title, $template->description,
				'','',
				$template->from_name? $template->from_name:$this->from_name,
				$template->from_email?$template->from_email:$this->from_email);
	}

	function sendDrawRequestAdmin(){		
		$template = get_option('fvn_mail_new_drawrequest');
		$template = json_decode($template);
		$template->description = nl2br($template->description);
		$template->description = $this->fillOrder($template->description);
		// 		echo $template->description;die;
		return FvnHelper::sendMail($template->to_email,$template->title, $template->description,
				'','',
				$template->from_name? $template->from_name:$this->from_name,
				$template->from_email?$template->from_email:$this->from_email);
	}

	function sendApproveDrawRequest(){		
		$template = get_option('fvn_mail_approve_drawrequest');
		$template = json_decode($template);
		$template->description = nl2br($template->description);
		$template->description = $this->fillOrder($template->description);
		// 		echo $template->description;die;
		return FvnHelper::sendMail($this->order_complex->user->email,$template->title, $template->description,
				'','',
				$template->from_name? $template->from_name:$this->from_name,
				$template->from_email?$template->from_email:$this->from_email);
	}

	function sendRejectDrawRequest(){		
		$template = get_option('fvn_mail_reject_drawrequest');
		$template = json_decode($template);
		$template->description = nl2br($template->description);
		$template->description = $this->fillOrder($template->description);
		// 		echo $template->description;die;
		return FvnHelper::sendMail($this->order_complex->user->email,$template->title, $template->description,
				'','',
				$template->from_name? $template->from_name:$this->from_name,
				$template->from_email?$template->from_email:$this->from_email);
	}
	
	

	function sendAdmin(){
		$template = get_option('fvn_mail_admin');
		$template = json_decode($template);
		$template->description = nl2br($template->description);
		$template->description = $this->fillOrder($template->description);
// 		echo $template->description;die;

// debug($template);
		return FvnHelper::sendMail($template->to_email,$template->title, $template->description,
				'','',
				$template->from_name? $template->from_name:$this->from_name,
				$template->from_email?$template->from_email:$this->from_email);
	}
	public function fillOrder($input){

		$input = str_replace('{email}', $this->order_complex->user->email, $input);
		$input = str_replace('{user_name}', $this->order_complex->user->display_name, $input);
		$input = str_replace('{phone}', $this->order_complex->user->phone, $input);
		$input = str_replace('{bank_name}', $this->order_complex->user->bank_name, $input);
		$input = str_replace('{bank_number}', $this->order_complex->user->bank_number, $input);
		$input = str_replace('{package_name}', $this->order_complex->package->name, $input);
		$input = str_replace('{order_days}', FvnDateHelper::getDate($this->order_complex->order->end)->diff(FvnDateHelper::getDate($this->order_complex->order->start))->days, $input);
		$input = str_replace('{order_status}', FvnParamOrderStatus::getDisplay($this->order_complex->order->order_status), $input);
		$input = str_replace('{pay_status}', FvnParamPayStatus::getDisplay($this->order_complex->order->pay_status), $input);

		$fields = self::getOrderKey();
		foreach($fields as $field){
			$input = str_replace('{'.$field.'}', $this->order_complex->order->$field, $input);
		}
		$link = FvnHelper::get_order_link($this->order_complex->order);		
		$input = str_replace('{link}', $link, $input);
		$draw_able = FvnInvestHelper::caculateDrawAble($this->order);
		$input = str_replace('{revenue}', $draw_able['revenue'], $input);
		$input = str_replace('{total_revenue}', $draw_able['total'], $input);
		// $input = str_replace('{order_info}', FvnHelper::renderLayout('email_order_info',$this->order_complex), $input);
		// $input = str_replace('{passengers}', FvnHelper::renderLayout('email_passenger',$this->order_complex), $input);
		// $input = str_replace('{sumarry}', FvnHelper::renderLayout('email_summary',$this->order_complex), $input);
		return $input;
	}
	
	
}