<?php

class FvnParamOrderStatus{
    
    const PENDING = ['display'=>'Đang xử lí', 'value'=>'PENDING'];
	const CONFIRMED = ['display'=>'Xác nhận', 'value'=>'CONFIRMED'];
    const WAITTING_APPROVE = ['display'=>'Chờ phê duyệt', 'value'=>'WAITTING_APPROVE'];
    const CLOSED = ['display'=>'Đã đóng', 'value'=>'CLOSED'];
    const CANCELLED = ['display'=>'Đã hủy', 'value'=>'CANCELLED'];

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