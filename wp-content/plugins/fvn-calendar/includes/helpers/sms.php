<?php
/**
* @package 	FVN-extension
* @author 		Vuong Anh Duong
* @link 		http://http://woafun.com/
* @copyright 	Copyright (C) 2011 - 2012 Vuong Anh Duong
* @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* @version 	$Id: bookpro.php 80 2012-08-10 09:25:35Z quannv $
**/

defined('ABSPATH') or die('Restricted access');
AImporter::helper('date');
class SMSHelper
{
	var $gateway;
	var $username;
	var $password;

 public function __construct() {
 	
 	$this->gateway="http://123.30.17.109/smsws/services/SendMT?wsdl";
 	$this->username='quannv';
 	$this->password='111111';
 } 
 public function createSMSFromOrder($order_id){
 	
 	$model= new BookProModelOrder();
 	$model->setId($order_id);
 	$order= $model->getByOrderNumber($order['order_number']);
 
 	
 	$smsModel=new BookProModelSms();
 	$sms=array('to'=>$order->mobile);
 	//$content=
 
 }
  
   
}

?>