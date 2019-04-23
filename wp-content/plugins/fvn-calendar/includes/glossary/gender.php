<?php

class FvnParamGender{
    const MALE = ['display'=>'Nam', 'value'=>'1'];
    const FEMALE = ['display'=>'Nữ', 'value'=>'2'];
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