<?php
/*
Plugin Name: Hbpayment-onepay
Plugin URI: http://http://hbproweb.com/
Description: Payment plugin that help plugin of Joombooking can connect with online payment gateway
Version: 1.0
Author: Vuong Anh Duong
Author URI: http://example.com
Text Domain: prowp-plugin
License: GPLv2
*/
defined('ABSPATH') or die('Restricted access');

class HBPayment_Onepay
{

	public $_element = 'hbpayment_onepay';
	public $params;
	public $returnUrl;
	public $cancelUrl;
	public $notifyUrl;
	public $config;
	/*
	 * Class Order 
	 */
	public $order;

	function __construct($main_config=array()) {
		$this->config = $main_config;
		$config = get_option($this->_element);
		$this->params = json_decode($config);
		
		$currency = $this->config->main_currency;
		if(empty($currency)){
			$currency = trim($this->getParam('api_currency'));
		}
		$this->currency = $currency;//'USD';
	}
	
	//get config options
	private function getParam($key,$default = null){
		return isset($this->params->$key) ? $this->params->$key : $default;
	}
	
	function formatNumber($total,$currency){
// 		return (int)$total; 
		if($currency=='USD'){
			$reuslt = (int)$total*23000;
		}else{
			$reuslt = (int)$total;
		}
		return $reuslt*100;
	}
	
	function _prePayment( )
	{
		$data = $this->order->getProperties();
		
		$MID = $this->getParam('sandbox',1)? $this->getParam('test_merchant_id','ONEPAY') : $this->getParam('merchant_id');
		$access_code= $this->getParam('sandbox',1)? $this->getParam('test_access_code','D67342C2') : $this->getParam('access_code');
		$SECURE_SECRET =$this->getParam('sandbox',1)?  trim($this->getParam('test_merchant_key','A3EFDFABA8653DF2342E8DAC29B51AF0')) : trim($this->getParam('merchant_key'));
		
// 		debug($access_code);
// 		debug($MID);
// 		debug($SECURE_SECRET);die;
		//get language
		$local='vi';
		$params=array('vpc_Merchant'=>$MID,
				'vpc_AccessCode'=>$access_code,
				'vpc_OrderInfo'=>$data['order_number'],
				'vpc_Amount'=> $this->formatNumber($data['total'],$data['currency']),
				'vpc_ReturnURL'=> $this->return_url,
				//'vpc_Currency' => JComponentHelper::getParams('com_bookpro')->get('main_currency'),
				'vpc_Version'=>'2',
				'vpc_Command'=>'pay',
				'vpc_Locale'=>$local,
				'vpc_MerchTxnRef'=>date('YmdHis').rand(),
				'vpc_TicketNo' => $_SERVER['REMOTE_ADDR'],
				'Title' => $this->getParam('gateway'),
				'vpc_Currency' => 'VND'
		);
// 		debug($params);die;
		
		// add the start of the vpcURL querystring parameters
		$vpcURL = $this->_getPostUrl()."?";
// 		debug($vpcURL);
		$params['AgainLink']=urlencode($_SERVER['HTTP_REFERER']);
		$md5HashData = "";
		ksort ($params);
		
		// set a parameter to show the first pair in the URL
		$appendAmp = 0;
		foreach($params as $key => $value) {
		
			// create the md5 input and URL leaving out any fields that have no value
			if (strlen($value) > 0) {
		
				// this ensures the first paramter of the URL is preceded by the '?' char
				if ($appendAmp == 0) {
					$vpcURL .= urlencode($key) . '=' . urlencode($value);
					$appendAmp = 1;
				} else {
					$vpcURL .= '&' . urlencode($key) . "=" . urlencode($value);
				}
				//$md5HashData .= $value; sử dụng cả tên và giá trị tham số để mã hóa
				if ((strlen($value) > 0) && ((substr($key, 0,4)=="vpc_") || (substr($key,0,5) =="user_"))) {
					$md5HashData .= $key . "=" . $value . "&";
				}
			}
		}
		//xóa ký tự & ở thừa ở cuối chuỗi dữ liệu mã hóa
		$md5HashData = rtrim($md5HashData, "&");
		// Create the secure hash and append it to the Virtual Payment Client Data if
		// the merchant secret has been provided.
		if (strlen($SECURE_SECRET) > 0) {
			//$vpcURL .= "&vpc_SecureHash=" . strtoupper(md5($md5HashData));
			// Thay hàm mã hóa dữ liệu
			$vpcURL .= "&vpc_SecureHash=" . strtoupper(hash_hmac('SHA256', $md5HashData, pack('H*',$SECURE_SECRET)));
		}else{
			return array('status'=>'0','msg'=>'Tai khoan onepay chua duoc cau hinh');
		}
// 		debug($vpcURL);die;
// debug($vpcURL);die;
		
		return array('status'=>'1','url'=>$vpcURL);

	}
	function _getPostUrl($full = true)
	{
	
		if($this->params->international){
			$url = $this->params->sandbox ? 'https://mtf.onepay.vn/vpcpay/vpcpay.op' : 'https://onepay.vn/vpcpay/vpcpay.op';
		}else{
			$url = $this->params->sandbox ? 'https://mtf.onepay.vn/onecomm-pay/vpc.op' : 'https://onepay.vn/onecomm-pay/vpc.op';
		}
	
	
		return $url;
	}
	
