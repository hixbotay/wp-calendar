<?php

class FvnParamInvestType{
    const MONTHLY = ['display'=>'Theo tháng', 'value'=>'M'];
    const YEAR = ['display'=>'Theo năm', 'value'=>'Y'];
    const DAY = ['display'=>'Theo ngày', 'value'=>'D'];

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