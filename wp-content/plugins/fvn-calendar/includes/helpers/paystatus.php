<?php
/**
 * Payment status.
 *
 * @package FVN-extension
 * @author Vuong Anh Duong
 * @link http://http://woafun.com/
 * @copyright Copyright (C) 2011 - 2012 Vuong Anh Duong
 * @version $Id$
 */

defined('ABSPATH') or die('Restricted access');
class PayStatus {

	static $PENDING = null;
	static $SUCCESS = null;
	static $DEPOSIT = null;
	static $REFUND = null;
	public $value = null;

	public static $map;
	private $key=null;
	public $text=null;

	public function __construct($value) {
		$this->value = $value;
		$this->text= __(strtoupper($this->value));
	}
	static function format($status){
		return __(strtoupper($status));
	}
	public function getText() {
		return __(strtoupper($this->value));
	}
	

	public static function init () {
		self::$PENDING  = new PayStatus("PENDING");
		self::$SUCCESS = new PayStatus("SUCCESS");
		self::$REFUND = new PayStatus('REFUND');
		self::$map = array (self::$PENDING,self::$SUCCESS);
		
		 
	}
	
	static function getHtmlList($name,$attribute,$select,$id=null,$blank_text='') {
		self::init();
		return FvnHtml::select(FvnParamPayStatus::getAll(), $name,$attribute ,'value', 'display', $select,$id,$blank_text);
	}

	public static function get($element) {
		if($element == null)
			return null;
		return self::$map[$element];
	}

	public function getValue() {
		return $this->value;
	}
	
	public function __getKey() {
		return $this->value;
	}
	public function __getText() {
		return $this->value;
	}
	public function __setKey($key){
		$this->key=$key;
	}
	
	public function equals(PayStatus $element) {
		return $element->getValue() == $this->getValue();
	}

	public function __toString () {
		return $this->value;
	}
}