	function query_tx($data){
		$input= HBFactory::getInput();
		
		$MID = $this->getParam('sandbox')? $this->getParam('test_merchant_id') : $this->getParam('merchant_id');
		$access_code= $this->getParam('sandbox')? $this->getParam('test_access_code') : $this->getParam('access_code');
		$SECURE_SECRET =$this->getParam('sandbox')?  trim($this->getParam('test_merchant_key')) : trim($this->getParam('merchant_key'));
		
		$params=array('vpc_Merchant'=>$MID,
				'vpc_AccessCode'=>$access_code,
				'vpc_OrderInfo'=>$data['order_number'],
				'vpc_MerchTxnRef'=> $data['vpc_MerchTxnRef'],
				'vpc_Version'=>'1',
				'vpc_Command'=>'queryDR',
				'vpc_User'=>'',
				'vpc_Password'=>''
		);
		// 		debug($params);die;
		
		// add the start of the vpcURL querystring parameters
		$vpcURL = $this->_getQueryUrl()."?";
		// 		debug($vpcURL);
		$params['AgainLink']=urlencode($_SERVER['HTTP_REFERER']);
		$md5HashData = "";
		ksort ($params);
		
		// set a parameter to show the first pair in the URL
		$appendAmp = 0;
		foreach($params as $key => $value) {
		
			// create the md5 input and URL leaving out any fields that have no value
			if (strlen($value) > 0) {
		
				// this ensures the first paramter of the URL is preceded by the '?' char
				if ($appendAmp == 0) {
					$vpcURL .= urlencode($key) . '=' . urlencode($value);
					$appendAmp = 1;
				} else {
					$vpcURL .= '&' . urlencode($key) . "=" . urlencode($value);
				}
				//$md5HashData .= $value; sử dụng cả tên và giá trị tham số để mã hóa
				if ((strlen($value) > 0) && ((substr($key, 0,4)=="vpc_") || (substr($key,0,5) =="user_"))) {
					$md5HashData .= $key . "=" . $value . "&";
				}
			}
		}
		//xóa ký tự & ở thừa ở cuối chuỗi dữ liệu mã hóa
		$md5HashData = rtrim($md5HashData, "&");
		$vpcURL .= "&vpc_SecureHash=" . strtoupper(hash_hmac('SHA256', $md5HashData, pack('H*',$SECURE_SECRET)));
		// debug($vpcURL);
		return FvnHelper::getDataFromApi($vpcURL);
		
	}
	function _getQueryUrl($full = true)
	{
	
		$url = $this->params->sandbox ? 'https://mtf.onepay.vn/onecomm-pay/Vpcdps.op' : 'https://onepay.vn/onecomm-pay/Vpcdps.op';
	
	
		return $url;
	}
	
	
	
