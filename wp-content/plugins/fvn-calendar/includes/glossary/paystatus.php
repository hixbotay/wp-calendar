<?php

class FvnParamPayStatus{
    const SUCCESS = ['display'=>'Đã thanh toán', 'value'=>'SUCCESS'];
    const PENDING = ['display'=>'Chưa thanh toán', 'value'=>'PENDING'];
    const WAITTING_APPROVE = ['display'=>'Chờ phê duyệt', 'value'=>'WAITTING_APPROVE'];
    const CLOSED = ['display'=>'Đã trả lại', 'value'=>'REFUNED'];

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