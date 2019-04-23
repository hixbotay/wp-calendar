<?php

class FvnParamVideoCallType{
    const ZALO = ['display'=>'Zalo', 'value'=>'1'];
    const FACEBOOK = ['display'=>'Facebook', 'value'=>'2'];
    const SKYPE = ['display'=>'Skype', 'value'=>'3'];
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