	public function _displayMessage(){		
		
		$input= HBFactory::getInput();
		
		$SECURE_SECRET =$this->getParam('sandbox',1)?  trim($this->getParam('test_merchant_key','A3EFDFABA8653DF2342E8DAC29B51AF0')) : trim($this->getParam('merchant_key'));
		
		$vpc_Txn_Secure_Hash = $_GET ["vpc_SecureHash"];
		unset ( $_GET ["vpc_SecureHash"] );
		
		// set a flag to indicate if hash has been validated
		$errorExists = false;
		$txnResponseCode = $input->get('vpc_TxnResponseCode');
		ksort ($_GET);
		
		if (strlen ( $SECURE_SECRET ) > 0 && $_GET ["vpc_TxnResponseCode"] != "7" && $_GET ["vpc_TxnResponseCode"] != "No Value Returned") {
		
			//$stringHashData = $SECURE_SECRET;
			//*****************************khởi tạo chuỗi mã hóa rỗng*****************************
			$stringHashData = "";
		
			// sort all the incoming vpc response fields and leave out any with no value
			foreach ( $_GET as $key => $value ) {
				//        if ($key != "vpc_SecureHash" or strlen($value) > 0) {
				//            $stringHashData .= $value;
				//        }
				//      *****************************chỉ lấy các tham số bắt đầu bằng "vpc_" hoặc "user_" và khác trống và không phải chuỗi hash code trả về*****************************
				if ($key != "vpc_SecureHash" && (strlen($value) > 0) && ((substr($key, 0,4)=="vpc_") || (substr($key,0,5) =="user_"))) {
					$stringHashData .= $key . "=" . $value . "&";
				}
			}
			//  *****************************Xóa dấu & thừa cuối chuỗi dữ liệu*****************************
			$stringHashData = rtrim($stringHashData, "&");
		
		
			//    if (strtoupper ( $vpc_Txn_Secure_Hash ) == strtoupper ( md5 ( $stringHashData ) )) {
			//    *****************************Thay hàm tạo chuỗi mã hóa*****************************
			if (strtoupper ( $vpc_Txn_Secure_Hash ) == strtoupper(hash_hmac('SHA256', $stringHashData, pack('H*',$SECURE_SECRET)))) {
				// Secure Hash validation succeeded, add a data field to be displayed
				// later.
				$hashValidated = "CORRECT";
			} else {
				// Secure Hash validation failed, add a data field to be displayed
				// later.
				$hashValidated = "INVALID HASH";
			}
		} else {
			// Secure Hash was not validated, add a data field to be displayed later.
			$hashValidated = "INVALID HASH";
		}
		
// 		debug($_GET);
// 		debug($txnResponseCode);
// 		debug($hashValidated);die;
		// debug($_REQUEST);
		// debug($hashValidated);
		$this->order->load($input->getInt('order_id'));
		$query = $this->query_tx([
			'order_number' => $this->order->order_number,
			'vpc_MerchTxnRef' => $input->get('vpc_MerchTxnRef')
		]);
		// debug($query);
		$this->order->load($input->getInt('order_id'));
		if($hashValidated=="CORRECT" && $txnResponseCode=="0"){
			
			if($this->order->id){
				if($this->order->pay_status=='SUCCESS'){
					return $this->order;
				}
				$query = $this->query_tx([
					'order_number' => $this->order->order_number,
					'vpc_MerchTxnRef' => $input->get('vpc_MerchTxnRef')
				]);
				debug($query);die;
				$this->order->pay_status = $this->getParam('pay_status','SUCCESS');
				$this->order->order_status = $this->getParam('order_status','CONFIRMED');
				$this->order->tx_id = $input->getString('vpc_MerchTxnRef');
				if($this->order->store()){
					$transaction = new FvnModel('#__fvn_transaction','id');
					$transaction->save(array(
						'user_id' => $this->order->user_id,
						'order_id' => $this->order->id,
						'total' => +$this->order->total,
						'content' => 'Nạp tiền cho '.$order->order_number,
						'created' => current_time( 'mysql' )
					));
				}
				hb_enqueue_message('Thanh toán thành công');
				return $this->order;
			}else{
				HBHelper::write_log('onepay.txt', 'luu order loi '.$input->get('vpc_OrderInfo'));
				hb_enqueue_message('Lỗi','error');
			}
			
		}elseif($txnResponseCode==24){
			hb_enqueue_message('Thông tin thẻ không đúng','error');
		}elseif ($hashValidated=="INVALID HASH" && $txnResponseCode=="0"){
			hb_enqueue_message('Thanh toán đang bị tạm ngừng','error');
		}else {
			hb_enqueue_message('Thanh toán thất bại','error');
		}
		hb_enqueue_message($input->getString('vpc_Message'),'error');
		wp_redirect(FvnHelper::getPaymentLink($this->order));
		exit;
		
		
		return $this->order;
	}
	
	function _processSale()
	{
		$this->write_log('onepay.txt',json_encode($_REQUEST));
	
		$app = JFactory::getApplication();
		
	
		//$app->enqueueMessage($transStatus);
		return $order;
	}
	function null2unknown($data)
	{
		if ($data == "") {
			return "No Value Returned";
		} else {
			return $data;
		}
	}

	/**
	 * Processes the payment form
	 * and returns HTML to be displayed to the user
	 * generally with a success/failed message
	 *
	 * @param $data     array       form post data
	 * @return string   HTML to display
	 */
	function _postPayment( )
	{
		return $this->_displayMessage();
	}

	/**
	 * Prepares variables for the payment form
	 *
	 * @return unknown_type
	 */
	function _renderForm( $data )
	{
		$html = $this->_getLayout('form', $data);
		return $html;
	}
	//render layout 
	private function _getLayout($layout,$data=null){
		ob_start();
		include __DIR__.'/'.$this->_element.'/tmpl/'.$layout.'.php';		
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	
	private function _autoload(){
// 		require_once (JPATH_ROOT.'/plugins/bookpro/payment_cash/lib/vendor/autoload.php');
	}
	
}
