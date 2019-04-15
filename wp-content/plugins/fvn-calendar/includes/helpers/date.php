<?php
/**
 * @package 	FVN-extension
 * @author 		Vuong Anh Duong
 * @link 		http://http://woafun.com/
 * @copyright 	Copyright (C) 2011 - 2012 Vuong Anh Duong
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: airport.php 66 2012-07-31 23:46:01Z quannv $
 **/
defined( 'ABSPATH' ) or die( 'Direct Access to this location is not allowed.' );
class HBDateHelper {	
	static $format;
		
	static function dayofweek(){
		$days = array(
				0 => 'Sun',
				1 => 'Mon',
				2 => 'Tue',
				3 => 'Wed',
				4 => 'Thu',
				5 => 'Fri',
				6 => 'Sat'
				);
		return $days;
	
	}
	
	//use in frontend where display ajax flight list
	public static function getArrayTabDate($date){
		$tzoffset = false;//get_option('gmt_offset');
		$date_arr=array();
		
		$currendate= HBFactory::getDate($date,$tzoffset);	
		$nowdate = HBFactory::getDate('now',$tzoffset);
		if($currendate < $nowdate){
			$currendate = $nowdate;			
		}	
	
//		if($roundtrip){
//			$nowdate->modify('+2 day');
//		}	

		$days = $currendate->diff($nowdate);
		
		$range = $days->days + $days->invert;

		if ($range >=3) {
			$int_start = -3;
			$int_end = 4;
		}

		if ($range < 3 && $range >0) {
			$int_start = -$range;

			$int_end = 7 - $range;

		}

		if ($range == 0) {
			$int_start = 0;
			$int_end = 7;
		}
		
		for ($i = $int_start; $i < $int_end; $i++) {

			$sdate=HBFactory::getDate($date,$tzoffset);

			if($i<0) {
				$sdate->sub(new DateInterval('P'.abs($i).'D'));
			}else{
				$sdate->add(new DateInterval('P'.abs($i).'D'));
			}

			$date_arr[]=$sdate;
		}
		
		return $date_arr;
	}
	
	private static function getFormat(){
		if(!self::$format){
			$config = HBFactory::getConfig();
			self::$format = $config->date_format_short;
		}
		return self::$format;
	}
    
    static function getConvertDateFormat($type='P')
	{
		$type = strtoupper($type);
		$php_format = self::getFormat();
		if($type=="P"){
			return $php_format;
		}else{

			// $type is param for PHP and Mooltoos and Javascript
			if($type=="M"){
				$SYMBOLS_MATCHING = array(
				// Day
	        'd' => '%d',
	        'D' => '%D',
	        'j' => '%j',
	        'l' => '%l',
	        'N' => '%N',
	        'S' => '%S',
	        'w' => '%w',
	        'z' => '%z',
				// Week
	        'W' => '%W',
				// Month
	        'F' => '%F',
	        'm' => '%m',
	        'M' => '%M',
	        'n' => '%n',
	        't' => '%t',
				// Year
	        'L' => '%L',
	        'o' => '%o',
	        'Y' => '%Y',
	        'y' => '%y',
				// Time
	        'a' => '%a',
	        'A' => '%A',
	        'B' => '%B',
	        'g' => '%g',
	        'G' => '%G',
	        'h' => '%h',
	        'H' => '%H',
	        'i' => '%i',
	        's' => '%s',
	        'u' => '%u'
	        );
			}elseif($type=="J"){
				$SYMBOLS_MATCHING = array(
				// Day
	        'd' => 'dd',
	        'D' => 'D',
	        'j' => 'd',
	        'l' => 'DD',
	        'N' => '',
	        'S' => '',
	        'w' => '',
	        'z' => 'o',
				// Week
	        'W' => '',
				// Month
	        'F' => 'MM',
	        'm' => 'mm',
	        'M' => 'M',
	        'n' => 'm',
	        't' => '',
				// Year
	        'L' => '',
	        'o' => '',
	        'Y' => 'yy',
	        'y' => 'y',
				// Time
	        'a' => '',
	        'A' => '',
	        'B' => '',
	        'g' => '',
	        'G' => '',
	        'h' => '',
	        'H' => '',
	        'i' => '',
	        's' => '',
	        'u' => ''
	        );
			}
			$jqueryui_format = "";
			$escaping = false;
			for($i = 0; $i < strlen($php_format); $i++)
			{
				$char = $php_format[$i];
				if($char === '\\') // PHP date format escaping character
				{
					$i++;
					if($escaping) $jqueryui_format .= $php_format[$i];
					else $jqueryui_format .= '\'' . $php_format[$i];
					$escaping = true;
				}
				else
				{
					if($escaping) { $jqueryui_format .= "'"; $escaping = false; }
					if(isset($SYMBOLS_MATCHING[$char]))
					$jqueryui_format .= $SYMBOLS_MATCHING[$char];
					else
					$jqueryui_format .= $char;
				}
			}
			return $jqueryui_format;
		}
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $string_date
	 */
	static function createFromFormat($string_date){
		
		if(empty($string_date))
			return '';
		$php_format = self::getFormat();
		$date = DateTime::createFromFormat($php_format, $string_date);
		return  $date;
		
	}
	
	static function createFromFormatYmd($string_date){		
		if(empty($string_date))
			return '';
		$php_format = self::getFormat();
		$date = DateTime::createFromFormat($php_format, $string_date);
		if($date){
			return  $date->format('Y-m-d');
		}
		return false;
		
	
	}
	
	static function formatHours($string_hours){
		$pattern = '/([0-1][0-9]|2[0-3])[:]([0-5][0-9])/';
		if(!preg_match($pattern, $string_hours)){
			return $string_hours;
		}
		$params = HBFactory::getConfig();
		if($params->formatHours == 12){
			$hour = explode(':', $string_hours);
			$compare = $hour[0] - 12;
			if($compare > 0){				
				return (int)($hour[0] - 12).':'.$hour[1].' PM';				
			}	
			else if($compare == 0){
				return '12:'.$hour[1].' PM';
			}
			else
				return (int)($hour[0]).':'.$hour[1].' AM';
			
		}
		else
			return $string_hours;
	}
	
	static function getDate($date_str=null,$time_zone=''){
		$date = new DateTime($date_str);
		return $date;
	}
	
	static function display($date,$format=''){
		
		if(substr($date, 0,4)=='0000' || empty($date)){
			return '';
		}
		if(!$format){
			$format = self::getConvertDateFormat();
		}
		return self::getDate($date)->format($format);
	}
	
}