<?php

class FvnParamOrderStatus{
    
    const PENDING = ['display'=>'Đang xử lí', 'value'=>'1'];
	const CONFIRMED = ['display'=>'Đã xác nhận', 'value'=>'2'];
    const WAITTING_APPROVE = ['display'=>'Chờ phê duyệt', 'value'=>'3'];
    const CLOSED = ['display'=>'Đã đóng', 'value'=>'4'];
    const CANCELLED = ['display'=>'Đã hủy', 'value'=>'5'];

    public static function getAll() {
        $oClass = new \ReflectionClass(__CLASS__);
        return $oClass->getConstants();
    }
    public static function getDisplay($value) {
        if (isset($value)){
            $oClass = new \ReflectionClass(__CLASS__);
            $constants = $oClass->getConstants();
            foreach ($constants as $item) {
                if ($item['value'] == $value) return $item['display'];
            }
        }
        return false;
    }
}