<?php
/*
Plugin Name: Jbpayment-cash
Plugin URI: http://http://woafun.com/
Description: Payment plugin that help plugin of Joombooking can connect with online payment gateway
Version: 1.0
Author: Vuong Anh Duong
Author URI: http://example.com
Text Domain: prowp-plugin
License: GPLv2
*/
defined('ABSPATH') or die('Restricted access');

class HbPayment_Cash
{

	public $_element = 'hbpayment_cash';
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
	
	/*
	 * Get value from REQUEST
	 */
	private function getInput($key,$default = null){
		if (isset($_REQUEST[$key])){
			return $_REQUEST[$key];
		}
		$key = 'amp;'.$key;
	
		if (isset($_GET[$key])){
			return $_GET[$key];
		}
		return $default;
	}
	
	
	function _prePayment( )
	{
		$this->order->pay_method = 'cash';
		// $this->order->order_status = FvnParamOrderStatus::CONFIRMED['value'];
		$result = array();
		if($this->order->store()){			
			$result['status']=1;
			$result['url'] = site_url('index.php?hbaction=payment&task=confirm&order_id='.$this->order->id.'&method=cash&token='.$this->order->order_number);
			hb_enqueue_message(__('Booking success'));
		}else{
			$result['status']=0;
			$result['code']='404';
			$result['msg']=__('Save failed');
		}
		
		return $result;

	}
	
	
	public function _displayMessage(){		
		
		return $this->order;
	}

	/**
	 * Processes the payment form
	 * and returns HTML to be displayed to the user
	 * generally with a success/failed message
	 *
	 * @param $data     array       form post data
	 * @return string   HTML to display
	 */
	function _postPayment( $data = array() )
	{
		return $this->order;
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
	public function _getLayout($layout,$data=null){
		ob_start();
		include __DIR__.'/tmpl/'.$layout.'.php';		
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	
	private function _autoload(){
// 		require_once (JPATH_ROOT.'/plugins/bookpro/payment_cash/lib/vendor/autoload.php');
	}
	
}
