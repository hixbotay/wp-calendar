<?php
/**
 * @package 	FVN-extension
 * @author 		Vuong Anh Duong
 * @link 		http://http://woafun.com/
 * @copyright 	Copyright (C) 2011 - 2012 Vuong Anh Duong
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: airport.php 66 2012-07-31 23:46:01Z quannv $
 **/


defined('ABSPATH') or die('Restricted access');

class OrderHelper {
	
	/**
	 * Get total revenue of orders
	 * @param $start: booking date 
	 * @param $end: booking date
	 * @param String $type: application type 
	 */
	static function getTotal($start,$end,$type=null){
		$db = JFactory::getDbo();
		
		$where=array();
		if($start){
			$where[]="created >= '".$start."'";
		}
		if($end) {
			$where[]="created <= '".$end."'";
		}
		if($type) {
			$where[]="LOWER(type) LIKE ".$db->quote('%' . JString::strtolower($type) . '%');
		}
		
		$query = "SELECT sum(total) FROM #__bookpro_orders";
		
		$query.=" WHERE ".implode(" AND ", $where);
		$db->setQuery($query);
		return $db->loadResult();
	
	}
	
	
}