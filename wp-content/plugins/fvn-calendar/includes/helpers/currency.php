<?php

defined( 'ABSPATH' ) or die( 'Direct Access to this location is not allowed.' );
/**
 * @package 	FVN-extension
 * @author 		Vuong Anh Duong
 * @link 		http://http://woafun.com/
 * @copyright 	Copyright (C) 2011 - 2012 Vuong Anh Duong
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: currency.php 16 2012-06-26 12:45:19Z quannv $
 **/
class FvnCurrencyHelper{

	public static function formatprice($value,$config=null){
		if(empty($config))
			$config= HBFactory::getConfig();
		
		$thousand	= $config->currency_seperator;
		$decimalpoint = $config->currency_decimalpoint;
		$symbol= $config->currency_symbol;
		$currency_type = $config->currency_display;
		
		$newval = self::formatNumberWConfig($value, $decimalpoint, $thousand);
		
		
		return self::disPlayCurrency($newval, $symbol, $currency_type);		
	}
	
	private static function formatNumberWConfig($value,$decimalpoint,$thousand){
		if ($value) {
			$value = number_format($value, $decimalpoint, '.',$thousand);
			if($decimalpoint > 0){
				
				
				for($i=1;$i<=$decimalpoint;$i++){
					$length = strlen($value);
					if($value[($length-1)] == '0'){
						$value= substr($value, 0, (strlen($value) - 1));
					}else{
						break;
					}
				}
				$value=trim($value,'.');
				
			}
		}
		else
			$value = 0;
		return $value;
	}
	
	private static function disPlayCurrency($value,$symbol,$currency_type){
		
		switch ($currency_type){
			case 0:
				// 0 = '00Symb'
				$value=$value.$symbol;
				break;
			case 2:
				// 2 = 'Symb00'
				$value=$symbol.$value;
				break;
			case 3:
				// 3 = 'Symb 00'
				$value=$symbol.' '.$value;
				break;
			case 1:
			default :
				// 1 = '00 Symb'
				$value=$value.' '.$symbol;
				break;
		}
		return $value;
	}
	
public static function formatpricewithVat($value,$vat = ''){	
		$config = HBFactory::getConfig();
		if(!empty($vat))
			$vat = $config->vat;
		
		$value += $value*($vat)/100;
		
		return self::formatprice($value,$config);
		
	}
	
	public static function formatNumber($value){
		$config = HBFactory::getConfig();
		$thousand=$config->currency_seperator;
		$decimalpoint = $config->currency_decimalpoint;
		
		return number_format($value, $decimalpoint, '.',$thousand);
			
	}
	public static function displayPrice($value,$currency = null){
		
		return self::formatprice($value);
	}
	public static function convertPrice($price,$currency_id,$currency_convert_id){
		AImporter::model('currency');
		$cmodel = new BookProModelCurrency();
		$cmodel->setId($currency_id);
		$currency = $cmodel->getObject();
		
		$cmodel = new BookProModelCurrency();
		$cmodel->setId($currency_convert_id);
		$currency_convert = $cmodel->getObject();
		
		if ($currency_id != $currency_convert_id && $currency_convert_id != NULL) {
			$price = ($price*$currency->currency_exchange_rate)/$currency_convert->currency_exchange_rate;
			return $price;
		}else{
			return $price;
		}
	}
	/**
	 * 
	 * @return unknown
	 */
	static function getMainCurrency()
	{
		static $instance;
		if (empty($instance)) {
			AImporter::model('currency');
			$model=new BookProModelCurrency();
			$instance = $model->getDefault();
		}
		return $instance;
	}
	/**
	 * 
	 * @param float $value
	 * @param int $currency_id
	 * @return Ambigous <string, string, mixed, multitype:>
	 */
	public static function formatPriceM($value,$currency_id){
		
		AImporter::model('currency');
		$cmodel = new BookProModelCurrency();
		$cmodel->setId($currency_id);
		$currency = $cmodel->getObject();
		if($currency->currency_exchange_rate==1){
			
		}else{
			$convetCurr=self::getMainCurrency();
			$value=self::convertPrice($value, $convetCurr->id, $currency_id);
		}
		$symbol = $currency->currency_symbol;
		
		$value = $value;
		if ($value) {
			$value = number_format($value, 2, '.',$currency->thousand_currency);
			$length = JString::strlen($value);
			if (JString::substr($value, $length - 2) == '00')
				$newval= JString::substr($value, 0, $length - 3);
			elseif (JString::substr($value, $length - 1) == '0')
			$newval= JString::substr($value, 0, $length - 1);
			else
				$newval=$value;
		}
		
		switch ($currency->currency_display){
			case 0:
				// 0 = '00Symb'
				$newval=$newval.$symbol;
				break;
			case 2:
				// 2 = 'Symb00'
				$newval=$symbol.$newval;
				break;
			case 3:
				// 3 = 'Symb 00'
				$newval=$symbol.' '.$newval;
				break;
			case 1:
			default :
				// 1 = '00 Symb'
				$newval=$newval.' '.$symbol;
				break;
		}
		return $value?$newval:JText::_('N/A');
	}

    public static function paymentStatus($method, $pay_status){
        if ($method === 'offline'){
            return "Payment in cash";
        }
        if ($pay_status === 'PENDING'){
            return "Unpaid";
        }else{
            return "Paid";
        }
    }

}